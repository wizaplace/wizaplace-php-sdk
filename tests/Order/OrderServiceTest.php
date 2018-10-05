<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\SDK\Tests\Order;

use DateTimeImmutable;
use GuzzleHttp\Psr7\Response;
use Wizaplace\SDK\Authentication\AuthenticationRequired;
use Wizaplace\SDK\Catalog\DeclinationId;
use Wizaplace\SDK\Exception\NotFound;
use Wizaplace\SDK\Exception\OrderNotFound;
use Wizaplace\SDK\Order\AfterSalesServiceRequest;
use Wizaplace\SDK\Order\CreateOrderReturn;
use Wizaplace\SDK\Order\OrderCommitmentCommand;
use Wizaplace\SDK\Order\OrderReturnStatus;
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
        $order = $this->buildOrderService()->getOrder(2);

        $this->assertSame(2, $order->getId());
        $this->assertSame(3, $order->getCompanyId());
        $this->assertSame(108.4, $order->getTotal());
        $this->assertSame(108.4, $order->getSubtotal());
        $this->assertSame(2.23, $order->getTaxtotal());
        $this->assertEquals(OrderStatus::STANDBY_BILLING(), $order->getStatus());
        $this->assertSame('TNT Express', $order->getShippingName());
        $this->assertCount(1, $order->getOrderItems());
        $this->assertSame('', $order->getCustomerComment());

        $this->assertNotNull($order->getPayment());
        $this->assertSame('manual', $order->getPayment()->getType());
        $this->assertNull($order->getPayment()->getProcessorName());

        // Premier orderItem
        $firstItem = $order->getOrderItems()[0];
        $this->assertTrue((new DeclinationId('4_0'))->equals($firstItem->getDeclinationId()));
        $this->assertSame('Corsair Gaming VOID Pro RGB Dolby 7.1 Sans fil - Edition Carbon', $firstItem->getProductName());
        $this->assertSame('7531596248951', $firstItem->getProductCode());
        $this->assertSame(54.2, $firstItem->getPrice());
        $this->assertSame(2, $firstItem->getAmount());
        $this->assertCount(0, $firstItem->getDeclinationOptions());
    }

    public function testGetInexistingOrderYieldsAnError(): void
    {
        $this->expectException(OrderNotFound::class);
        $this->buildOrderService()->getOrder(404);
    }

    public function testCreateOrderReturn()
    {
        $orderService = $this->buildOrderService();

        $creationCommand = new CreateOrderReturn(1, "Broken on arrival");
        $creationCommand->addItem(new DeclinationId('1_0'), 1, 1);

        $returnId = $orderService->createOrderReturn($creationCommand);
        $this->assertGreaterThan(0, $returnId);

        $return = $orderService->getOrderReturn($returnId);
        $this->assertSame($returnId, $return->getId());
        $this->assertSame(1, $return->getOrderId());
        $this->assertTrue(OrderReturnStatus::PROCESSING()->equals($return->getStatus()));
        $this->assertSame('Broken on arrival', $return->getComments());
        $this->assertGreaterThan(1500000000, $return->getCreatedAt()->getTimestamp());
        $returnItems = $return->getItems();
        $this->assertCount(1, $returnItems);
        /** @var ReturnItem $returnItem */
        $returnItem = reset($returnItems);
        $this->assertSame(1, $returnItem->getAmount());
        $this->assertTrue((new DeclinationId('1_0'))->equals($returnItem->getDeclinationId()));
        $this->assertSame(67.9, $returnItem->getPrice());
        $this->assertSame('Z11 Plus Boîtier PC en Acier ATX', $returnItem->getProductName());
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

    public function testSendingAnAfterSalesServiceRequest()
    {
        $request = (new AfterSalesServiceRequest())
            ->setOrderId(4)
            ->setComments("Help please :(")
            ->setItemsDeclinationsIds(['1_0']);

        $this->buildOrderService()->sendAfterSalesServiceRequest($request);

        // sadly we can't check any data, so we just check that 2 request were made (one for authentication, one for the SAV request)
        $this->assertCount(2, static::$historyContainer);
        $this->assertSame('/api/v1/user/orders/4/after-sales', static::$historyContainer[1]['request']->getUri()->getPath());
    }

    public function testSendingAnAfterSalesServiceRequestWithoutAuthentication()
    {
        $request = (new AfterSalesServiceRequest())
            ->setOrderId(4)
            ->setComments("Help please :(")
            ->setItemsDeclinationsIds(['1_0']);

        $this->expectException(AuthenticationRequired::class);
        $this->buildOrderServiceWithoutAuthentication()->sendAfterSalesServiceRequest($request);
    }

    public function testGetOrderWithComment()
    {
        $order = $this->buildOrderService()->getOrder(4);

        $this->assertSame(4, $order->getId());
        $this->assertSame(3, $order->getCompanyId());
        $this->assertSame(67.9, $order->getTotal());
        $this->assertSame(67.9, $order->getSubtotal());
        $this->assertSame(1.4, $order->getTaxtotal());
        $this->assertEquals(OrderStatus::COMPLETED(), $order->getStatus());
        $this->assertSame('TNT Express', $order->getShippingName());
        $this->assertCount(1, $order->getOrderItems());
        $this->assertSame('Please deliver at the front desk of my company.', $order->getCustomerComment());

        // Premier orderItem
        $item = $order->getOrderItems()[0];
        $this->assertTrue((new DeclinationId('1_0'))->equals($item->getDeclinationId()));
        $this->assertSame('Z11 Plus Boîtier PC en Acier ATX', $item->getProductName());
        $this->assertSame('978020137962', $item->getProductCode());
        $this->assertSame(67.9, $item->getPrice());
        $this->assertSame(1, $item->getAmount());
        $this->assertCount(0, $item->getDeclinationOptions());
        $this->assertSame('Please, gift wrap this product.', $item->getCustomerComment());
    }

    public function testGetOrdersWhichReturnsCompanyName(): array
    {
        $apiClient = $this->buildApiClient();
        $apiClient->authenticate('customer-1@world-company.com', 'password-customer-1');
        $orderService = new OrderService($apiClient);
        $orders = $orderService->getOrders();

        $this->assertSame('The World Company Inc.', $orders[0]->getCompanyName());
        $this->assertSame('The World Company Inc.', $orders[1]->getCompanyName());
        $this->assertSame('The World Company Inc.', $orders[3]->getCompanyName());

        return $orders;
    }

    public function testCommitOrder()
    {
        $orderService = $this->buildOrderService();

        static::$historyContainer = [];

        $commitCommand = new OrderCommitmentCommand(6, new DateTimeImmutable('2018-01-01'), 'ABC123');
        $orderService->commitOrder($commitCommand);

        // We don't have a way to check that the order request was processed.
        // So we just check that an HTTP request was made successfully
        $this->assertCount(1, static::$historyContainer);
        /** @var Response $response */
        $response = static::$historyContainer[0]['response'];
        $this->assertSame(204, $response->getStatusCode());
    }

    public function testCommitOrderWhichDoesNotExist()
    {
        $commitCommand = new OrderCommitmentCommand(404, new DateTimeImmutable('2018-01-01'), '404');

        $this->expectException(NotFound::class);
        $this->buildOrderService()->commitOrder($commitCommand);
    }

    private function buildOrderService(): OrderService
    {
        $apiClient = $this->buildApiClient();
        $apiClient->authenticate('customer-1@world-company.com', 'password-customer-1');

        return new OrderService($apiClient);
    }

    private function buildOrderServiceWithoutAuthentication(): OrderService
    {
        return new OrderService($this->buildApiClient());
    }
}
