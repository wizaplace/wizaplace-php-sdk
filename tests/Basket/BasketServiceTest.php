<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\SDK\Tests\Basket;

use Wizaplace\SDK\Basket\CheckoutWithPreAuthTokenCommand;
use Wizaplace\SDK\Basket\BasketComment;
use Wizaplace\SDK\Basket\BasketService;
use Wizaplace\SDK\Basket\CheckoutWithRedirectUrlCommand;
use Wizaplace\SDK\Basket\ProductComment;
use Wizaplace\SDK\Catalog\DeclinationId;
use Wizaplace\SDK\Exception\BasketIsEmpty;
use Wizaplace\SDK\Exception\BasketNotFound;
use Wizaplace\SDK\Exception\CouponCodeAlreadyApplied;
use Wizaplace\SDK\Order\OrderService;
use Wizaplace\SDK\Order\OrderStatus;
use Wizaplace\SDK\Tests\ApiTestCase;

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

                $availableShippings = $shippingGroup->getShippings();
                $shippings[$shippingGroup->getId()] = end($availableShippings)->getId();

                foreach ($shippingGroup->getItems() as $basketItem) {
                    // Here we mostly check the items were properly unserialized
                    $this->assertGreaterThan(0, $basketItem->getProductId());
                    $this->assertGreaterThan(0, $basketItem->getQuantity());
                    $this->assertNotEmpty($basketItem->getProductName());
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
            $this->assertNotEmpty($availablePayment->getName());
            $this->assertGreaterThan(0, $availablePayment->getId());
            $this->assertGreaterThanOrEqual(0, $availablePayment->getPosition());
        }
        $selectedPayment = reset($availablePayments)->getId();
        $redirectUrl = 'https://demo.loc/order/confirm';

        $paymentInformation = $basketService->checkout($basket->getId(), $selectedPayment, true, $redirectUrl);

        // @TODO : check that the two following values are normal
        $this->assertSame('', $paymentInformation->getHtml());
        $this->assertNull($paymentInformation->getRedirectUrl());

        $orders = $paymentInformation->getOrders();
        $this->assertCount(1, $orders);

        $order = $orderService->getOrder($orders[0]->getId());
        $this->assertSame($orders[0]->getId(), $order->getId());
        $this->assertSame(3, $order->getCompanyId());
        $this->assertSame('Colissmo', $order->getShippingName());
        $this->assertEquals(OrderStatus::STANDBY_BILLING(), $order->getStatus());
        $this->assertGreaterThan(1500000000, $order->getTimestamp()->getTimestamp());
        $this->assertSame(135.8, $order->getTotal());
        $this->assertSame(135.8, $order->getSubtotal());
        $this->assertSame('40 rue Laure Diebold', $order->getShippingAddress()->getAddress());

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
        $this->assertGreaterThan(strlen($pdfHeader), strlen($pdfContents));
    }

    public function testCheckoutWithRedirectUrl()
    {
        $apiClient = $this->buildApiClient();
        $apiClient->authenticate('customer-1@world-company.com', 'password-customer-1');
        $basketService = new BasketService($apiClient);
        $basket = $basketService->createEmptyBasket();
        $basketService->addProductToBasket($basket->getId(), new DeclinationId('1'), 1);
        $basket = $basketService->getBasket($basket->getId());
        $shippings = [];
        foreach ($basket->getCompanyGroups() as $companyGroup) {
            foreach ($companyGroup->getShippingGroups() as $shippingGroup) {
                $availableShippings = $shippingGroup->getShippings();
                $shippings[$shippingGroup->getId()] = end($availableShippings)->getId();
                foreach ($shippingGroup->getItems() as $basketItem) {
                    // Here we mostly check the items were properly unserialized
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
            $this->assertNotEmpty($availablePayment->getName());
            $this->assertGreaterThan(0, $availablePayment->getId());
            $this->assertGreaterThanOrEqual(0, $availablePayment->getPosition());
        }
        $selectedPayment = reset($availablePayments)->getId();
        $redirectUrl = 'https://demo.loc/order/confirm';
        $fakeCssUrl = 'https://fakeadress.com/style.css';

        $checkoutCommand = new CheckoutWithRedirectUrlCommand();
        $checkoutCommand->setBasketId($basket->getId());
        $checkoutCommand->setPaymentId($selectedPayment);
        $checkoutCommand->setAcceptTerms(true);
        $checkoutCommand->setCssUrl($fakeCssUrl);
        $checkoutCommand->setRedirectUrl($redirectUrl);

        $paymentInformation = $basketService->checkoutBasket($checkoutCommand);

        // @TODO : check that the two following values are normal
        $this->assertSame('', $paymentInformation->getHtml());
        $this->assertNull($paymentInformation->getRedirectUrl());

        $orders = $paymentInformation->getOrders();
        $this->assertCount(1, $orders);
    }

    public function testCheckoutWithPreauthToken()
    {
        $apiClient = $this->buildApiClient();
        $apiClient->authenticate('customer-1@world-company.com', 'password-customer-1');
        $basketService = new BasketService($apiClient);
        $basket = $basketService->createEmptyBasket();
        $basketService->addProductToBasket($basket->getId(), new DeclinationId('1'), 1);
        $basket = $basketService->getBasket($basket->getId());
        $shippings = [];
        foreach ($basket->getCompanyGroups() as $companyGroup) {
            foreach ($companyGroup->getShippingGroups() as $shippingGroup) {
                $availableShippings = $shippingGroup->getShippings();
                $shippings[$shippingGroup->getId()] = end($availableShippings)->getId();
                foreach ($shippingGroup->getItems() as $basketItem) {
                    // Here we mostly check the items were properly unserialized
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
            $this->assertNotEmpty($availablePayment->getName());
            $this->assertGreaterThan(0, $availablePayment->getId());
            $this->assertGreaterThanOrEqual(0, $availablePayment->getPosition());
        }
        $selectedPayment = reset($availablePayments)->getId();
        $fakePreauthToken = 'ThisIsAFakeToken';

        $checkoutCommand = new CheckoutWithPreAuthTokenCommand();
        $checkoutCommand->setBasketId($basket->getId());
        $checkoutCommand->setPaymentId($selectedPayment);
        $checkoutCommand->setAcceptTerms(true);
        $checkoutCommand->setPreauthToken($fakePreauthToken);

        $paymentInformation = $basketService->checkoutBasket($checkoutCommand);

        // @TODO : check that the two following values are normal
        $this->assertSame('', $paymentInformation->getHtml());
        $this->assertNull($paymentInformation->getRedirectUrl());

        $orders = $paymentInformation->getOrders();
        $this->assertCount(1, $orders);
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
        $basketService->selectShippings($basketId, [
            $shippingGroups[0]->getId() => $shippings[1]->getId(),
        ]);

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
        $this->assertSame(1, $basketService->addProductToBasket($basketId, new DeclinationId('3_8_7'), 1));

        $basket = $basketService->getBasket($basketId);

        $declinationOption = $basket->getCompanyGroups()[0]->getShippingGroups()[0]->getItems()[0]->getDeclinationOptions()[8];
        $this->assertSame(8, $declinationOption->getOptionId());
        $this->assertSame('size', $declinationOption->getOptionName());
        $this->assertSame(7, $declinationOption->getVariantId());
        $this->assertSame('13', $declinationOption->getVariantName());
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
        $this->assertSame(1, $basketService->addProductToBasket($basketId, new DeclinationId('3_8_7'), 1));
        $comments = [
            new ProductComment(new DeclinationId('3_8_7'), 'I will be available only during the afternoon'),
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
        $basketService->addProductToBasket($basketId, new DeclinationId('1_0'), 1);
        $basketService->addProductToBasket($basketId, new DeclinationId('3_8_7'), 1);

        $basketId2 = $basketService->create();
        $basketService->addProductToBasket($basketId2, new DeclinationId('1_0'), 2);
        $basketService->addProductToBasket($basketId2, new DeclinationId('3_8_8'), 1);

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

        $this->assertSame([
            '1_0' => 2,
            '3_8_7' => 1,
            '3_8_8' => 1,
        ], $quantitiesMap);

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

        $this->assertSame([
            '1_0' => 2,
            '3_8_8' => 1,
        ], $quantitiesMap);
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

    private function buildAuthenticatedBasketService(string $email = "admin@wizaplace.com", string $password = "password"): BasketService
    {
        $apiClient = $this->buildApiClient();
        $apiClient->authenticate($email, $password);

        return new BasketService($apiClient);
    }
}
