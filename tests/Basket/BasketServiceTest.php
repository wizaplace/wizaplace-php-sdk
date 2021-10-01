<?php

/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Tests\Basket;

use Wizaplace\SDK\Basket\Basket;
use Wizaplace\SDK\Basket\BasketComment;
use Wizaplace\SDK\Basket\BasketItems;
use Wizaplace\SDK\Basket\BasketService;
use Wizaplace\SDK\Basket\ExternalShippingPrice;
use Wizaplace\SDK\Basket\ProductComment;
use Wizaplace\SDK\Basket\Shipping;
use Wizaplace\SDK\Catalog\DeclinationId;
use Wizaplace\SDK\Exception\BasketIsEmpty;
use Wizaplace\SDK\Exception\BasketNotFound;
use Wizaplace\SDK\Exception\CouponCodeAlreadyApplied;
use Wizaplace\SDK\Order\OrderService;
use Wizaplace\SDK\Order\OrderStatus;
use Wizaplace\SDK\Tests\ApiTestCase;
use Wizaplace\SDK\User\AddressBookService;
use Wizaplace\SDK\User\UserService;
use Wizaplace\SDK\User\UserTitle;

/**
 * @see BasketService
 */
final class BasketServiceTest extends ApiTestCase
{
    public function testFullCheckout()
    {
        $apiClient = $this->buildApiClient();
        $apiClient->authenticate('customer-1@world-company.com', 'password-customer-1');

        $basketService = new BasketService($apiClient);
        $orderService = new OrderService($apiClient);

        $basket = $basketService->createEmptyBasket();
        $this->assertNotEmpty($basket->getId());
        $this->assertSame(0.0, $basket->getTotalPrice()->getPriceWithTaxes());
        $this->assertSame(0.0, $basket->getTotalPrice()->getPriceWithoutVat());
        $this->assertSame(0.0, $basket->getTotalPrice()->getVat());
        $this->assertSame(0.0, $basket->getItemsPrice()->getPriceWithTaxes());
        $this->assertSame(0.0, $basket->getItemsPrice()->getPriceWithoutVat());
        $this->assertSame(0.0, $basket->getItemsPrice()->getVat());
        $this->assertSame(0.0, $basket->getShippingPrice()->getPriceWithTaxes());
        $this->assertSame(0.0, $basket->getShippingPrice()->getPriceWithoutVat());
        $this->assertSame(0.0, $basket->getShippingPrice()->getVat());
        $this->assertNull($basket->getShippingAddress());

        $newQuantity = $basketService->addProductToBasket($basket->getId(), new DeclinationId('1'), 1);
        $this->assertSame(1, $newQuantity);

        $newQuantity = $basketService->addProductToBasket($basket->getId(), new DeclinationId('1'), 1);
        $this->assertSame(2, $newQuantity);

        $basket = $basketService->getBasket($basket->getId());
        $this->assertNotNull($basket);
        $this->assertNull($basket->getShippingAddress());

        $this->assertSame(135.8, $basket->getTotalPrice()->getPriceWithTaxes());
        $this->assertSame(133.01, $basket->getTotalPrice()->getPriceWithoutVat());
        $this->assertSame(2.79, $basket->getTotalPrice()->getVat());
        $this->assertSame(135.8, $basket->getItemsPrice()->getPriceWithTaxes());
        $this->assertSame(133.01, $basket->getItemsPrice()->getPriceWithoutVat());
        $this->assertSame(2.79, $basket->getItemsPrice()->getVat());
        $this->assertSame(0.0, $basket->getShippingPrice()->getPriceWithTaxes());
        $this->assertSame(0.0, $basket->getShippingPrice()->getPriceWithoutVat());
        $this->assertSame(0.0, $basket->getShippingPrice()->getVat());

        $shippings = [];
        foreach ($basket->getCompanyGroups() as $companyGroup) {
            $this->assertGreaterThan(0, $companyGroup->getCompany()->getId());
            $this->assertNotEmpty($companyGroup->getCompany()->getName());
            $this->assertNotEmpty($companyGroup->getCompany()->getSlug());

            foreach ($companyGroup->getShippingGroups() as $shippingGroup) {
                $this->assertGreaterThan(0, $shippingGroup->getId());

                $this->assertSame(135.8, $shippingGroup->getTotalPrice()->getPriceWithTaxes());
                $this->assertSame(133.01, $shippingGroup->getTotalPrice()->getPriceWithoutVat());
                $this->assertSame(2.79, $shippingGroup->getTotalPrice()->getVat());
                $this->assertSame(135.8, $shippingGroup->getItemsPrice()->getPriceWithTaxes());
                $this->assertSame(133.01, $shippingGroup->getItemsPrice()->getPriceWithoutVat());
                $this->assertSame(2.79, $shippingGroup->getItemsPrice()->getVat());
                $this->assertSame(0.0, $shippingGroup->getShippingPrice()->getPriceWithTaxes());
                $this->assertSame(0.0, $shippingGroup->getShippingPrice()->getPriceWithoutVat());
                $this->assertSame(0.0, $shippingGroup->getShippingPrice()->getVat());
                static::assertFalse($shippingGroup->isCarriagePaid());

                $availableShippings = $shippingGroup->getShippings();

                /** @var Shipping $shipping */
                $shipping = current($availableShippings);
                $this->assertEquals(38, $shipping->getId());
                $this->assertEquals('Lettre prioritaire', $shipping->getName());
                $this->assertEquals(0., $shipping->getPrice());
                $this->assertEquals('', $shipping->getDeliveryTime());
                $this->assertNull($shipping->getImage());
                $this->assertEquals(0., $shipping->getShippingPrice()->getPriceWithoutVat());
                $this->assertEquals(0., $shipping->getShippingPrice()->getPriceWithTaxes());
                $this->assertEquals(0., $shipping->getShippingPrice()->getVat());
                static::assertNull($shipping->getCarriagePaidThreshold());

                $shippings[$shippingGroup->getId()] = end($availableShippings)->getId();

                foreach ($shippingGroup->getItems() as $basketItem) {
                    // Here we mostly check the items were properly unserialized
                    $this->assertGreaterThan(0, $basketItem->getProductId());
                    $this->assertGreaterThan(0, $basketItem->getQuantity());
                    $this->assertNotEmpty($basketItem->getProductName());
                    $this->assertNotEmpty($basketItem->getProductCode());
                    $this->assertNotEmpty($basketItem->getDeclinationId());
                    $this->assertSame([], $basketItem->getDeclinationOptions());
                    $this->assertGreaterThan(0, $basketItem->getIndividualPrice());
                    $this->assertGreaterThan(0, $basketItem->getUnitPrice()->getPriceWithTaxes());
                    $this->assertGreaterThan(0, $basketItem->getUnitPrice()->getPriceWithoutVat());
                    $this->assertGreaterThan(0, $basketItem->getUnitPrice()->getVat());
                    $this->assertGreaterThanOrEqual($basketItem->getIndividualPrice(), $basketItem->getTotal());
                    $this->assertGreaterThanOrEqual($basketItem->getUnitPrice()->getPriceWithTaxes(), $basketItem->getTotalPrice()->getPriceWithTaxes());
                    $this->assertGreaterThanOrEqual($basketItem->getUnitPrice()->getPriceWithoutVat(), $basketItem->getTotalPrice()->getPriceWithoutVat());
                    $this->assertGreaterThanOrEqual($basketItem->getUnitPrice()->getVat(), $basketItem->getTotalPrice()->getVat());
                    $this->assertTrue(\is_array($basketItem->getDivisions()));
                    $basketItem->getMainImage();
                    $basketItem->getCrossedOutPrice();
                }
            }
        }
        $basketService->selectShippings($basket->getId(), $shippings);

        $availablePayments = $basketService->getPayments($basket->getId());
        foreach ($availablePayments as $availablePayment) {
            // Here we mostly check the payments were properly unserialized
            $availablePayment->getImage();
            $availablePayment->getDescription();
            static::assertNotEmpty($availablePayment->getName());
            static::assertGreaterThan(0, $availablePayment->getId());
            static::assertGreaterThanOrEqual(0, $availablePayment->getPosition());
            static::assertSame(null, $availablePayment->getExternalReference());
        }
        $selectedPayment = reset($availablePayments)->getId();
        $redirectUrl = 'https://demo.loc/order/confirm';
        $cssUrl = 'https://demo.loc/custom.css';

        $paymentInformation = $basketService->checkout(
            $basket->getId(),
            $selectedPayment,
            true,
            $redirectUrl,
            $cssUrl
        );

        // @TODO : check that the two following values are normal
        $this->assertSame('', $paymentInformation->getHtml());
        $this->assertNull($paymentInformation->getRedirectUrl());

        $orders = $paymentInformation->getOrders();
        $this->assertCount(1, $orders);
        $this->assertSame(15, $paymentInformation->getParentOrderId());

        $order = $orderService->getOrder($orders[0]->getId());
        $this->assertSame($orders[0]->getId(), $order->getId());
        $this->assertSame(3, $order->getCompanyId());
        $this->assertSame('TNT Express', $order->getShippingName());
        $this->assertEquals(OrderStatus::STANDBY_BILLING(), $order->getStatus());
        $this->assertGreaterThan(1500000000, $order->getTimestamp()->getTimestamp());
        $this->assertSame(135.8, $order->getTotal());
        $this->assertSame(135.8, $order->getSubtotal());
        $this->assertSame('40 rue Laure Diebold', $order->getShippingAddress()->getAddress());
        $this->assertSame('40 rue Laure Diebold', $order->getBillingAddress()->getAddress());

        $orderItems = $order->getOrderItems();
        $this->assertCount(1, $orderItems);
        $this->assertTrue((new DeclinationId('1_0'))->equals($orderItems[0]->getDeclinationId()));
        $this->assertSame('Z11 Plus Boîtier PC en Acier ATX', $orderItems[0]->getProductName());
        $this->assertSame('978020137962', $orderItems[0]->getProductCode());
        $this->assertSame(67.9, $orderItems[0]->getPrice());
        $this->assertSame(2, $orderItems[0]->getAmount());

        $pdf = $orderService->downloadPdfInvoice($orders[0]->getId());
        $pdfHeader = '%PDF-1.4';
        $pdfContents = $pdf->getContents();
        $this->assertStringStartsWith($pdfHeader, $pdfContents);
        $this->assertGreaterThan(\strlen($pdfHeader), \strlen($pdfContents));
    }

