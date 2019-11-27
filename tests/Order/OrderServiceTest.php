<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\SDK\Tests\Order;

use DateTimeImmutable;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Response;
use Wizaplace\SDK\Authentication\AuthenticationRequired;
use Wizaplace\SDK\Catalog\DeclinationId;
use Wizaplace\SDK\Exception\NotFound;
use Wizaplace\SDK\Exception\OrderNotCancellable;
use Wizaplace\SDK\Exception\OrderNotFound;
use Wizaplace\SDK\Order\AfterSalesServiceRequest;
use Wizaplace\SDK\Order\CreateOrderReturn;
use Wizaplace\SDK\Order\OrderAdjustment;
use Wizaplace\SDK\Order\OrderCommitmentCommand;
use Wizaplace\SDK\Order\OrderReturnStatus;
use Wizaplace\SDK\Order\OrderService;
use Wizaplace\SDK\Order\OrderStatus;
use Wizaplace\SDK\Order\Payment;
use Wizaplace\SDK\Order\RefundRequest;
use Wizaplace\SDK\Order\RefundRequestItem;
use Wizaplace\SDK\Order\RefundRequestShipping;
use Wizaplace\SDK\Order\RefundStatus;
use Wizaplace\SDK\Order\ReturnItem;
use Wizaplace\SDK\Tests\ApiTestCase;
use Wizaplace\SDK\Vendor\Order\OrderService as VendorOrderService;
use Wizaplace\SDK\Vendor\Order\OrderStatus as VendorOrderStatus;

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
        static::assertSame(0.0, $order->getMarketplaceDiscountTotal());
        static::assertSame(108.4, $order->getCustomerTotal());

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
        $this->assertSame("CORSAIR-CASQUE-GAMING", $firstItem->getSupplierRef());
    }

    public function testGetInexistingOrderYieldsAnError(): void
    {
        $this->expectException(OrderNotFound::class);
        $this->buildOrderService()->getOrder(404);
    }

    public function testGetShippingCostAndDiscount(): void
    {
        $order = $this->buildOrderService("lorene+admin@wizaplace.com", "azerty")->getOrder(854);

        $this->assertSame(27.6, $order->getShippingCost());
        $this->assertSame(9.33, $order->getDiscount());
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
        $this->assertSame("INFO-001", $item->getSupplierRef());
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

    public function testGetOrdersWhichReturnsProductImageId()
    {
        $apiClient = $this->buildApiClient();
        $apiClient->authenticate('customer-1@world-company.com', 'password-customer-1');
        $orderService = new OrderService($apiClient);
        $order1 = $orderService->getOrder(1);
        $order2 = $orderService->getOrder(4);

        $this->assertSame(null, $order1->getOrderItems()[0]->getProductImageId());
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

    public function testGetOrderWithCommitmentDate()
    {
        $orderService = $this->buildOrderService();
        $order = $orderService->getOrder(6);

        $order->getPayment();
        $this->assertSame(6, $order->getId());
        $this->assertEquals(OrderStatus::STANDBY_BILLING(), $order->getStatus());
        $this->assertNotEmpty($order->getPayment());

        //Check infos in Payment
        $payment = $order->getPayment();
        $date = '2018-11-30';
        $this->assertInstanceOf(DateTimeImmutable::class, $payment->getCommitmentDate());
        $this->assertEquals($date, $payment->getCommitmentDate()->format('Y-m-d'));
    }

    public function testGetOrderPayment()
    {
        $orderService = $this->buildOrderService('admin@wizaplace.com', 'password');
        $payment = $orderService->getPayment(6);

        static::assertInstanceOf(Payment::class, $payment);
        static::assertSame("payment-deferment", $payment->getType());
        static::assertSame("stripe", $payment->getProcessorName());
        static::assertNull($payment->getCommitmentDate());
        static::assertEmpty($payment->getProcessorInformations());
        static::assertNull($payment->getExternalReference());
    }

    public function testCreateOrderAdjustment(): void
    {
        $this->buildOrderService('admin@wizaplace.com', 'password')->createOrderAdjustment(10, 3230927120, 10);
        static::assertSame(9.01, $this->buildOrderService()->getOrder(10)->getTotal());
    }

    public function testGetAdjustments(): void
    {
        $orderService = $this->buildOrderService('admin@wizaplace.com', 'password');
        $adjustments = $orderService->getAdjustments(1);
        /** @var OrderAdjustment $adjustment */
        $adjustment = current($adjustments);
        static::assertInternalType('array', $adjustments);
        static::assertCount(1, $adjustments);
        static::assertSame(2085640488, $adjustment->getItemId());
        static::assertSame(70.9, $adjustment->getOldPriceWithoutTaxes());
        static::assertSame(67.9, $adjustment->getNewPriceWithoutTaxes());
        static::assertSame(70.9, $adjustment->getOldTotalIncludingTaxes());
        static::assertSame(67.9, $adjustment->getNewTotalIncludingTaxes());
        static::assertSame(7, $adjustment->getCreatedBy());
        static::assertInstanceOf(\DateTime::class, $adjustment->getCreatedAt());
    }

    public function testGetOrderRefunds(): void
    {
        $orderService = $this->buildOrderService('admin@wizaplace.com', 'password');
        $refunds = $orderService->getOrderRefunds(20);

        static::assertCount(1, $refunds);

        $refund = $refunds[0];

        static::assertSame(1, $refund->getRefundId());
        static::assertSame(20, $refund->getOrderId());
        static::assertSame(false, $refund->isPartial());
        static::assertSame(true, $refund->hasShipping());
        static::assertSame(15.5, $refund->getAmount());
        static::assertSame(0., $refund->getShippingAmount());
        static::assertEquals(RefundStatus::CREATED(), $refund->getStatus());
        static::assertNull($refund->getMessage());
        static::assertEquals(new \DateTime('2019-11-04T13:57:20+01:00'), $refund->getCreatedAt());
        static::assertEquals(new \DateTime('2019-11-04T13:57:20+01:00'), $refund->getUpdatedAt());
        static::assertCount(1, $refund->getItems());

        $item = $refund->getItems()[0];

        static::assertSame(1559389775, $item->getItemId());
        static::assertSame(15.5, $item->getAmount());
        static::assertSame(1, $item->getQuantity());
    }

    public function testPostOrderCancels(): void
    {
        $orderService = $this->buildOrderService('admin@wizaplace.com', 'password');
        $vendorOrderService = $this->buildVendorOrderService('admin@wizaplace.com', 'password');
        $order = $vendorOrderService->getOrderById(19);

        $orderService->cancelOrder(19);

        $order = $vendorOrderService->getOrderById(19);

        static::assertEquals(VendorOrderStatus::CANCELED(), $order->getStatus());
    }

    public function testPostOrderCancelsCantCancel(): void
    {
        static::expectException(OrderNotCancellable::class);

        $orderService = $this->buildOrderService('admin@wizaplace.com', 'password');

        $orderService->cancelOrder(13);
    }

    public function testPostOrderCancelsNotFound(): void
    {
        static::expectException(NotFound::class);

        $orderService = $this->buildOrderService('admin@wizaplace.com', 'password');

        $orderService->cancelOrder(4756687);
    }

    public function testPostOrderCancelsForbidden(): void
    {
        static::expectException(ClientException::class);
        static::expectExceptionCode(403);

        $orderService = $this->buildOrderService('vendor@wizaplace.com', 'password');

        $orderService->cancelOrder(13);
    }

    public function testPostOrderRefundComplete(): void
    {
        $orderService = $this->buildOrderService('admin@wizaplace.com', 'password');
        $request = new RefundRequest(false);

        $refund = $orderService->postRefundOrder(29, $request);

        static::assertSame(3, $refund->getRefundId());
        static::assertSame(29, $refund->getOrderId());
        static::assertSame(false, $refund->isPartial());
        static::assertSame(true, $refund->hasShipping());
        static::assertSame(97., $refund->getAmount());
        static::assertSame(0., $refund->getShippingAmount());
        static::assertTrue(RefundStatus::CREATED()->equals($refund->getStatus()));
        static::assertNull($refund->getMessage());
        static::assertEquals(new \DateTime('2019-11-07T10:31:40+0100'), $refund->getCreatedAt());
        static::assertEquals(new \DateTime('2019-11-07T10:31:40+0100'), $refund->getUpdatedAt());
        static::assertCount(1, $refund->getItems());

        $item = $refund->getItems()[0];

        static::assertSame(4261345357, $item->getItemId());
        static::assertSame(97., $item->getAmount());
        static::assertSame(1, $item->getQuantity());
    }

    public function testPostOrderRefundPartial(): void
    {
        $orderService = $this->buildOrderService('admin@wizaplace.com', 'password');
        $item = new RefundRequestItem(2858825212, 2);
        $shipping = new RefundRequestShipping(false);
        $request = new RefundRequest(true, [$item], $shipping, 'NEIN!');

        $refund = $orderService->postRefundOrder(32, $request);

        static::assertSame(4, $refund->getRefundId());
        static::assertSame(32, $refund->getOrderId());
        static::assertSame(true, $refund->isPartial());
        static::assertSame(false, $refund->hasShipping());
        static::assertSame(44., $refund->getAmount());
        static::assertSame(0., $refund->getShippingAmount());
        static::assertTrue(RefundStatus::CREATED()->equals($refund->getStatus()));
        static::assertSame('NEIN!', $refund->getMessage());
        static::assertEquals(new \DateTime('2019-11-07T10:48:41+0100'), $refund->getCreatedAt());
        static::assertEquals(new \DateTime('2019-11-07T10:48:41+0100'), $refund->getUpdatedAt());
        static::assertCount(1, $refund->getItems());

        $item = $refund->getItems()[0];

        static::assertSame(2858825212, $item->getItemId());
        static::assertSame(22., $item->getAmount());
        static::assertSame(2, $item->getQuantity());
    }

    private function buildOrderService(string $email = 'customer-1@world-company.com', $password = 'password-customer-1'): OrderService
    {
        $apiClient = $this->buildApiClient();
        $apiClient->authenticate($email, $password);

        return new OrderService($apiClient);
    }

    private function buildVendorOrderService(
        string $email = 'customer-1@world-company.com',
        string $password = 'password-customer-1'
    ): VendorOrderService {
        $apiClient = $this->buildApiClient();
        $apiClient->authenticate($email, $password);

        return new VendorOrderService($apiClient);
    }

    private function buildOrderServiceWithoutAuthentication(): OrderService
    {
        return new OrderService($this->buildApiClient());
    }
}
