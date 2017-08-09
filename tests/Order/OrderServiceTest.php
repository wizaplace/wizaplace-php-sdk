<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\Tests\Order;

use Wizaplace\Authentication\AuthenticationRequired;
use Wizaplace\Order\CreateOrderReturn;
use Wizaplace\Order\OrderService;
use Wizaplace\Order\ReturnItem;
use Wizaplace\Tests\ApiTestCase;

/**
 * @see OrderService
 */
class OrderServiceTest extends ApiTestCase
{
    public function testGetOrder()
    {
        $order = $this->buildOrderService()->getOrder(1);

        $this->assertEquals(1, $order->getId());
    }

    public function testCreateOrderReturn()
    {
        $orderService = $this->buildOrderService();

        $creationCommand = new CreateOrderReturn(1, "Broken on arrival");
        $creationCommand->addItem('1_0', 1, 1);

        $returnId = $orderService->createOrderReturn($creationCommand);
        $this->assertGreaterThan(0, $returnId);

        $return = $orderService->getOrderReturn($returnId);
        $this->assertEquals($returnId, $return->getId());
        $this->assertEquals(1, $return->getOrderId());
        $this->assertEquals('R', $return->getStatus());
        $this->assertEquals('Broken on arrival', $return->getComments());
        $this->assertGreaterThan(1500000000, $return->getCreatedAt()->getTimestamp());
        $returnItems = $return->getItems();
        $this->assertCount(1, $returnItems);
        /** @var ReturnItem $returnItem */
        $returnItem = reset($returnItems);
        $this->assertEquals(1, $returnItem->getAmount());
        $this->assertEquals('1_0', $returnItem->getDeclinationId());
        $this->assertEquals(20.0, $returnItem->getPrice());
        $this->assertEquals('optio corporis similique voluptatum', $returnItem->getProductName());
        $this->assertEquals(1, $returnItem->getReason());
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

        $this->assertEquals(3, $returnTypes[0]->getId());
        $this->assertEquals('Colis arrivé en mauvais état, endommagé', $returnTypes[0]->getName());
        $this->assertEquals(10, $returnTypes[0]->getPosition());
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