    public function testCleanBasket()
    {
        $apiClient = $this->buildApiClient();
        $apiClient->authenticate('customer-1@world-company.com', 'password-customer-1');

        $basketService = new BasketService($apiClient);

        $basketId = $basketService->create();
        $this->assertNotEmpty($basketId);

        $basketService->addProductToBasket($basketId, new DeclinationId('1'), 1);

        $basket = $basketService->getBasket($basketId);
        $this->assertNotNull($basket);

        $this->assertSame(1, $basket->getTotalQuantity());

        $basketService->cleanBasket($basketId);

        $basket = $basketService->getBasket($basketId);
        $this->assertNotNull($basket);

        $this->assertSame(0, $basket->getTotalQuantity());
    }

    public function testCreatingABasket()
    {
        $basketService = $this->buildAuthenticatedBasketService();

        $basketId = $basketService->create();
        $this->assertNotEmpty($basketId);

        $basket = $basketService->getBasket($basketId);
        $this->assertNotNull($basket);
    }

    public function testCreatingAnEmptyBasket()
    {
        $basketService = $this->buildAuthenticatedBasketService();

        $basket = $basketService->createEmptyBasket();
        $this->assertNotEmpty($basket->getId());

        $basket2 = $basketService->getBasket($basket->getId());
        $this->assertNotNull($basket2);

        $this->assertEquals($basket2, $basket);
    }

