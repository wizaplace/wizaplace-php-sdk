<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\SDK\Tests\Order;

use Wizaplace\SDK\Authentication\AuthenticationRequired;
use Wizaplace\SDK\Order\AfterSalesServiceRequest;
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
        $order = $this->buildOrderService()->getOrder(2);

        $this->assertSame(2, $order->getId());
        $this->assertSame(3, $order->getCompanyId());
        $this->assertSame(35.3, $order->getTotal());
        $this->assertSame(35.3, $order->getSubtotal());
        $this->assertEquals(OrderStatus::STANDBY_BILLING(), $order->getStatus());
        $this->assertSame('Lettre prioritaire', $order->getShippingName());
        $this->assertCount(2, $order->getOrderItems());

        // Premier orderItem
        $firstItem = $order->getOrderItems()[0];
        $this->assertSame('2_6_4_7_6', $firstItem->getDeclinationId());
        $this->assertSame('Souris sans fil avec récepteur nano 6 boutons', $firstItem->getProductName());
        $this->assertSame('color_red_connectivity_wired', $firstItem->getProductCode());
        $this->assertSame(15.5, $firstItem->getPrice());
        $this->assertSame(1, $firstItem->getAmount());
        $this->assertCount(2, $firstItem->getDeclinationOptions());

        $this->assertSame(6, $firstItem->getDeclinationOptions()[0]->getOptionId());
        $this->assertSame('color', $firstItem->getDeclinationOptions()[0]->getOptionName());
        $this->assertSame(4, $firstItem->getDeclinationOptions()[0]->getVariantId());
        $this->assertSame('red', $firstItem->getDeclinationOptions()[0]->getVariantName());

        $this->assertSame(7, $firstItem->getDeclinationOptions()[1]->getOptionId());
        $this->assertSame('connectivity', $firstItem->getDeclinationOptions()[1]->getOptionName());
        $this->assertSame(6, $firstItem->getDeclinationOptions()[1]->getVariantId());
        $this->assertSame('wired', $firstItem->getDeclinationOptions()[1]->getVariantName());
        $this->assertSame('', $firstItem->getCustomerComment());

        // Deuxième orderItem
        $secondItem = $order->getOrderItems()[1];
        $this->assertSame('4_0', $secondItem->getDeclinationId());
        $this->assertSame('Product with shippings', $secondItem->getProductName());
        $this->assertSame('0493020427963', $secondItem->getProductCode());
        $this->assertSame(9.9, $secondItem->getPrice());
        $this->assertSame(2, $secondItem->getAmount());
        $this->assertCount(0, $secondItem->getDeclinationOptions());
        $this->assertSame('', $secondItem->getCustomerComment());
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
        $order = $this->buildOrderService()->getOrder(5);

        $this->assertSame(5, $order->getId());
        $this->assertSame(3, $order->getCompanyId());
        $this->assertSame(67.9, $order->getTotal());
        $this->assertSame(67.9, $order->getSubtotal());
        $this->assertEquals(OrderStatus::STANDBY_BILLING(), $order->getStatus());
        $this->assertSame('TNT Express', $order->getShippingName());
        $this->assertCount(1, $order->getOrderItems());

        // Premier orderItem
        $item = $order->getOrderItems()[0];
        $this->assertSame('1_0', $item->getDeclinationId());
        $this->assertSame('Z11 Plus Boîtier PC en Acier ATX', $item->getProductName());
        $this->assertSame('978020137962', $item->getProductCode());
        $this->assertSame(67.9, $item->getPrice());
        $this->assertSame(1, $item->getAmount());
        $this->assertCount(0, $item->getDeclinationOptions());
        $this->assertSame('Please, gift wrap this product.', $item->getCustomerComment());
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
