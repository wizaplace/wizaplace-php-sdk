<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\SDK\Tests\Basket;

use Wizaplace\SDK\Basket\ProductComment;
use Wizaplace\SDK\Basket\BasketService;
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

        $basketId = $basketService->create();
        $this->assertNotEmpty($basketId);

        $newQuantity = $basketService->addProductToBasket($basketId, '1', 1);
        $this->assertSame(1, $newQuantity);

        $newQuantity = $basketService->addProductToBasket($basketId, '1', 1);
        $this->assertSame(2, $newQuantity);

        $basket = $basketService->getBasket($basketId);
        $this->assertNotNull($basket);

        $shippings = [];
        foreach ($basket->getCompanyGroups() as $companyGroup) {
            $this->assertGreaterThan(0, $companyGroup->getCompany()->getId());
            $this->assertNotEmpty($companyGroup->getCompany()->getName());
            $this->assertNotEmpty($companyGroup->getCompany()->getSlug());

            foreach ($companyGroup->getShippingGroups() as $shippingGroup) {
                $this->assertGreaterThan(0, $shippingGroup->getId());

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
                    $this->assertGreaterThanOrEqual($basketItem->getIndividualPrice(), $basketItem->getTotal());
                    $basketItem->getMainImage();
                    $basketItem->getCrossedOutPrice();
                }
            }
        }
        $basketService->selectShippings($basketId, $shippings);

        $availablePayments = $basketService->getPayments($basketId);
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

        $paymentInformation = $basketService->checkout($basketId, $selectedPayment, true, $redirectUrl);

        // @TODO : check that the two following values are normal
        $this->assertSame('', $paymentInformation->getHtml());
        $this->assertSame('', $paymentInformation->getRedirectUrl());

        $orders = $paymentInformation->getOrders();
        $this->assertCount(1, $orders);

        $order = $orderService->getOrder($orders[0]['id']);
        $this->assertSame($orders[0]['id'], $order->getId());
        $this->assertSame(3, $order->getCompanyId());
        $this->assertSame('Colissmo', $order->getShippingName());
        $this->assertEquals(OrderStatus::STANDBY_BILLING(), $order->getStatus());
        $this->assertGreaterThan(1500000000, $order->getTimestamp()->getTimestamp());
        $this->assertSame(135.8, $order->getTotal());
        $this->assertSame(135.8, $order->getSubtotal());
        $this->assertSame('40 rue Laure Diebold', $order->getShippingAddress()->getAddress());

        $orderItems = $order->getOrderItems();
        $this->assertCount(1, $orderItems);
        $this->assertSame('1_0', $orderItems[0]->getDeclinationId());
        $this->assertSame('Z11 Plus Boîtier PC en Acier ATX', $orderItems[0]->getProductName());
        $this->assertSame('978020137962', $orderItems[0]->getProductCode());
        $this->assertSame(67.9, $orderItems[0]->getPrice());
        $this->assertSame(2, $orderItems[0]->getAmount());
    }

    public function testCleanBasket()
    {
        $apiClient = $this->buildApiClient();
        $apiClient->authenticate('customer-1@world-company.com', 'password-customer-1');

        $basketService = new BasketService($apiClient);

        $basketId = $basketService->create();
        $this->assertNotEmpty($basketId);

        $basketService->addProductToBasket($basketId, '1', 1);

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

    public function testSelectingShippings()
    {
        // Arrange
        $basketService = $this->buildAuthenticatedBasketService();

        $basketId = $basketService->create();
        $this->assertNotEmpty($basketId);

        $basketService->addProductToBasket($basketId, '5', 1);

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
        $this->assertSame(1, $basketService->addProductToBasket($basketId, '3_8_7', 1));

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
    }

    public function testAddCommentToProduct()
    {
        $basketService = $this->buildAuthenticatedBasketService();

        $basketId = $basketService->create();
        $this->assertSame(1, $basketService->addProductToBasket($basketId, '3_8_7', 1));
        $comments = [
            new ProductComment('3_8_7', 'I will be available only during the afternoon'),
        ];
        $basketService->updateComments($basketId, $comments);
    }

    public function testAddCommentToProductWithWrongDeclinationId()
    {
        $this->expectExceptionCode(400);
        $this->expectExceptionMessage('Missing declination Id');

        $basketService = $this->buildAuthenticatedBasketService();

        $basketId = $basketService->create();
        $this->assertSame(1, $basketService->addProductToBasket($basketId, '3_8_7', 1));
        $comments = [
            new ProductComment('', 'I will be available only during the afternoon'),
        ];
        $basketService->updateComments($basketId, $comments);
    }

    public function testAddCommentToProductWithEmptyComment()
    {
        $this->expectExceptionCode(400);
        $this->expectExceptionMessage('Missing comment');

        $basketService = $this->buildAuthenticatedBasketService();

        $basketId = $basketService->create();
        $this->assertSame(1, $basketService->addProductToBasket($basketId, '3_8_7', 1));
        $comments = [
            new ProductComment('3_8_7', ''),
        ];
        $basketService->updateComments($basketId, $comments);
    }

    public function testAddCommentToProductAbsentOfBasket()
    {
        $this->expectExceptionCode(400);
        $this->expectExceptionMessage('This product is not in the basket. Impossible to add a comment.');

        $basketService = $this->buildAuthenticatedBasketService();

        $basketId = $basketService->create();
        $comments = [
            new ProductComment('3_8_7', 'I will be available only during the afternoon'),
        ];
        $basketService->updateComments($basketId, $comments);
    }

    private function buildAuthenticatedBasketService(string $email = "admin@wizaplace.com", string $password = "password"): BasketService
    {
        $apiClient = $this->buildApiClient();
        $apiClient->authenticate($email, $password);

        return new BasketService($apiClient);
    }
}
