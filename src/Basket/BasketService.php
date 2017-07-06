<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\Basket;

use GuzzleHttp\Exception\ClientException;
use Wizaplace\AbstractService;
use Wizaplace\Authentication\AuthenticationRequired;
use Wizaplace\Basket\Exception\BadQuantity;
use Wizaplace\Basket\Exception\CouponAlreadyPresent;
use Wizaplace\Basket\Exception\CouponNotInTheBasket;
use Wizaplace\Exception\NotFound;
use Wizaplace\Exception\SomeParametersAreInvalid;

/**
 * This service helps creating orders through a basket.
 *
 * Example:
 *
 *     // Create a basket and add products
 *     $basketId = $basketService->create();
 *     $basketService->addProductToBasket($basketId, <product ID>, 2);
 *
 *     // Select a payment method
 *     $availablePayments = $basketService->getPayments();
 *     $selectedPaymentId = <let the user select the payment from the available payments>
 *
 *     // Turn the basket into an order
 *     $redirectUrl = <URL of the confirmation page>
 *     $paymentInfo = $basketService->checkout($basketId, $selectedPaymentId, true, $redirectUrl);
 *
 *     // The order is now created, it needs to be paid
 *     // Redirect the user to the payment provider's form : $paymentInfo->getRedirectUrl();
 */
class BasketService extends AbstractService
{
    /**
     * Create a new basket.
     *
     * The basket will *not* be associated to the current user. Basket are disconnected from users.
     * If you want to keep the basket, store it (or store the ID) in the user's session.
     *
     * @return string The ID of the created basket.
     */
    public function create(): string
    {
        return (string) $this->client->post("basket");
    }

    /**
     * Add a product or a product's declination to a basket.
     *
     * @param string $declinationId ID of the product or the product's declination to add to the basket.
     *                              Be aware that when a product has declinations, you should use the
     *                              declination ID instead of the product ID, else you loose the information
     *                              of which declination was added to the basket.
     *
     * @return int quantity added
     *
     * @throws BadQuantity The quantity is invalid.
     * @throws NotFound The basket could not be found.
     */
    public function addProductToBasket(string $basketId, string $declinationId, int $quantity): int
    {
        if ($quantity < 1) {
            throw new BadQuantity('"quantity" must be greater than 0');
        }

        try {
            $responseData = $this->client->post("basket/{$basketId}/add", [
                'form_params' => [
                    'declinationId' => $declinationId,
                    'quantity' => $quantity,
                ],
            ]);
        } catch (ClientException $ex) {
            $code = $ex->getResponse()->getStatusCode();

            if (404 === $code) {
                throw new NotFound('Basket not found', $ex);
            }

            throw $ex;
        }

        return $responseData['quantity'];
    }

    /**
     * Get a basket
     */
    public function getBasket(string $basketId): Basket
    {
        return new Basket($this->client->get("basket/{$basketId}"));
    }

    /**
     * Remove a product (or a product's declination) from the basket.
     *
     * @throws NotFound The basket could not be found.
     *
     * @see addProductToBasket()
     */
    public function removeProductFromBasket(string $basketId, string $declinationId): void
    {
        if (empty($declinationId)) {
            throw new \InvalidArgumentException('"declinationId" must not be empty');
        }

        try {
            $this->client->post("basket/{$basketId}/remove", [
                'form_params' => [
                    'declinationId' => $declinationId,
                ],
            ]);
        } catch (ClientException $ex) {
            $code = $ex->getResponse()->getStatusCode();

            if (404 === $code) {
                throw new NotFound('Basket not found', $ex);
            }

            throw $ex;
        }
    }

    /**
     * Clear all the products from the basket.
     *
     * @throws NotFound The basket could not be found.
     */
    public function cleanBasket(string $basketId): void
    {
        $basket = $this->getBasket($basketId);
        foreach ($basket->getCompanyGroups() as $companyGroup) {
            foreach ($companyGroup->getShippingGroups() as $shippingGroup) {
                foreach ($shippingGroup->getItems() as $item) {
                    $this->removeProductFromBasket($basketId, $item->getDeclinationId());
                }
            }
        }
    }

