<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\Basket;

use GuzzleHttp\Exception\ClientException;
use Wizaplace\ApiClientInjection;
use Wizaplace\Basket\Exception\BadQuantity;
use Wizaplace\Basket\Exception\CouponAlreadyPresent;
use Wizaplace\Basket\Exception\CouponNotInTheBasket;
use Wizaplace\Exception\NotFound;
use Wizaplace\Exception\SomeParametersAreInvalid;

class BasketService
{
    use ApiClientInjection;

    /**
     * Add a product to a basket
     *
     * @return int    quantity added
     *
     * @throws BadQuantity When quantity is invalid
     * @throws NotFound    When basket could not be found
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
     * Remove a product from the basket
     *
     * @throws NotFound When basket could not be found
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

        return;
    }

    /**
     * Update product quantity
     *
     * @return int    quantity added
     *
     * @throws BadQuantity When quantity is invalid
     * @throws NotFound    When basket could not be found
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

    public function create(): string
    {
        return $this->client->post('POST', "basket");
    }

    /**
     * @throws CouponAlreadyPresent
     * @throws NotFound
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
     * @return Payment[]
     * @throws NotFound
     */
    public function getPayments(string $basketId): array
    {
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

    public function checkout(string $basketId, int $paymentId, bool $acceptTerms): PaymentInformation
    {
        try {
            $result = $this->client->post(
                "basket/{$basketId}/order",
                [
                    'form_params' => [
                        'paymentId' => $paymentId,
                        "acceptTermsAndConditions" => $acceptTerms,
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
