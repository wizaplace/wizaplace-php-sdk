<?php

/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Basket;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\RequestOptions;
use Wizaplace\SDK\AbstractService;
use Wizaplace\SDK\Authentication\AuthenticationRequired;
use Wizaplace\SDK\Authentication\BadCredentials;
use Wizaplace\SDK\Basket\Exception\BadQuantity;
use Wizaplace\SDK\Basket\Exception\CouponNotInTheBasket;
use Wizaplace\SDK\Catalog\DeclinationId;
use Wizaplace\SDK\Exception\NotFound;
use Wizaplace\SDK\Exception\SomeParametersAreInvalid;

use function theodorejb\polycast\to_string;

/**
 * Class BasketService
 * @package Wizaplace\SDK\Basket
 *
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
class BasketService extends AbstractService
{
    private const QUANTITY = 'quantity';

    /**
     * Create a new basket.
     *
     * The basket will *not* be associated to the current user. Basket are disconnected from users.
     * If you want to keep the basket, store it (or store the ID) in the user's session.
     *
     * @return string The ID of the created basket.
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Wizaplace\SDK\Exception\JsonDecodingError
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
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Wizaplace\SDK\Exception\JsonDecodingError
     */
    public function createEmptyBasket(): Basket
    {
        $id = $this->create();

        return Basket::createEmpty($id);
    }

    /**
     * Add a product or a product's declination to a basket.
     *
     * @param string        $basketId
     * @param DeclinationId $declinationId ID of the product or the product's declination to add to the basket.
     *
     * @param int           $quantity
     *
     * @return int Total product quantity in the basket
     *
     * @throws BadQuantity The quantity is invalid.
     * @throws NotFound The basket could not be found.
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Wizaplace\SDK\Exception\JsonDecodingError
     *
     * @deprecated use addProduct
     */
    public function addProductToBasket(string $basketId, DeclinationId $declinationId, int $quantity): int
    {
        return (int) $this->addProduct($basketId, $declinationId, $quantity)[static::QUANTITY];
    }

    /**
     * Add a product or a product's declination to a basket.
     *
     * @param string        $basketId
     * @param DeclinationId $declinationId ID of the product or the product's declination to add to the basket.
     *
     * @param int           $quantity
     *
     * @return array[] Total product quantity in the basket and product quantity added to basket
     *
     * @throws BadQuantity The quantity is invalid.
     * @throws NotFound The basket could not be found.
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Wizaplace\SDK\Exception\JsonDecodingError
     */
    public function addProduct(string $basketId, DeclinationId $declinationId, int $quantity): array
    {
        if ($quantity < 1) {
            throw new BadQuantity('"quantity" must be greater than 0');
        }

        try {
            $responseData = $this->client->post(
                "basket/{$basketId}/add",
                [
                    RequestOptions::FORM_PARAMS => [
                        'declinationId' => to_string($declinationId),
                        static::QUANTITY => $quantity,
                    ],
                ]
            );
        } catch (ClientException $ex) {
            $code = $ex->getResponse()->getStatusCode();

            if (404 === $code) {
                throw new NotFound('Basket not found', $ex);
            }

            throw $ex;
        }
        return $responseData;
    }

    /**
     * Get a basket
     *
     * @param string $basketId
     *
     * @return Basket
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Wizaplace\SDK\Exception\JsonDecodingError
     */
    public function getBasket(string $basketId): Basket
    {
        return new Basket($this->client->get("basket/{$basketId}"));
    }

    /**
     * Get basket items (faster, with minimal informations)
     *
     * @param string $basketId
     * @param int $offset
     * @param int $limit
     */
    public function getBasketItems(string $basketId, int $offset = 0, int $limit = 100): BasketItems
    {
        return new BasketItems(
            $this->client->get(
                "basket/{$basketId}/items?offset={$offset}&limit={$limit}"
            )
        );
    }

    /**
     * Get the currently authenticated user's basket ID
     * @return string|null
     * @throws AuthenticationRequired
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Wizaplace\SDK\Exception\JsonDecodingError
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
     *
     * @param string|null $basketId
     *
     * @throws AuthenticationRequired
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Wizaplace\SDK\Exception\JsonDecodingError
     */
    public function setUserBasketId(?string $basketId): void
    {
        $this->client->mustBeAuthenticated();
        $userId = $this->client->getApiKey()->getId();

        $this->client->post(
            "users/$userId/basket",
            [
                RequestOptions::JSON => [
                    'id' => $basketId,
                ],
            ]
        );
    }

    public function deleteUserBasket(): void
    {
        $this->client->mustBeAuthenticated();
        $userId = $this->client->getApiKey()->getId();

        try {
            $this->client->delete("users/$userId/basket");
        } catch (ClientException $exception) {
            switch ($exception->getResponse()->getStatusCode()) {
                case 404:
                    throw new NotFound('Basket not found', $exception);
            }

            throw $exception;
        }
    }

    /**
     * Remove a product (or a product's declination) from the basket.
     *
     * @param string        $basketId
     * @param DeclinationId $declinationId
     *
     * @throws NotFound The basket could not be found.
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Wizaplace\SDK\Exception\JsonDecodingError
     * @see addProductToBasket()
     */
    public function removeProductFromBasket(string $basketId, DeclinationId $declinationId): void
    {
        try {
            $this->client->post(
                "basket/{$basketId}/remove",
                [
                    RequestOptions::FORM_PARAMS => [
                        'declinationId' => to_string($declinationId),
                    ],
                ]
            );
        } catch (ClientException $ex) {
            $code = $ex->getResponse()->getStatusCode();

            if (404 === $code) {
                throw new NotFound('Basket not found', $ex);
            }

            throw $ex;
        }
    }

    /**
     * Bulk remove a product (or a product's declination) from the basket
     *
     * @param array[] $declinations
     */
    public function bulkRemoveProductsFromBasket(string $basketId, array $declinations): void
    {
        $jsonDeclinations = [];

        foreach ($declinations as $declinationId) {
            $jsonDeclinations[] = json_encode($declinationId);
        }

        try {
            $this->client->post(
                "basket/{$basketId}/bulk-remove",
                [
                    RequestOptions::JSON => [
                        'declinations' => $jsonDeclinations,
                    ],
                ]
            );
        } catch (ClientException $exception) {
            $code = $exception->getResponse()->getStatusCode();

            if (404 === $code) {
                throw new NotFound('Basket not found', $exception);
            }

            throw $exception;
        }
    }

    /**
     * Clear all the products from the basket.
     *
     * @param string $basketId
     *
     * @throws NotFound The basket could not be found.
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Wizaplace\SDK\Exception\JsonDecodingError
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
     * @param string        $basketId
     * @param DeclinationId $declinationId
     * @param int           $quantity
     *
     * @return int Quantity of the product to set.
     *
     * @throws BadQuantity The quantity is invalid.
     * @throws NotFound The basket could not be found.
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Wizaplace\SDK\Exception\JsonDecodingError
     * @see addProductToBasket()
     */
    public function updateProductQuantity(string $basketId, DeclinationId $declinationId, int $quantity): int
    {
        if ($quantity < 1) {
            throw new BadQuantity('"quantity" must be greater than 0');
        }

        try {
            $responseData = $this->client->post(
                "basket/{$basketId}/modify",
                [
                    RequestOptions::FORM_PARAMS => [
                        'declinationId' => to_string($declinationId),
                        static::QUANTITY => $quantity,
                    ],
                ]
            );
        } catch (ClientException $ex) {
            $code = $ex->getResponse()->getStatusCode();

            if (404 === $code) {
                throw new NotFound('Basket not found', $ex);
            }

            throw $ex;
        }

        return $responseData[static::QUANTITY];
    }

    /**
     * Add a coupon to the given basket.
     *
     * A coupon is a simple string. It can be added to the basket to get basket promotions.
     *
     * @param string $basketId
     * @param string $coupon
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Wizaplace\SDK\Exception\JsonDecodingError
     */
    public function addCoupon(string $basketId, string $coupon)
    {
        $this->client->post("basket/{$basketId}/coupons/{$coupon}");
    }

    /**
     * @param string $basketId
     * @param string $coupon
     *
     * @throws CouponNotInTheBasket
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Wizaplace\SDK\Exception\JsonDecodingError
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
     * @param string $basketId
     *
     * @return Payment[]
     *
     * @throws NotFound
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Wizaplace\SDK\Exception\JsonDecodingError
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
        $payments = array_map(
            static function (array $payment): Payment {
                return new Payment($payment);
            },
            $payments
        );

        return $payments;
    }

    /**
     * @param string $basketId
     * @param array  $selections a map of BasketShippingGroup ids to Shipping ids
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Wizaplace\SDK\Exception\JsonDecodingError
     */
    public function selectShippings(string $basketId, array $selections): void
    {
        $this->client->post(
            "basket/$basketId/shippings",
            [
                RequestOptions::JSON => [
                    'shippingGroups' => $selections,
                ],
            ]
        );
    }

    /**
     * Checkout the basket to create an order.
     *
     * @param string $basketId
     * @param int    $paymentId   ID of the payment method to use (see getPayments())
     * @param bool   $acceptTerms Whether the user accepts the terms and conditions or not
     *                            (should be true else the order cannot be created)
     * @param string $redirectUrl URL to redirect to when the payment is made
     *                            (usually the order confirmation page)
     * @param string $css         URL of the css file to include in the payment page
     *
     * @return PaymentInformation Information to proceed to the payment of the order that was created.
     *
     * @throws AuthenticationRequired
     * @throws NotFound
     * @throws SomeParametersAreInvalid
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Wizaplace\SDK\Exception\JsonDecodingError
     * @see getPayments()
     */
    public function checkout(
        string $basketId,
        int $paymentId,
        bool $acceptTerms,
        string $redirectUrl,
        string $css = null
    ): PaymentInformation {
        $this->client->mustBeAuthenticated();
        try {
            $result = $this->client->post(
                "basket/{$basketId}/order",
                [
                    RequestOptions::FORM_PARAMS => [
                        'paymentId' => $paymentId,
                        'acceptTermsAndConditions' => $acceptTerms,
                        'redirectUrl' => $redirectUrl,
                        'css' => $css,
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
     * @param string    $basketId
     * @param Comment[] $comments
     *
     * @throws NotFound
     * @throws SomeParametersAreInvalid
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Wizaplace\SDK\Exception\JsonDecodingError
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
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Wizaplace\SDK\Exception\JsonDecodingError
     */
    public function mergeBaskets(string $targetBasketId, string $sourceBasketId)
    {
        $this->client->post(
            "basket/$targetBasketId/merge",
            [
                RequestOptions::JSON => [
                    'basketId' => $sourceBasketId,
                ],
            ]
        );
    }

    /**
     * Sets a pickup point as the basket's shipping destination for a
     * Chrono Relais shipping type.
     *
     * @param SetPickupPointCommand $command
     *
     * @throws SomeParametersAreInvalid
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Wizaplace\SDK\Exception\JsonDecodingError
     */
    public function setPickupPoint(SetPickupPointCommand $command): void
    {
        $command->validate();

        $this->client->post(
            'basket/' . $command->getBasketId() . '/chronorelais-pickup-point',
            [
                RequestOptions::JSON => [
                    'pickupPointId' => $command->getPickupPointId(),
                    'title' => $command->getTitle()->getValue(),
                    'firstName' => $command->getFirstName(),
                    'lastName' => $command->getLastName(),
                    'shippingGroupsIds' => $command->getShippingGroupsIds(),
                ],
            ]
        );
    }

    /**
     * Sets a pickup point as the basket's shipping destination for a
     * Mondial Relay shipping type.
     *
     * @param SetPickupPointCommand $command
     *
     * @return array The full address
     *
     * @throws SomeParametersAreInvalid
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Wizaplace\SDK\Exception\JsonDecodingError
     */
    public function setMondialRelayPickupPoint(SetPickupPointCommand $command): array
    {
        $command->validate();

        return $this->client->post(
            sprintf('basket/%s/mondialrelay-pickup-point', $command->getBasketId()),
            [
                RequestOptions::JSON => [
                    'pickupPointId' => $command->getPickupPointId(),
                    'title' => $command->getTitle()->getValue(),
                    'firstName' => $command->getFirstName(),
                    'lastName' => $command->getLastName(),
                    'shippingGroupsIds' => $command->getShippingGroupsIds(),
                ],
            ]
        );
    }

    /**
     * set basket shippingAddress from AddressBook
     *
     * @param string $basketId
     * @param string $addressId
     * @throws BadCredentials
     * @throws NotFound
     * @throws SomeParametersAreInvalid
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Wizaplace\SDK\Exception\JsonDecodingError
     */
    public function chooseShippingAddressAction(string $basketId, string $addressId): void
    {
        try {
            $this->client->post(
                "basket/{$basketId}/choose-shipping-address",
                [
                    RequestOptions::JSON => [
                        'addressId' => $addressId,
                    ],
                ]
            );
        } catch (ClientException $exception) {
            switch ($exception->getResponse()->getStatusCode()) {
                case 400:
                    throw new SomeParametersAreInvalid($exception->getMessage(), 400);
                case 403:
                    throw new BadCredentials($exception);
                case 404:
                    throw new NotFound('Basket not found', $exception);
                default:
                    throw $exception;
            }
        }
    }

    /**
     * set basket billingAddress from AddressBook
     *
     * @param string $basketId
     * @param string $addressId
     * @throws BadCredentials
     * @throws NotFound
     * @throws SomeParametersAreInvalid
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Wizaplace\SDK\Exception\JsonDecodingError
     */
    public function chooseBillingAddressAction(string $basketId, string $addressId): void
    {
        try {
            $this->client->post(
                "basket/{$basketId}/choose-billing-address",
                [
                    RequestOptions::JSON => [
                        'addressId' => $addressId,
                    ],
                ]
            );
        } catch (ClientException $exception) {
            switch ($exception->getResponse()->getStatusCode()) {
                case 400:
                    throw new SomeParametersAreInvalid($exception->getMessage(), 400);
                case 403:
                    throw new BadCredentials($exception);
                case 404:
                    throw new NotFound('Basket not found', $exception);
                default:
                    throw $exception;
            }
        }
    }

    /**
     * Update shipping price for shipping group.
     *
     * @param string $basketId
     * @param ExternalShippingPrice[] $shippings
     *
     * @throws SomeParametersAreInvalid
     * @throws BadCredentials
     * @throws NotFound
     */
    public function updateShippingPrice(string $basketId, array $shippings): void
    {
        $body = ['shippingGroups' => []];

        foreach ($shippings as $shipping) {
            $key = \array_search(
                $shipping->getShippingGroupId(),
                \array_column($body['shippingGroups'], 'id'),
                true
            );
            $shippingArray = [
                'id' => $shipping->getShippingId(),
                'price' => $shipping->getPrice(),
            ];

            if (false === $key) {
                $body['shippingGroups'][] = [
                    'id' => $shipping->getShippingGroupId(),
                    'shippings' => [$shippingArray],
                ];
            } else {
                $body['shippingGroups'][$key]['shippings'][] = $shippingArray;
            }
        }

        try {
            $this->client->post(
                "basket/{$basketId}/shipping-price",
                [
                    RequestOptions::JSON => $body,
                ]
            );
        } catch (ClientException $exception) {
            switch ($exception->getResponse()->getStatusCode()) {
                case 400:
                    throw new SomeParametersAreInvalid($exception->getMessage(), 400);
                case 403:
                    throw new BadCredentials($exception);
                case 404:
                    throw new NotFound('Basket not found', $exception);
                default:
                    throw $exception;
            }
        }
    }

    /**
     * Reset all shipping price to default
     *
     * @param string $basketId
     *
     * @throws SomeParametersAreInvalid
     * @throws BadCredentials
     * @throws NotFound
     */
    public function resetShippingPrice(string $basketId): void
    {
        try {
            $this->client->delete("basket/{$basketId}/shipping-price");
        } catch (ClientException $exception) {
            switch ($exception->getResponse()->getStatusCode()) {
                case 400:
                    throw new SomeParametersAreInvalid($exception->getMessage(), 400);
                case 403:
                    throw new BadCredentials($exception);
                case 404:
                    throw new NotFound('Basket not found', $exception);
                default:
                    throw $exception;
            }
        }
    }

    /**
     * @param Comment $comment
     *
     * @return array
     */
    private static function serializeComment(Comment $comment): array
    {
        return $comment->toArray();
    }
}
