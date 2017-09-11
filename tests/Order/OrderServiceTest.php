<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\SDK\Tests\Order;

use Wizaplace\SDK\Authentication\AuthenticationRequired;
use Wizaplace\SDK\Order\CreateOrderReturn;
use Wizaplace\SDK\Order\OrderService;
use Wizaplace\SDK\Order\OrderStatus;
use Wizaplace\SDK\Order\ReturnItem;
use Wizaplace\SDK\Tests\ApiTestCase;

/**
 * @see OrderService
 */
final class OrderServiceTest extends ApiTestCase
{
    public function testGetOrder()
    {
        $order = $this->buildOrderService()->getOrder(1);

        $this->assertSame(1, $order->getId());
        $this->assertEquals(OrderStatus::STANDBY_BILLING(), $order->getStatus());
    }

    public function testCreateOrderReturn()
    {
        $orderService = $this->buildOrderService();

        $creationCommand = new CreateOrderReturn(1, "Broken on arrival");
        $creationCommand->addItem('1_0', 1, 1);

        $returnId = $orderService->createOrderReturn($creationCommand);
        $this->assertGreaterThan(0, $returnId);

        $return = $orderService->getOrderReturn($returnId);
        $this->assertSame($returnId, $return->getId());
        $this->assertSame(1, $return->getOrderId());
        $this->assertSame('R', $return->getStatus());
        $this->assertSame('Broken on arrival', $return->getComments());
        $this->assertGreaterThan(1500000000, $return->getCreatedAt()->getTimestamp());
        $returnItems = $return->getItems();
        $this->assertCount(1, $returnItems);
        /** @var ReturnItem $returnItem */
        $returnItem = reset($returnItems);
        $this->assertSame(1, $returnItem->getAmount());
        $this->assertSame('1_0', $returnItem->getDeclinationId());
        $this->assertSame(20.0, $returnItem->getPrice());
        $this->assertSame('optio corporis similique voluptatum', $returnItem->getProductName());
        $this->assertSame(1, $returnItem->getReason());

        $returns = $orderService->getOrderReturns();
        $this->assertCount(1, $returns);
        $this->assertEquals($return, $returns[0]);
    }

    public function testGetOrdersWithoutAuthentication()
    {
        $this->expectException(AuthenticationRequired::class);
        $this->buildOrderServiceWithoutAuthentication()->getOrders();
    }

    public function testGetOrderWithoutAuthentication()
    {
        $this->expectException(AuthenticationRequired::class);
        $this->buildOrderServiceWithoutAuthentication()->getOrder(1);
    }

    public function testGetOrderReturnWithoutAuthentication()
    {
        $this->expectException(AuthenticationRequired::class);
        $this->buildOrderServiceWithoutAuthentication()->getOrderReturn(1);
    }

    public function testGetOrderReturnsWithoutData()
    {
        $returns = $this->buildOrderService()->getOrderReturns();

        $this->assertCount(0, $returns);
    }

    public function testGetOrderReturnsWithoutAuthentication()
    {
        $this->expectException(AuthenticationRequired::class);
        $this->buildOrderServiceWithoutAuthentication()->getOrderReturns();
    }

    public function testCreateOrderReturnWithoutAuthentication()
    {
        $this->expectException(AuthenticationRequired::class);
        $this->buildOrderServiceWithoutAuthentication()->createOrderReturn(new CreateOrderReturn(1, ""));
    }

    public function testGetReturnReasons()
    {
        $returnTypes = $this->buildOrderServiceWithoutAuthentication()->getReturnReasons();
        $this->assertCount(7, $returnTypes);

        $this->assertSame(3, $returnTypes[0]->getId());
        $this->assertSame('Colis arrivé en mauvais état, endommagé', $returnTypes[0]->getName());
        $this->assertSame(10, $returnTypes[0]->getPosition());
    }

    private function buildOrderService(): OrderService
    {
        $apiClient = $this->buildApiClient();
        $apiClient->authenticate('user@wizaplace.com', 'password');

        return new OrderService($apiClient);
    }

    private function buildOrderServiceWithoutAuthentication(): OrderService
    {
        return new OrderService($this->buildApiClient());
    }
}