    public function testSelectingShippings()
    {
        // Arrange
        $basketService = $this->buildAuthenticatedBasketService();

        $basketId = $basketService->create();
        $this->assertNotEmpty($basketId);

        $basketService->addProductToBasket($basketId, new DeclinationId('5'), 1);

        $basket = $basketService->getBasket($basketId);
        $this->assertNotNull($basket);

        $companyGroups = $basket->getCompanyGroups();
        $this->assertCount(1, $companyGroups);

        $shippingGroups = $companyGroups[0]->getShippingGroups();
        $this->assertCount(1, $shippingGroups);

        $shippings = $shippingGroups[0]->getShippings();
        $this->assertCount(3, $shippings);
        $this->assertSame('Lettre prioritaire', $shippings[1]->getName());
        // @TODO : insert some real data in the fixtures
        $this->assertSame(0.0, $shippings[1]->getPrice());
        $this->assertSame('', $shippings[1]->getDeliveryTime());

        $this->assertTrue($shippings[0]->isSelected());
        $this->assertFalse($shippings[1]->isSelected());

        // Act
        $basketService->selectShippings(
            $basketId,
            [
                $shippingGroups[0]->getId() => $shippings[1]->getId(),
            ]
        );

        // Assert
        $basket = $basketService->getBasket($basketId);
        $this->assertNotNull($basket);

        $companyGroups = $basket->getCompanyGroups();
        $this->assertCount(1, $companyGroups);

        $shippingGroups = $companyGroups[0]->getShippingGroups();
        $this->assertCount(1, $shippingGroups);

        $shippings = $shippingGroups[0]->getShippings();
        $this->assertCount(3, $shippings);

        $this->assertFalse($shippings[0]->isSelected());
        $this->assertTrue($shippings[1]->isSelected());
        $this->assertFalse($shippings[2]->isSelected());
    }

    public function testGetBasketWithOption()
    {
        $basketService = $this->buildAuthenticatedBasketService();

        $basketId = $basketService->create();
        static::assertSame(1, $basketService->addProductToBasket($basketId, new DeclinationId('3_5_40'), 1));

        $basket = $basketService->getBasket($basketId);

        $declinationOption = $basket->getCompanyGroups()[0]->getShippingGroups()[0]->getItems()[0]->getDeclinationOptions()[5];
        static::assertSame(5, $declinationOption->getOptionId());
        static::assertSame('size', $declinationOption->getOptionName());
        static::assertSame(null, $declinationOption->getOptionCode());
        static::assertSame(40, $declinationOption->getVariantId());
        static::assertSame('13', $declinationOption->getVariantName());
    }

    public function testGetBasketWithSystemOption(): void
    {
        $basketService = $this->buildAuthenticatedBasketService();

        $basketId = $basketService->create();
        static::assertSame(1, $basketService->addProductToBasket($basketId, new DeclinationId('1_6_44'), 1));

        $basket = $basketService->getBasket($basketId);

        $declinationSystemOption = $basket->getCompanyGroups()[0]->getShippingGroups()[0]->getItems()[0]->getDeclinationOptions()[6];
        static::assertSame(6, $declinationSystemOption->getOptionId());
        static::assertSame('Fréquence de paiement', $declinationSystemOption->getOptionName());
        static::assertSame('payment_frequency', $declinationSystemOption->getOptionCode());
        static::assertSame(44, $declinationSystemOption->getVariantId());
        static::assertSame('1', $declinationSystemOption->getVariantName());
    }

    public function testGetBasketItems(): void
    {
        $basketService = $this->buildAuthenticatedBasketService();

        $basketId = $basketService->create();

        // add 2 distinct items
        static::assertSame(5, $basketService->addProductToBasket($basketId, new DeclinationId('3_5_40'), 5));
        static::assertSame(3, $basketService->addProductToBasket($basketId, new DeclinationId('13_0'), 3));
        static::assertSame(1, $basketService->addProductToBasket($basketId, new DeclinationId('1_6_44'), 1));

        $basketItems = $basketService->getBasketItems($basketId);
        static::assertSame(9, $basketItems->getQuantitiesTotal());
        static::assertSame(3, $basketItems->getTotal());

        $items = $basketItems->getItems();
        static::assertSame(5, $items[0]['quantity']);
        static::assertSame(3, $items[1]['quantity']);
        static::assertSame(1, $items[2]['quantity']);

        $option = $items[0]['options'][0];
        static::assertSame(5, $option['optionId']);
        static::assertSame('size', $option['optionName']);
        static::assertSame(null, $option['optionCode']);
        static::assertSame('13', $option['valueName']);

        $systemOption = $items[2]['options'][0];
        static::assertSame(6, $systemOption['optionId']);
        static::assertSame('Fréquence de paiement', $systemOption['optionName']);
        static::assertSame('payment_frequency', $systemOption['optionCode']);
        static::assertSame('1', $systemOption['valueName']);
    }

    public function testGetBasketItemsPaginate(): void
    {
        $basketService = $this->buildAuthenticatedBasketService();
        $basketId = $basketService->create();

        // Add two distinct items
        static::assertSame(5, $basketService->addProductToBasket($basketId, new DeclinationId('3_3_7'), 5));
        static::assertSame(3, $basketService->addProductToBasket($basketId, new DeclinationId('13_0'), 3));

        // Limit one item per page
        $limit = 1;

        // First page
        $basketItems = $basketService->getBasketItems($basketId, 0, $limit);
        $items = $basketItems->getItems();
        static::assertSame($limit, \count($items));
        static::assertSame(5, $items[0]['quantity']);

        // Second page
        $basketItems = $basketService->getBasketItems($basketId, 1, $limit);
        $items = $basketItems->getItems();
        static::assertSame($limit, \count($items));
        static::assertSame(3, $items[0]['quantity']);

        // Third page / should be empty
        $basketItems = $basketService->getBasketItems($basketId, 2, $limit);
        $items = $basketItems->getItems();
        static::assertSame(0, \count($items));
    }

    public function testNewEmptyBasketItems(): void
    {
        $emptyBasketItems = new BasketItems([]);
        static::assertSame(null, $emptyBasketItems->getBasketId());
        static::assertSame([], $emptyBasketItems->getItems());
        static::assertSame(0, $emptyBasketItems->getQuantitiesTotal());
        static::assertSame(0, $emptyBasketItems->getOffset());
        static::assertSame(0, $emptyBasketItems->getLimit());
        static::assertSame(0, $emptyBasketItems->getTotal());
    }

    public function testGetAndSetBasketId()
    {
        $service = $this->buildAuthenticatedBasketService();

        $this->assertNull($service->getUserBasketId());

        $basketId = $service->create();
        $this->assertNotEmpty($basketId);

        $service->setUserBasketId($basketId);

        $this->assertSame($basketId, $service->getUserBasketId());

        $service->setUserBasketId(null);

        $this->assertNull($service->getUserBasketId());
    }