    /**
     * Update the quantity of a product (or a declination) in a basket.
     *
     * @return int Quantity of the product to set.
     *
     * @throws BadQuantity The quantity is invalid.
     * @throws NotFound The basket could not be found.
     *
     * @see addProductToBasket()
     */
    public function updateProductQuantity(string $basketId, string $declinationId, int $quantity): int
    {
        if ($quantity < 1) {
            throw new BadQuantity('"quantity" must be greater than 0');
        }

        try {
            $responseData = $this->client->post("basket/{$basketId}/modify", [
                'form_params' => [
                    'declinationId' => $declinationId,
                    'quantity' => $quantity,
                ],
            ]);
        } catch (ClientException $ex) {
            $code = $ex->getResponse()->getStatusCode();

            if (404 === $code) {
                throw new NotFound('Basket not found', $ex);
            }

            throw $ex;
        }

        return $responseData['quantity'];
    }

    /**
     * Add a coupon to the given basket.
     *
     * A coupon is a simple string. It can be added to the basket to get basket promotions.
     *
     * @throws CouponAlreadyPresent
     * @throws NotFound The basket cannot be found.
     */
    public function addCoupon(string $basketId, string $coupon)
    {
        try {
            $this->client->post("basket/{$basketId}/coupons/{$coupon}");
        } catch (ClientException $ex) {
            $code = $ex->getResponse()->getStatusCode();

            if (404 === $code) {
                throw new NotFound('Basket not found', $ex);
            } elseif (409 == $code) {
                throw new CouponAlreadyPresent('Coupon exist', $code, $ex);
            }

            throw $ex;
        }
    }

    /**
     * @throws CouponNotInTheBasket
     */
    public function removeCoupon(string $basketId, string $coupon)
    {
        try {
            $this->client->delete("basket/{$basketId}/coupons/{$coupon}");
        } catch (ClientException $ex) {
            $code = $ex->getResponse()->getStatusCode();

            if (404 === $code) {
                throw new CouponNotInTheBasket('Coupon not in the basket', $code, $ex);
            }

            throw $ex;
        }
    }

    /**
     * Returns all the payment methods available to checkout the basket.
     *
     * The user can then choose which payment method to use to pay for the order
     * (for example credit card, bank wire, etc.)
     *
     * @return Payment[]
     *
     * @throws NotFound
     * @throws AuthenticationRequired
     */
    public function getPayments(string $basketId): array
    {
        $this->client->mustBeAuthenticated();
        try {
            $payments = $this->client->get("basket/{$basketId}/payments");
        } catch (ClientException $ex) {
            $code = $ex->getResponse()->getStatusCode();

            if (404 === $code) {
                throw new NotFound('Basket not found', $ex);
            }

            throw $ex;
        }
        $payments = array_map(function ($payment) {
            return new Payment($payment);
        }, $payments);

        return $payments;
    }

    /**
     * @param array $selections a map of BasketShippingGroup ids to Shipping ids
     */
    public function selectShippings(string $basketId, array $selections): void
    {
        $this->client->post("basket/$basketId/shippings", [
            'json' => [
                'shippingGroups' => $selections,
            ],
        ]);
    }

    /**
     * Checkout the basket to create an order.
     *
     * @param int $paymentId ID of the payment method to use (see getPayments())
     * @param bool $acceptTerms Whether the user accepts the terms and conditions or not
     *                          (should be true else the order cannot be created)
     * @param string $redirectUrl URL to redirect to when the payment is made
     *                          (usually the order confirmation page)
     * @return PaymentInformation Information to proceed to the payment of the order that was created.
     *
     * @see getPayments()
     *
     * @throws AuthenticationRequired
     * @throws NotFound
     * @throws SomeParametersAreInvalid
     */
    public function checkout(string $basketId, int $paymentId, bool $acceptTerms, string $redirectUrl): PaymentInformation
    {
        $this->client->mustBeAuthenticated();
        try {
            $result = $this->client->post(
                "basket/{$basketId}/order",
                [
                    'form_params' => [
                        'paymentId' => $paymentId,
                        "acceptTermsAndConditions" => $acceptTerms,
                        'redirectUrl' => $redirectUrl,
                    ],
                ]
            );
        } catch (ClientException $ex) {
            $code = $ex->getResponse()->getStatusCode();

            if (404 === $code) {
                throw new NotFound('Basket not found', $ex);
            } elseif (400 === $code) {
                throw new SomeParametersAreInvalid($ex->getMessage(), $ex->getCode(), $ex);
            }

            throw $ex;
        }

        return new PaymentInformation($result);
    }
}
