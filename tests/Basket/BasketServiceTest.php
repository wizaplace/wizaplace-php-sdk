<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\Tests\Basket;

use Wizaplace\Basket\BasketService;
use Wizaplace\Order\OrderService;
use Wizaplace\Tests\ApiTestCase;

/**
 * @see BasketService
 */
final class BasketServiceTest extends ApiTestCase
{
    public function testFullCheckout()
    {
        $apiClient = $this->buildApiClient();
        $apiClient->authenticate('user@wizaplace.com', 'password');

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

            foreach ($companyGroup->getShippingGroups() as $shippingGroup) {
                $this->assertGreaterThan(0, $shippingGroup->getId());

                $availableShippings = $shippingGroup->getShippings();
                $shippings[$shippingGroup->getId()] = end($availableShippings)->getId();

                foreach ($shippingGroup->getItems() as $basketItem) {
                    // Here we mostly check the items were properly unserialized
                    $this->assertGreaterThan(0, $basketItem->getProductId());
                    $this->assertGreaterThan(0, $basketItem->getQuantity());
                    $this->assertNotEmpty($basketItem->getProductName());
                    $this->assertNotEmpty($basketItem->getProductUrl());
                    $this->assertNotEmpty($basketItem->getDeclinationId());
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
        $this->assertSame(4, $order->getCompanyId());
        $this->assertSame('TNT Express', $order->getShippingName());
        $this->assertSame('STANDBY_BILLING', $order->getStatus());
        $this->assertGreaterThan(1500000000, $order->getTimestamp()->getTimestamp());
        $this->assertSame(40.0, $order->getTotal());
        $this->assertSame(40.0, $order->getSubtotal());
        $this->assertSame('40 rue Laure Diebold', $order->getShippingAddress()->getAddress());

        $orderItems = $order->getOrderItems();
        $this->assertCount(1, $orderItems);
        $this->assertSame('1_0', $orderItems[0]->getDeclinationId());
        $this->assertSame('optio corporis similique voluptatum', $orderItems[0]->getProductName());
        $this->assertSame('6086375420678', $orderItems[0]->getProductCode());
        $this->assertSame(20.0, $orderItems[0]->getPrice());
        $this->assertSame(2, $orderItems[0]->getAmount());
    }

    public function testCleanBasket()
    {
        $apiClient = $this->buildApiClient();
        $apiClient->authenticate('user@wizaplace.com', 'password');

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
        $this->assertCount(2, $shippings);
        $this->assertSame('Colissmo', $shippings[1]->getName());
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
        $this->assertCount(2, $shippings);

        $this->assertFalse($shippings[0]->isSelected());
        $this->assertTrue($shippings[1]->isSelected());
    }

    private function buildAuthenticatedBasketService(string $email = "admin@wizaplace.com", string $password = "password"): BasketService
    {
        $apiClient = $this->buildApiClient();
        $apiClient->authenticate($email, $password);

        return new BasketService($apiClient);
    }
}