    public function testUpdateCommentToProduct()
    {
        $basketService = $this->buildAuthenticatedBasketService();

        $basketId = $basketService->create();
        $this->assertSame(1, $basketService->addProductToBasket($basketId, new DeclinationId('3_5_40'), 1));
        $comments = [
            new ProductComment(new DeclinationId('3_5_40'), 'I will be available only during the afternoon'),
        ];
        $basketService->updateComments($basketId, $comments);

        $basket = $basketService->getBasket($basketId);
        $comment = $basket->getCompanyGroups()[0]->getShippingGroups()[0]->getItems()[0]->getComment();

        $this->assertSame('I will be available only during the afternoon', $comment);
    }

    public function testUpdateCommentToProductWithWrongDeclinationId()
    {

        $basketService = $this->buildAuthenticatedBasketService();

        $basketId = $basketService->create();
        $this->assertSame(1, $basketService->addProductToBasket($basketId, new DeclinationId('3_8_7'), 1));

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionCode(400);
        $this->expectExceptionMessage('Missing declination Id');

        $comments = [
            new ProductComment(new DeclinationId(''), 'I will only be available during the afternoons.'),
        ];
        $basketService->updateComments($basketId, $comments);
    }

    public function testUpdateCommentToProductWithEmptyComment()
    {
        $basketService = $this->buildAuthenticatedBasketService();

        $basketId = $basketService->create();
        $this->assertSame(1, $basketService->addProductToBasket($basketId, new DeclinationId('3_8_7'), 1));
        $comments = [
            new ProductComment(new DeclinationId('3_8_7'), ''),
        ];
        $basketService->updateComments($basketId, $comments);
    }

    public function testUpdateCommentToProductAbsentOfBasket()
    {
        $this->expectExceptionCode(400);
        $this->expectExceptionMessage('This product is not in the basket. Impossible to add a comment.');

        $basketService = $this->buildAuthenticatedBasketService();

        $basketId = $basketService->create();
        $comments = [
            new ProductComment(new DeclinationId('3_8_7'), 'I will be available only during the afternoon'),
        ];
        $basketService->updateComments($basketId, $comments);
    }

    public function testUpdateCommentToBasket()
    {
        $basketService = $this->buildAuthenticatedBasketService();

        $basketId = $basketService->create();
        $this->assertNotEmpty($basketId);

        $comments = [
            new BasketComment('I am superman, please deliver to space.'),
        ];

        $basketService->updateComments($basketId, $comments);

        $basket = $basketService->getBasket($basketId);
        $comment = $basket->getComment();

        $this->assertSame('I am superman, please deliver to space.', $comment);
    }

    public function testUpdateCommentToBasketWithInexistantBasketId()
    {
        $this->expectExceptionCode(404);
        $this->expectExceptionMessage('Basket not found');

        $basketService = $this->buildAuthenticatedBasketService();

        $comments = [
            new BasketComment('I am superman, please deliver to space.'),
        ];

        $basketService->updateComments('404', $comments);
    }

    public function testUpdateCommentToBasketWithEmptyComment()
    {
        $basketService = $this->buildAuthenticatedBasketService();

        $basketId = $basketService->create();
        $this->assertNotEmpty($basketId);

        $comments = [
            new BasketComment(''),
        ];

        $basketService->updateComments($basketId, $comments);
    }

    public function testUpdateCommentToBasketAndProduct()
    {
        $basketService = $this->buildAuthenticatedBasketService();

        $basketId = $basketService->create();
        $this->assertNotEmpty($basketId);

        $basketService->addProductToBasket($basketId, new DeclinationId('5'), 1);

        $comments = [
            new ProductComment(new DeclinationId('5_0'), 'please, gift wrap this product.'),
            new BasketComment('I am superman, please deliver to space.'),
        ];
        $basketService->updateComments($basketId, $comments);

        $basket = $basketService->getBasket($basketId);
        $productComment = $basket->getCompanyGroups()[0]->getShippingGroups()[0]->getItems()[0]->getComment();
        $basketComment = $basket->getComment();

        $this->assertSame('please, gift wrap this product.', $productComment);
        $this->assertSame('I am superman, please deliver to space.', $basketComment);
    }

    public function testMergingTwoBaskets()
    {
        $apiClient = $this->buildApiClient();
        $basketService = new BasketService($apiClient);

        $basketId = $basketService->create();
        $basketService->addProductToBasket($basketId, new DeclinationId('1_6_44'), 1);
        $basketService->addProductToBasket($basketId, new DeclinationId('3_5_40'), 1);

        $basketId2 = $basketService->create();
        $basketService->addProductToBasket($basketId2, new DeclinationId('1_6_44'), 2);
        $basketService->addProductToBasket($basketId2, new DeclinationId('3_5_41'), 1);

        $basketService->mergeBaskets($basketId, $basketId2);

        // check that the target basket was properly affected by the merge
        $mergedBasket = $basketService->getBasket($basketId);
        $quantitiesMap = [];
        foreach ($mergedBasket->getCompanyGroups() as $companyGroup) {
            foreach ($companyGroup->getShippingGroups() as $shippingGroup) {
                foreach ($shippingGroup->getItems() as $item) {
                    $quantitiesMap[(string) $item->getDeclinationId()] = $item->getQuantity();
                }
            }
        }

        $this->assertSame(
            [
                '1_6_44' => 2,
                '3_5_40' => 1,
                '3_5_41' => 1,
            ],
            $quantitiesMap
        );

        // check that the source basket is unchanged
        $sourceBasket = $basketService->getBasket($basketId2);
        $quantitiesMap = [];
        foreach ($sourceBasket->getCompanyGroups() as $companyGroup) {
            foreach ($companyGroup->getShippingGroups() as $shippingGroup) {
                foreach ($shippingGroup->getItems() as $item) {
                    $quantitiesMap[(string) $item->getDeclinationId()] = $item->getQuantity();
                }
            }
        }

        $this->assertSame(
            [
                '1_6_44' => 2,
                '3_5_41' => 1,
            ],
            $quantitiesMap
        );
    }

    public function testCoupons(): void
    {
        $apiClient = $this->buildApiClient();
        $apiClient->authenticate('customer-1@world-company.com', 'password-customer-1');

        $basketService = new BasketService($apiClient);

        $basketId = $basketService->create();
        $this->assertNotEmpty($basketId);

        $this->assertSame(1, $basketService->addProductToBasket($basketId, new DeclinationId('1_0'), 1));

        $coupons = $basketService->getBasket($basketId)->getCoupons();
        $this->assertSame([], $coupons);

        $basketService->addCoupon($basketId, 'SUPERPROMO');
        $coupons = $basketService->getBasket($basketId)->getCoupons();
        $this->assertSame(['SUPERPROMO'], $coupons);

        $basketService->removeCoupon($basketId, 'SUPERPROMO');
        $coupons = $basketService->getBasket($basketId)->getCoupons();
        $this->assertSame([], $coupons);

        $basketService->addCoupon($basketId, 'SUPERPROMO');
        $this->expectException(CouponCodeAlreadyApplied::class);
        $basketService->addCoupon($basketId, 'SUPERPROMO');
    }

