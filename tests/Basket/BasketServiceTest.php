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
class BasketServiceTest extends ApiTestCase
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
        $this->assertEquals(1, $newQuantity);

        $newQuantity = $basketService->addProductToBasket($basketId, '1', 1);
        $this->assertEquals(2, $newQuantity);

        $basket = $basketService->getBasket($basketId);
        $this->assertNotNull($basket);

        $shippings = [];
        foreach ($basket->getCompanyGroups() as $companyGroup) {
            foreach ($companyGroup->getShippingGroups() as $shippingGroup) {
                $availableShippings = $shippingGroup->getShippings();
                $shippings[$shippingGroup->getId()] = end($availableShippings)->getId();
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
        $orders = $paymentInformation->getOrders();
        $this->assertCount(1, $orders);

        $order = $orderService->getOrder($orders[0]['id']);
        $this->assertEquals($orders[0]['id'], $order->getId());
        $this->assertCount(1, $order->getOrderItem());
        $this->assertEquals('TNT Express', $order->getShippingName());
        $this->assertEquals('STANDBY_BILLING', $order->getStatus());
        $this->assertEquals(40.0, $order->getTotal());
        $this->assertEquals(40.0, $order->getSubtotal());
        $this->assertEquals('40 rue Laure Diebold', $order->getShippingAddress()->getAddress());
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
