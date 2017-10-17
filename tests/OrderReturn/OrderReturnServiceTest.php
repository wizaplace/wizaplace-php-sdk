<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\SDK\Tests\OrderReturn;

use Wizaplace\SDK\Authentication\AuthenticationRequired;
use Wizaplace\SDK\OrderReturn\CreateOrderReturn;
use Wizaplace\SDK\OrderReturn\ReturnStatus;
use Wizaplace\SDK\OrderReturn\ReturnItem;
use Wizaplace\SDK\OrderReturn\OrderReturnService;
use Wizaplace\SDK\Tests\ApiTestCase;

/**
 * @see OrderReturnService
 */
final class OrderReturnServiceTest extends ApiTestCase
{
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
        $this->assertEquals(ReturnStatus::PROCESSING(), $return->getStatus());
        $this->assertSame('Broken on arrival', $return->getComments());
        $this->assertGreaterThan(1500000000, $return->getCreatedAt()->getTimestamp());
        $returnItems = $return->getItems();
        $this->assertCount(1, $returnItems);
        /** @var ReturnItem $returnItem */
        $returnItem = reset($returnItems);
        $this->assertSame(1, $returnItem->getAmount());
        $this->assertSame('1_0', $returnItem->getDeclinationId());
        $this->assertSame(67.9, $returnItem->getPrice());
        $this->assertSame('Z11 Plus Boîtier PC en Acier ATX', $returnItem->getProductName());
        $this->assertSame(1, $returnItem->getReason());

        $returns = $orderService->getOrderReturns();
        $this->assertCount(1, $returns);
        $this->assertEquals($return, $returns[0]);
    }
    public function testGetOrderReturnWithoutAuthentication()
    {
        $this->expectException(AuthenticationRequired::class);
        $this->buildOrderReturnServiceWithoutAuthentication()->getOrderReturn(1);
    }

    public function testGetOrderReturnsWithoutData()
    {
        $returns = $this->buildOrderService()->getOrderReturns();

        $this->assertCount(0, $returns);
    }

    public function testGetOrderReturnsWithoutAuthentication()
    {
        $this->expectException(AuthenticationRequired::class);
        $this->buildOrderReturnServiceWithoutAuthentication()->getOrderReturns();
    }

    public function testCreateOrderReturnWithoutAuthentication()
    {
        $this->expectException(AuthenticationRequired::class);
        $this->buildOrderReturnServiceWithoutAuthentication()->createOrderReturn(new CreateOrderReturn(1, ""));
    }

    public function testGetReturnReasons()
    {
        $returnTypes = $this->buildOrderReturnServiceWithoutAuthentication()->getReturnReasons();
        $this->assertCount(7, $returnTypes);

        $this->assertSame(3, $returnTypes[0]->getId());
        $this->assertSame('Colis arrivé en mauvais état, endommagé', $returnTypes[0]->getName());
        $this->assertSame(10, $returnTypes[0]->getPosition());
    }

    private function buildOrderService(): OrderReturnService
    {
        $apiClient = $this->buildApiClient();
        $apiClient->authenticate('customer-1@world-company.com', 'password-customer-1');

        return new OrderReturnService($apiClient);
    }

    private function buildOrderReturnServiceWithoutAuthentication(): OrderReturnService
    {
        return new OrderReturnService($this->buildApiClient());
    }
}