    public function testAddCouponToNonExistingBasket()
    {
        $apiClient = $this->buildApiClient();
        $apiClient->authenticate('customer-1@world-company.com', 'password-customer-1');

        $basketService = new BasketService($apiClient);

        $this->expectException(BasketNotFound::class);
        $this->expectExceptionMessage('basket not found');
        $basketService->addCoupon('404', 'SUPERPROMO');
    }

    public function testPickupPointInformation(): void
    {
        $basketService = $this->buildAuthenticatedBasketService();

        // Default values (with an empty basket)
        $basket = $basketService->createEmptyBasket();
        $this->assertFalse($basket->isEligibleToPickupPointsShipping());
        $this->assertFalse($basket->isPickupPointsShipping());

        $basket = $basketService->getBasket($basket->getId());
        $this->assertFalse($basket->isEligibleToPickupPointsShipping());
        $this->assertFalse($basket->isPickupPointsShipping());

        $this->assertSame(1, $basketService->addProductToBasket($basket->getId(), new DeclinationId('13_0'), 1));

        $basket = $basketService->getBasket($basket->getId());
        $this->assertTrue($basket->isEligibleToPickupPointsShipping());
        $this->assertFalse($basket->isPickupPointsShipping());

        $basketService->addProductToBasket($basket->getId(), new DeclinationId('1_0'), 1);

        $basket = $basketService->getBasket($basket->getId());
        $this->assertFalse($basket->isEligibleToPickupPointsShipping());
        $this->assertFalse($basket->isPickupPointsShipping());
    }

    public function testCheckingOutAnEmptyBasketYieldsAnError(): void
    {
        $service = $this->buildAuthenticatedBasketService();
        $basket = $service->createEmptyBasket();

        $this->expectException(BasketIsEmpty::class);
        $service->checkout($basket->getId(), 1, true, 'https://demo.loc/order/confirm');
    }

    public function testGetTotalMarketplaceDiscount(): void
    {
        // Connect as a customer
        $apiClient = $this->buildApiClient();
        $apiClient->authenticate('customer-1@world-company.com', 'password-customer-1');
        $service = new BasketService($apiClient);

        // Add a product
        $basket = $service->createEmptyBasket();
        static::assertSame(1, $service->addProductToBasket($basket->getId(), new DeclinationId('1_0'), 1));

        // Add a Marketplace coupon
        $service->addCoupon($basket->getId(), 'FIRST');

        $basket = $service->getBasket($basket->getId());
        static::assertSame(10.0, $basket->getTotalMarketplaceDiscount());
    }

    public function testBulkRemoveProductsFromBasket(): void
    {
        // Connect as a customer
        $apiClient = $this->buildApiClient();
        $apiClient->authenticate('customer-1@world-company.com', 'password-customer-1');
        $basketService = new BasketService($apiClient);

        // Add a product
        $basket = $basketService->createEmptyBasket();
        $basketId = $basket->getId();
        $basketService->addProductToBasket($basketId, new DeclinationId('1_0'), 1);
        $basketService->addProductToBasket($basketId, new DeclinationId('3_3_7'), 1);
        $basketService->addProductToBasket($basketId, new DeclinationId('13_0'), 1);
        $declinations = [
            [
                'declinationId' => "1_0",
            ],
            [
                'declinationId' => "3_3_7",
            ],
        ];

        $basketService->bulkRemoveProductsFromBasket($basketId, $declinations);

        $sourceBasket = $basketService->getBasket($basketId);
        $totalItemsInBasket = 0;

        foreach ($sourceBasket->getCompanyGroups() as $companyGroup) {
            foreach ($companyGroup->getShippingGroups() as $shippingGroup) {
                $totalItemsInBasket += \count($shippingGroup->getItems());
            }
        }

        static::assertSame(1, $totalItemsInBasket);
    }

    public function testDeleteUserBasket(): void
    {
        $basketId = '8c54d443-eef1-4086-9bb7-b8917d8710e3';

        $apiClient = $this->buildApiClient();
        $apiClient->authenticate('user@wizaplace.com', 'password');
        $service = new BasketService($apiClient);

        $items = $service->getBasketItems($basketId);
        static::assertCount(2, $items->getItems());

        $service->deleteUserBasket();

        static::assertNull($service->getUserBasketId());
    }

    public function testAddProduct(): void
    {
        $apiClient = $this->buildApiClient();
        $apiClient->authenticate('customer-1@world-company.com', 'password-customer-1');

        $basketService = new BasketService($apiClient);

        $basketId = $basketService->create();
        $this->assertNotEmpty($basketId);
        $expectedResult = [
            'quantity' => 10,
            'added' => 10,
        ];
        $basketBody = $basketService->addProduct($basketId, new DeclinationId('1_0'), 10);
        static::assertSame($expectedResult, $basketBody);

        $basketService = new BasketService($apiClient);

        $basketId = $basketService->create();
        $this->assertNotEmpty($basketId);
        $expectedResult = [
            'quantity' => 0,
            'added' => 0,
        ];
        $basketBody = $basketService->addProduct($basketId, new DeclinationId('1_0'), 3);
        static::assertSame($expectedResult, $basketBody);
    }

    /** @dataProvider basketProvider */
    public function testCarriagePaid(array $basketData, bool $isCarriagePaid): void
    {
        $apiClient = $this->buildApiClient();
        $apiClient->authenticate('customer-1@world-company.com', 'password-customer-1');

        $basket = new Basket($basketData);

        foreach ($basket->getCompanyGroups() as $companyGroup) {
            foreach ($companyGroup->getShippingGroups() as $shippingGroup) {
                static::assertSame($isCarriagePaid, $shippingGroup->isCarriagePaid());
                $availableShippings = $shippingGroup->getShippings();
                static::assertSame(50., $availableShippings[0]->getCarriagePaidThreshold());
            }
        }
    }

