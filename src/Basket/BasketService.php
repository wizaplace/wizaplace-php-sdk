<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\SDK\Basket;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\RequestOptions;
use Wizaplace\SDK\AbstractService;
use Wizaplace\SDK\Authentication\AuthenticationRequired;
use Wizaplace\SDK\Basket\Exception\BadQuantity;
use Wizaplace\SDK\Basket\Exception\CouponNotInTheBasket;
use Wizaplace\SDK\Catalog\DeclinationId;
use Wizaplace\SDK\Exception\BasketIsEmpty;
use Wizaplace\SDK\Exception\BasketNotFound;
use Wizaplace\SDK\Exception\CouponCodeAlreadyApplied;
use Wizaplace\SDK\Exception\CouponCodeDoesNotApply;
use Wizaplace\SDK\Exception\NotFound;
use Wizaplace\SDK\Exception\SomeParametersAreInvalid;
use function theodorejb\polycast\to_string;

/**
 * This service helps creating orders through a basket.
 *
 * Example:
 *
 *     // Create a basket and add products
 *     $basketId = $basketService->create();
 *     $basketService->addProductToBasket($basketId, <product ID>, 2);
 *
 *     // Select the shipping methods
 *     $basket = $basketService->getBasket($basketId);
 *     $shippings = [];
 *     foreach ($basket->getCompanyGroups() as $companyGroup) {
 *         foreach ($companyGroup->getShippingGroups() as $shippingGroup) {
 *             $shippings[$shippingGroup->getId()] = <let the user select the shipping from $shippingGroup->getShippings()>
 *         }
 *     }
 *     $basketService->selectShippings($basketId, $shippings);
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
final class BasketService extends AbstractService
{
    /**
     * Create a new basket.
     *
     * The basket will *not* be associated to the current user. Basket are disconnected from users.
     * If you want to keep the basket, store it (or store the ID) in the user's session.
     *
     * @return string The ID of the created basket.
     * @deprecated use \Wizaplace\SDK\Basket\BasketService::createEmptyBasket instead
     */
    public function create(): string
    {
        return to_string($this->client->post("basket"));
    }

    /**
     * Create a new basket.
     *
     * The basket will *not* be associated to the current user. Basket are disconnected from users.
     * If you want to keep the basket, store it (or store the ID) in the user's session.
     *
     * @return Basket The new empty basket.
     */
    public function createEmptyBasket(): Basket
    {
        $id = $this->create();

        return Basket::createEmpty($id);
    }

    /**
     * Add a product or a product's declination to a basket.
     *
     * @param DeclinationId $declinationId ID of the product or the product's declination to add to the basket.
     *
     * @return int quantity added
     *
     * @throws BadQuantity The quantity is invalid.
     * @throws NotFound The basket could not be found.
     */
    public function addProductToBasket(string $basketId, DeclinationId $declinationId, int $quantity): int
    {
        if ($quantity < 1) {
            throw new BadQuantity('"quantity" must be greater than 0');
        }

        try {
            $responseData = $this->client->post("basket/{$basketId}/add", [
                RequestOptions::FORM_PARAMS => [
                    'declinationId' => to_string($declinationId),
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
     * Get the currently authenticated user's basket ID
     * @throws AuthenticationRequired
     */
    public function getUserBasketId(): ?string
    {
        $this->client->mustBeAuthenticated();
        $userId = $this->client->getApiKey()->getId();

        $response = $this->client->get("users/$userId/basket");

        $id = $response['id'];
        if ($id === '') {
            $id = null;
        }

        return $id;
    }

    /**
     * Set the currently authenticated user's basket ID
     * @throws AuthenticationRequired
     */
    public function setUserBasketId(?string $basketId): void
    {
        $this->client->mustBeAuthenticated();
        $userId = $this->client->getApiKey()->getId();

        $this->client->post("users/$userId/basket", [
            RequestOptions::JSON => [
                'id' => $basketId,
            ],
        ]);
    }

    /**
     * Remove a product (or a product's declination) from the basket.
     *
     * @throws NotFound The basket could not be found.
     *
     * @see addProductToBasket()
     */
    public function removeProductFromBasket(string $basketId, DeclinationId $declinationId): void
    {
        try {
            $this->client->post("basket/{$basketId}/remove", [
                RequestOptions::FORM_PARAMS => [
                    'declinationId' => to_string($declinationId),
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
    public function updateProductQuantity(string $basketId, DeclinationId $declinationId, int $quantity): int
    {
        if ($quantity < 1) {
            throw new BadQuantity('"quantity" must be greater than 0');
        }

        try {
            $responseData = $this->client->post("basket/{$basketId}/modify", [
                RequestOptions::FORM_PARAMS => [
                    'declinationId' => to_string($declinationId),
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
     * @throws CouponCodeAlreadyApplied
     * @throws BasketNotFound
     * @throws CouponCodeDoesNotApply
     */
    public function addCoupon(string $basketId, string $coupon)
    {
        $this->client->post("basket/{$basketId}/coupons/{$coupon}");
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
        $payments = array_map(static function (array $payment) : Payment {
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
            RequestOptions::JSON => [
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
     * @throws BasketNotFound
     * @throws BasketIsEmpty
     * @throws SomeParametersAreInvalid
     */
    public function checkout(string $basketId, int $paymentId, bool $acceptTerms, string $redirectUrl): PaymentInformation
    {
        $this->client->mustBeAuthenticated();
        try {
            $result = $this->client->post(
                "basket/{$basketId}/order",
                [
                    RequestOptions::FORM_PARAMS => [
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
            }
            if (400 === $code) {
                throw new SomeParametersAreInvalid($ex->getMessage(), $ex->getCode(), $ex);
            }

            throw $ex;
        }

        return new PaymentInformation($result);
    }

    /**
     * Add or update comments on the basket or products inside the basket.
     *
     * Example:
     *
     * $basketService->updateComments($basketId, [
     *     // Comment on a product inside the basket
     *     new ProductComment($declinationId, 'Please gift wrap this product.'),
     *     // Comment on the basket
     *     new BasketComment('I am superman, please deliver to space'),
     * ]);
     *
     * @param Comment[] $comments
     * @throws NotFound
     * @throws SomeParametersAreInvalid
     */
    public function updateComments(string $basketId, array $comments): void
    {
        $commentsToPost = array_map([self::class, 'serializeComment'], $comments);

        try {
            $this->client->post(
                "basket/{$basketId}/comments",
                [
                    RequestOptions::FORM_PARAMS => [
                        'comments' => $commentsToPost,
                    ],
                ]
            );
        } catch (ClientException $e) {
            $code = $e->getResponse()->getStatusCode();

            if (404 === $code) {
                throw new NotFound('Basket not found', $e);
            }
            if (400 === $code) {
                throw new SomeParametersAreInvalid($e->getMessage(), $code, $e);
            }

            throw $e;
        }
    }

    /**
     * Merge baskets ($sourceBasketId into $targetBasketId).
     *
     * @param string $targetBasketId
     * @param string $sourceBasketId
     */
    public function mergeBaskets(string $targetBasketId, string $sourceBasketId)
    {
        $this->client->post("basket/$targetBasketId/merge", [
            RequestOptions::JSON => [
                'basketId' => $sourceBasketId,
            ],
        ]);
    }

    /**
     * Sets a pickup point as the basket's shipping destination.
     *
     * @param SetPickupPointCommand $command
     * @throws SomeParametersAreInvalid
     */
    public function setPickupPoint(SetPickupPointCommand $command): void
    {
        $command->validate();

        $this->client->post('basket/'.$command->getBasketId().'/chronorelais-pickup-point', [
            RequestOptions::JSON => [
                'pickupPointId' => $command->getPickupPointId(),
                'title' => $command->getTitle()->getValue(),
                'firstName' => $command->getFirstName(),
                'lastName' => $command->getLastName(),
            ],
        ]);
    }

    /**
     * Sets a pickup point as the basket's shipping destination.
     *
     * @param SetPickupPointCommand $command
     *
     * @return array The full address
     *
     * @throws SomeParametersAreInvalid
     */
    public function setMRPickupPoint(SetPickupPointCommand $command): array
    {
        $command->validate();

        return $this->client->post(sprintf('basket/%s/mondialrelay-pickup-point', $command->getBasketId()), [
            RequestOptions::JSON => [
                'pickupPointId' => $command->getPickupPointId(),
                'title' => $command->getTitle()->getValue(),
                'firstName' => $command->getFirstName(),
                'lastName' => $command->getLastName(),
            ],
        ]);
    }

    private static function serializeComment(Comment $comment): array
    {
        return $comment->toArray();
    }
}