    public function testChooseShippingAddress(): void
    {
        $apiClient = $this->buildApiClient();
        $apiClient->authenticate('admin@wizaplace.com', 'password');

        $basketService = new BasketService($apiClient);
        $userService = new UserService($apiClient);
        $addressBookService = new AddressBookService($apiClient);

        $basket = $basketService->createEmptyBasket();

        $userId = $userService->register('user_shipping_basket1@example.com', 'password', 'Jean', 'Paul');
        $apiClient->authenticate('user_shipping_basket1@example.com', 'password');
        $basketService->setUserBasketId($basket->getId());

        $address = [
            'label' => 'Domicile',
            'firstname' => 'firstName',
            'lastname' => 'lastName',
            'title' => UserTitle::MR(),
            'company' => 'ACME',
            'phone' => '20000',
            'address' => '40 rue Laure Diebold',
            'address_2' => '3ème étage',
            'city' => 'Lyon',
            'zipcode' => '69009',
            'country' => 'FR',
            'division_code' => 'FR-03',
            'comment' => 'Près de la poste'
        ];

        $addressId = $addressBookService->createAddressInAddressBook($userId, $address);
        $basketService->chooseShippingAddressAction($basket->getId(), $addressId);
        $basket = $basketService->getBasket($basket->getId());

        static::assertSame(UserTitle::MR()->getValue(), $basket->getShippingAddress()->getTitle()->getValue());
        static::assertSame('firstName', $basket->getShippingAddress()->getFirstName());
        static::assertSame('lastName', $basket->getShippingAddress()->getLastName());
        static::assertSame('ACME', $basket->getShippingAddress()->getCompany());
        static::assertSame('20000', $basket->getShippingAddress()->getPhone());
        static::assertSame('40 rue Laure Diebold', $basket->getShippingAddress()->getAddress());
        static::assertSame('3ème étage', $basket->getShippingAddress()->getAddressSecondLine());
        static::assertSame('69009', $basket->getShippingAddress()->getZipCode());
        static::assertSame('Lyon', $basket->getShippingAddress()->getCity());
        static::assertSame('FR', $basket->getShippingAddress()->getCountry());
        static::assertSame('FR-03', $basket->getShippingAddress()->getDivision());
        static::assertSame('Domicile', $basket->getShippingAddress()->getLabel());
        static::assertSame('Près de la poste', $basket->getShippingAddress()->getComment());
    }

    public function testChooseBillingAddress(): void
    {
        $apiClient = $this->buildApiClient();
        $apiClient->authenticate('admin@wizaplace.com', 'password');

        $basketService = new BasketService($apiClient);
        $userService = new UserService($apiClient);
        $addressBookService = new AddressBookService($apiClient);

        $basket = $basketService->createEmptyBasket();

        $userId = $userService->register('user_billing_basket@example.com', 'password', 'Jean', 'Paul');
        $apiClient->authenticate('user_billing_basket@example.com', 'password');
        $basketService->setUserBasketId($basket->getId());

        $address = [
            'label' => 'Domicile',
            'firstname' => 'firstName',
            'lastname' => 'lastName',
            'title' => UserTitle::MR(),
            'company' => 'ACME',
            'phone' => '20000',
            'address' => '40 rue Laure Diebold',
            'address_2' => '3ème étage',
            'city' => 'Lyon',
            'zipcode' => '69009',
            'country' => 'FR',
            'division_code' => 'FR-03',
            'comment' => 'Près de la poste'
        ];

        $addressId = $addressBookService->createAddressInAddressBook($userId, $address);
        $basketService->chooseBillingAddressAction($basket->getId(), $addressId);
        $basket = $basketService->getBasket($basket->getId());

        static::assertSame(UserTitle::MR()->getValue(), $basket->getBillingAddress()->getTitle()->getValue());
        static::assertSame('firstName', $basket->getBillingAddress()->getFirstName());
        static::assertSame('lastName', $basket->getBillingAddress()->getLastName());
        static::assertSame('ACME', $basket->getBillingAddress()->getCompany());
        static::assertSame('20000', $basket->getBillingAddress()->getPhone());
        static::assertSame('40 rue Laure Diebold', $basket->getBillingAddress()->getAddress());
        static::assertSame('3ème étage', $basket->getBillingAddress()->getAddressSecondLine());
        static::assertSame('69009', $basket->getBillingAddress()->getZipCode());
        static::assertSame('Lyon', $basket->getBillingAddress()->getCity());
        static::assertSame('FR', $basket->getBillingAddress()->getCountry());
        static::assertSame('FR-03', $basket->getBillingAddress()->getDivision());
        static::assertSame('Domicile', $basket->getBillingAddress()->getLabel());
        static::assertSame('Près de la poste', $basket->getBillingAddress()->getComment());
    }

    public function testGetBasketWithAddressesHavingLabelAndComment(): void
    {
        $apiClient = $this->buildApiClient();
        $apiClient->authenticate('admin@wizaplace.com', 'password');

        $basketService = new BasketService($apiClient);
        $userService = new UserService($apiClient);
        $addressBookService = new AddressBookService($apiClient);

        $basket = $basketService->createEmptyBasket();

        $userId = $userService->register('user_shipping_basket@example.com', 'password', 'Jean', 'Paul');
        $apiClient->authenticate('user_shipping_basket@example.com', 'password');
        $basketService->setUserBasketId($basket->getId());

        $address = [
            'label' => 'Domicile',
            'firstname' => 'firstName',
            'lastname' => 'lastName',
            'title' => UserTitle::MR(),
            'company' => 'ACME',
            'phone' => '20000',
            'address' => '40 rue Laure Diebold',
            'address_2' => '3ème étage',
            'city' => 'Lyon',
            'zipcode' => '69009',
            'country' => 'FR',
            'division_code' => 'FR-03',
            'comment' => 'Près de la poste'
        ];

        $addressId = $addressBookService->createAddressInAddressBook($userId, $address);
        $basketService->chooseShippingAddressAction($basket->getId(), $addressId);
        $basket = $basketService->getBasket($basket->getId());

        static::assertSame('Domicile', $basket->getShippingAddress()->getLabel());
        static::assertSame('Près de la poste', $basket->getShippingAddress()->getComment());
    }

    public function testUpdateShippingPrice(): void
    {
        $basketService = $this->buildAuthenticatedBasketService();

        // Create basket and add product
        $basket = $basketService->createEmptyBasket();
        $price = 10.0;
        $basketService->addProductToBasket($basket->getId(), new DeclinationId('1'), 1);
        $basketService->addProductToBasket($basket->getId(), new DeclinationId('2'), 1);

        $basket = $basketService->getBasket($basket->getId());
        $shippings = [];

        foreach ($basket->getCompanyGroups() as $companyGroup) {
            foreach ($companyGroup->getShippingGroups() as $shippingGroup) {
                foreach ($shippingGroup->getShippings() as $shipping) {
                    $shippings[] = new ExternalShippingPrice(
                        $shippingGroup->getId(),
                        $shipping->getId(),
                        $price
                    );
                }
            }
        }

        $basketService->updateShippingPrice($basket->getId(), $shippings);

        $basket = $basketService->getBasket($basket->getId());

        foreach ($basket->getCompanyGroups() as $companyGroup) {
            foreach ($companyGroup->getShippingGroups() as $shippingGroup) {
                foreach ($shippingGroup->getShippings() as $shipping) {
                    static::assertSame($price, $shipping->getShippingPrice()->getPriceWithTaxes());
                    static::assertSame($price, $shipping->getPrice());
                    static::assertTrue($shipping->isExternalPrice());
                }
            }
        }
    }


    public function testResetShippingPrice(): void
    {
        $basketService = $this->buildAuthenticatedBasketService();

        // Create basket and add product
        $basket = $basketService->createEmptyBasket();
        $price = 10.0;
        $basketService->addProductToBasket($basket->getId(), new DeclinationId('1'), 1);
        $basketService->addProductToBasket($basket->getId(), new DeclinationId('2'), 1);

        $basket = $basketService->getBasket($basket->getId());
        $shippings = [];

        foreach ($basket->getCompanyGroups() as $companyGroup) {
            foreach ($companyGroup->getShippingGroups() as $shippingGroup) {
                foreach ($shippingGroup->getShippings() as $shipping) {
                    $shippings[] = new ExternalShippingPrice(
                        $shippingGroup->getId(),
                        $shipping->getId(),
                        $price
                    );
                }
            }
        }

        $basketService->updateShippingPrice($basket->getId(), $shippings);
        $basketService->resetShippingPrice($basket->getId());

        $basket = $basketService->getBasket($basket->getId());

        foreach ($basket->getCompanyGroups() as $companyGroup) {
            foreach ($companyGroup->getShippingGroups() as $shippingGroup) {
                foreach ($shippingGroup->getShippings() as $shipping) {
                    static::assertNotSame($price, $shipping->getShippingPrice()->getPriceWithTaxes());
                    static::assertNotSame($price, $shipping->getPrice());
                    static::assertFalse($shipping->isExternalPrice());
                }
            }
        }
    }

    public function basketProvider(): array
    {
        return [
            [
                [
                    'id' => uniqid('id', true),
                    'coupons' => [],
                    'subtotal' => 0.0,
                    'totalDiscount' => 0.0,
                    'totalShipping' => 0.0,
                    'totalTax' => 0.0,
                    'total' => 0.0,
                    'totalQuantity' => 0,
                    'comment' => '',
                    'companyGroups' => [
                        [
                            'company' => [
                                'id' => 1,
                                'name' => 'Marchand de test',
                                'slug' => 'toto'
                            ],
                            'shippingGroups' => [
                                [
                                    'id' => 1,
                                    'items' => [
                                        [
                                            'declinationId' => uniqid('id', true),
                                            'productId' => 1,
                                            'productName' => 'Toto',
                                            'productCode' => '12345',
                                            'individualPrice' => 50.,
                                            'crossedOutPrice' => 50.,
                                            'mainImage' => [
                                                'id' => 1,
                                            ],
                                            'quantity' => 1,
                                            'total' => 50.,
                                            'comment' => 'toto',
                                            'unitPrice' => [
                                                'priceWithoutVat' => 50.,
                                                'priceWithTaxes' => 50.,
                                                'vat' => 0.,
                                            ],
                                            'totalPrice' => [
                                                'priceWithoutVat' => 50.,
                                                'priceWithTaxes' => 50.,
                                                'vat' => 0.,
                                            ],
                                            'greenTax' => 0.,
                                        ],
                                    ],
                                    'shippings' => [
                                        [
                                            'id' => 1,
                                            'name' => 'Lettre prioritaire',
                                            'price' => 5.,
                                            'deliveryTime' => "24h",
                                            'selected' => true,
                                            'shippingPrice' => [
                                                'priceWithoutVat' => 5.,
                                                'priceWithTaxes' => 5.,
                                                'vat' => 0.,
                                            ],
                                            'image' => null,
                                            'carriagePaidThreshold' => 50.,
                                        ],
                                    ],
                                    'itemsPrice' => [
                                        'priceWithoutVat' => 50.,
                                        'priceWithTaxes' => 50.,
                                        'vat' => 0.,
                                    ],
                                    'selectedShippingPrice' => [
                                        'priceWithoutVat' => 5.,
                                        'priceWithTaxes' => 5.,
                                        'vat' => 0.,
                                    ],
                                    'totalPrice' => [
                                        'priceWithoutVat' => 55.,
                                        'priceWithTaxes' => 55.,
                                        'vat' => 0.,
                                    ],
                                    'carriagePaid' => true,
                                ],
                            ],
                        ],
                    ],
                    'totalItemsPrice' => [
                        'priceWithoutVat' => 0.0,
                        'priceWithTaxes' => 0.0,
                        'vat' => 0.0,
                    ],
                    'totalShippingsPrice' => [
                        'priceWithoutVat' => 0.0,
                        'priceWithTaxes' => 0.0,
                        'vat' => 0.0,
                    ],
                    'totalGlobalPrice' => [
                        'priceWithoutVat' => 0.0,
                        'priceWithTaxes' => 0.0,
                        'vat' => 0.0,
                    ],
                    'isEligibleToPickupPointsShipping' => false,
                    'isPickupPointsShipping' => false,
                ],
                true
            ],
            [
                [
                    'id' => uniqid('id', true),
                    'coupons' => [],
                    'subtotal' => 0.0,
                    'totalDiscount' => 0.0,
                    'totalShipping' => 0.0,
                    'totalTax' => 0.0,
                    'total' => 0.0,
                    'totalQuantity' => 0,
                    'comment' => '',
                    'companyGroups' => [
                        [
                            'company' => [
                                'id' => 1,
                                'name' => 'Marchand de test',
                                'slug' => 'toto'
                            ],
                            'shippingGroups' => [
                                [
                                    'id' => 1,
                                    'items' => [
                                        [
                                            'declinationId' => uniqid('id', true),
                                            'productId' => 1,
                                            'productName' => 'Toto',
                                            'productCode' => '12345',
                                            'individualPrice' => 50.,
                                            'crossedOutPrice' => 50.,
                                            'mainImage' => [
                                                'id' => 1,
                                            ],
                                            'quantity' => 1,
                                            'total' => 50.,
                                            'comment' => 'toto',
                                            'unitPrice' => [
                                                'priceWithoutVat' => 50.,
                                                'priceWithTaxes' => 50.,
                                                'vat' => 0.,
                                            ],
                                            'totalPrice' => [
                                                'priceWithoutVat' => 50.,
                                                'priceWithTaxes' => 50.,
                                                'vat' => 0.,
                                            ],
                                            'greenTax' => 0.,
                                        ],
                                    ],
                                    'shippings' => [
                                        [
                                            'id' => 1,
                                            'name' => 'Lettre prioritaire',
                                            'price' => 5.,
                                            'deliveryTime' => "24h",
                                            'selected' => true,
                                            'shippingPrice' => [
                                                'priceWithoutVat' => 5.,
                                                'priceWithTaxes' => 5.,
                                                'vat' => 0.,
                                            ],
                                            'image' => null,
                                            'carriagePaidThreshold' => 50.,
                                        ]
                                    ],
                                    'itemsPrice' => [
                                        'priceWithoutVat' => 50.,
                                        'priceWithTaxes' => 50.,
                                        'vat' => 0.,
                                    ],
                                    'selectedShippingPrice' => [
                                        'priceWithoutVat' => 5.,
                                        'priceWithTaxes' => 5.,
                                        'vat' => 0.,
                                    ],
                                    'totalPrice' => [
                                        'priceWithoutVat' => 55.,
                                        'priceWithTaxes' => 55.,
                                        'vat' => 0.,
                                    ],
                                    'carriagePaid' => false,
                                ],
                            ],
                        ]
                    ],
                    'totalItemsPrice' => [
                        'priceWithoutVat' => 0.0,
                        'priceWithTaxes' => 0.0,
                        'vat' => 0.0,
                    ],
                    'totalShippingsPrice' => [
                        'priceWithoutVat' => 0.0,
                        'priceWithTaxes' => 0.0,
                        'vat' => 0.0,
                    ],
                    'totalGlobalPrice' => [
                        'priceWithoutVat' => 0.0,
                        'priceWithTaxes' => 0.0,
                        'vat' => 0.0,
                    ],
                    'isEligibleToPickupPointsShipping' => false,
                    'isPickupPointsShipping' => false,
                ],
                false
            ],
            [
                [
                    'id' => uniqid('id', true),
                    'coupons' => [],
                    'subtotal' => 0.0,
                    'totalDiscount' => 0.0,
                    'totalShipping' => 0.0,
                    'totalTax' => 0.0,
                    'total' => 0.0,
                    'totalQuantity' => 0,
                    'comment' => '',
                    'companyGroups' => [
                        [
                            'company' => [
                                'id' => 1,
                                'name' => 'Marchand de test',
                                'slug' => 'toto'
                            ],
                            'shippingGroups' => [
                                [
                                    'id' => 1,
                                    'items' => [
                                        [
                                            'declinationId' => uniqid('id', true),
                                            'productId' => 1,
                                            'productName' => 'Toto',
                                            'productCode' => '12345',
                                            'individualPrice' => 50.,
                                            'crossedOutPrice' => 50.,
                                            'mainImage' => [
                                                'id' => 1,
                                            ],
                                            'quantity' => 1,
                                            'total' => 50.,
                                            'comment' => 'toto',
                                            'unitPrice' => [
                                                'priceWithoutVat' => 50.,
                                                'priceWithTaxes' => 50.,
                                                'vat' => 0.,
                                            ],
                                            'totalPrice' => [
                                                'priceWithoutVat' => 50.,
                                                'priceWithTaxes' => 50.,
                                                'vat' => 0.,
                                            ],
                                            'greenTax' => 0.,
                                        ],
                                    ],
                                    'shippings' => [
                                        [
                                            'id' => 1,
                                            'name' => 'Lettre prioritaire',
                                            'price' => 5.,
                                            'deliveryTime' => "24h",
                                            'selected' => true,
                                            'shippingPrice' => [
                                                'priceWithoutVat' => 5.,
                                                'priceWithTaxes' => 5.,
                                                'vat' => 0.,
                                            ],
                                            'image' => null,
                                            'carriagePaidThreshold' => 50.,
                                        ]
                                    ],
                                    'itemsPrice' => [
                                        'priceWithoutVat' => 50.,
                                        'priceWithTaxes' => 50.,
                                        'vat' => 0.,
                                    ],
                                    'selectedShippingPrice' => [
                                        'priceWithoutVat' => 5.,
                                        'priceWithTaxes' => 5.,
                                        'vat' => 0.,
                                    ],
                                    'totalPrice' => [
                                        'priceWithoutVat' => 55.,
                                        'priceWithTaxes' => 55.,
                                        'vat' => 0.,
                                    ],
                                ],
                            ],
                        ]
                    ],
                    'totalItemsPrice' => [
                        'priceWithoutVat' => 0.0,
                        'priceWithTaxes' => 0.0,
                        'vat' => 0.0,
                    ],
                    'totalShippingsPrice' => [
                        'priceWithoutVat' => 0.0,
                        'priceWithTaxes' => 0.0,
                        'vat' => 0.0,
                    ],
                    'totalGlobalPrice' => [
                        'priceWithoutVat' => 0.0,
                        'priceWithTaxes' => 0.0,
                        'vat' => 0.0,
                    ],
                    'isEligibleToPickupPointsShipping' => false,
                    'isPickupPointsShipping' => false,
                ],
                false
            ],
        ];
    }

    private function buildAuthenticatedBasketService(string $email = "admin@wizaplace.com", string $password = "password"): BasketService
    {
        $apiClient = $this->buildApiClient();
        $apiClient->authenticate($email, $password);

        return new BasketService($apiClient);
    }
}
