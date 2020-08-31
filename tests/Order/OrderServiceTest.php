<?php

/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */

declare(strict_types=1);

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
use Wizaplace\SDK\Pim\Option\SystemOption;
use Wizaplace\SDK\Subscription\SubscriptionSummary;
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

    public function testGetOrderCarriagePaid(): void
    {
        $order = $this->buildOrderService()->getOrder(15);
        static::assertTrue($order->isCarriagePaid());
    }

    public function testGetOrderWithMaxPriceAdjustment()
    {
        $order = $this->buildOrderService()->getOrder(10);

        static::assertSame(10, $order->getId());

        // Premier orderItem
        $firstItem = $order->getOrderItems()[0];
        static::assertTrue((new DeclinationId('2_3_34_4_38'))->equals($firstItem->getDeclinationId()));
        static::assertSame('Souris sans fil avec récepteur nano 6 boutons', $firstItem->getProductName());
        static::assertSame('color_white_connectivity_wireles', $firstItem->getProductCode());
        static::assertSame(15.5, $firstItem->getPrice());
        static::assertSame(1, $firstItem->getAmount());
        static::assertSame("INFO-002", $firstItem->getSupplierRef());
        static::assertSame(50, $firstItem->getMaxPriceAdjustment());
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
        $creationCommand->addItem(new DeclinationId('1_0'), 3, 1);

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
        $this->assertSame(3, $returnItem->getReason());
        $this->assertSame("INFO-001", $returnItem->getSupplierRef());

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

    public function testGetUserOrdersWithSubscriptionId(): void
    {
        $orderService = $this->buildOrderService('user@wizaplace.com', 'password');
        $orders = $orderService->getOrders();

        static::assertCount(1, $orders);
        static::assertUuid($orders[0]->getSubscriptionId());
        static::assertFalse($orders[0]->isSubscriptionInitiator());
    }

    public function testGetUserOrderWithSubscriptionId(): void
    {
        $orderService = $this->buildOrderService('user@wizaplace.com', 'password');
        $order = $orderService->getOrder(130000);

        static::assertUuid($order->getSubscriptionId());
        static::assertFalse($order->isSubscriptionInitiator());
    }

    public function testGetUserSubscriptions(): void
    {
        $subscriptions = $this->buildOrderService("user@wizaplace.com", "password")->getSubscriptions(210011);

        static::assertCount(2, $subscriptions);
        static::assertInstanceOf(SubscriptionSummary::class, $subscriptions[0]);
        static::assertInstanceOf(SubscriptionSummary::class, $subscriptions[1]);
    }

    public function testNewOrderItemProperties(): void
    {
        $order = $this->buildOrderService()->getOrder(2);
        $firstItem = $order->getOrderItems()[0];

        static::assertSame('19377517', $firstItem->getItemId());
        static::assertFalse($firstItem->IsSubscription());
        static::assertFalse($firstItem->IsRenewable());
    }

    public function testGetOrdersWithSortAndPagination(): void
    {
        $apiClient = $this->buildApiClient();
        $apiClient->authenticate('customer-1@world-company.com', 'password-customer-1');
        $orderService = new OrderService($apiClient);

        $orders = $orderService->getOrders(["id" => "desc"]);
        static::assertSame(12, $orders[0]->getId());
        static::assertSame(11, $orders[1]->getId());
        static::assertSame(10, $orders[2]->getId());

        $orders = $orderService->getOrders(["id" => "asc"], 2, 4);
        static::assertSame(4, $orders[0]->getId());
        static::assertSame(5, $orders[1]->getId());
        static::assertSame(10, $orders[2]->getId());
        static::assertSame(11, $orders[3]->getId());

        $orders = $orderService->getOrders();
        static::assertSame(1, $orders[0]->getId());
        static::assertSame(2, $orders[1]->getId());
        static::assertSame(4, $orders[2]->getId());
    }

    public function testGetOrderWithBillingShippingAddress(): void
    {
        $order = $this->buildOrderService()->getOrder(10);
        $this->assertSame("01234567890", $order->getBillingAddress()->getPhone());
        $this->assertSame("01234567890", $order->getShippingAddress()->getPhone());
    }

    public function testGetAnOrderWithSystemOption(): void
    {
        $apiClient = $this->buildApiClient();
        $apiClient->authenticate('user@wizaplace.com', 'password');
        $orderService = new OrderService($apiClient);

        static::assertInstanceOf(
            SystemOption::class,
            $orderService
                ->getOrder(14)
                ->getOrderItems()[0]
                ->getDeclinationOptions()[0]
                ->getCode()
        );
    }

    public function testGetOrderRefunds(): void
    {
        $orderService = $this->buildOrderService('user@wizaplace.com', 'password');
        $refunds = $orderService->getOrderRefunds(14);

        static::assertCount(1, $refunds);

        $refund = $refunds[0];

        static::assertSame(1, $refund->getRefundId());
        static::assertSame(14, $refund->getOrderId());
        static::assertSame(false, $refund->isPartial());
        static::assertSame(true, $refund->hasShipping());
        static::assertSame(15.5, $refund->getAmount());
        static::assertSame(0., $refund->getShippingAmount());
        static::assertEquals(RefundStatus::PAID(), $refund->getStatus());
        static::assertNull($refund->getMessage());
        static::assertCount(1, $refund->getItems());

        $item = $refund->getItems()[0];

        static::assertSame(969482885, $item->getItemId());
        static::assertSame(1, $item->getQuantity());
        static::assertSame(15.5, $item->getTotalPrice()->getIncludingTaxes());
        static::assertSame(15.5, $refund->getTotalItemsPrice()->getIncludingTaxes());
        static::assertSame(15.5, $refund->getTotalGlobalPrice()->getIncludingTaxes());
        static::assertSame(0., $refund->getTotalShippingPrice()->getIncludingTaxes());
    }

    public function testGetOrderRefund(): void
    {
        $orderService = $this->buildOrderService('user@wizaplace.com', 'password');
        $refund = $orderService->getOrderRefund(14, 1);

        static::assertSame(1, $refund->getRefundId());
        static::assertSame(14, $refund->getOrderId());
        static::assertSame(false, $refund->isPartial());
        static::assertSame(true, $refund->hasShipping());
        static::assertSame(15.5, $refund->getAmount());
        static::assertSame(0., $refund->getShippingAmount());
        static::assertEquals(RefundStatus::PAID(), $refund->getStatus());
        static::assertNull($refund->getMessage());
        static::assertCount(1, $refund->getItems());

        $item = $refund->getItems()[0];

        static::assertSame(969482885, $item->getItemId());
        static::assertSame(1, $item->getQuantity());
        static::assertSame(15.5, $item->getTotalPrice()->getIncludingTaxes());
        static::assertSame(15.5, $refund->getTotalItemsPrice()->getIncludingTaxes());
        static::assertSame(15.5, $refund->getTotalGlobalPrice()->getIncludingTaxes());
        static::assertSame(0., $refund->getTotalShippingPrice()->getIncludingTaxes());
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
        $request = new RefundRequest(
            false,
            null,
            new RefundRequestShipping(false)
        );

        $refund = $orderService->postRefundOrder(28, $request);

        static::assertSame(25, $refund->getRefundId());
        static::assertSame(28, $refund->getOrderId());
        static::assertSame(false, $refund->isPartial());
        static::assertSame(true, $refund->hasShipping());
        static::assertSame(145., $refund->getAmount());
        static::assertSame(4., $refund->getShippingAmount());
        static::assertTrue(RefundStatus::PAID()->equals($refund->getStatus()));
        static::assertNull($refund->getMessage());
        static::assertEquals(new \DateTime('2020-01-17T15:37:48+01:00'), $refund->getCreatedAt());
        static::assertEquals(new \DateTime('2020-01-17T15:37:48+01:00'), $refund->getUpdatedAt());
        static::assertCount(1, $refund->getItems());

        $item = $refund->getItems()[0];

        static::assertSame(2973481700, $item->getItemId());
        static::assertSame(1, $item->getQuantity());
        static::assertSame(141., $item->getTotalPrice()->getIncludingTaxes());
        static::assertSame(141., $refund->getTotalItemsPrice()->getIncludingTaxes());
        static::assertSame(145., $refund->getTotalGlobalPrice()->getIncludingTaxes());
        static::assertSame(4., $refund->getTotalShippingPrice()->getIncludingTaxes());
    }

    public function testPostOrderRefundPartial(): void
    {
        $orderService = $this->buildOrderService('admin@wizaplace.com', 'password');
        $item = new RefundRequestItem(2973481700, 2);
        $shipping = new RefundRequestShipping(false);
        $request = new RefundRequest(true, [$item], $shipping, 'NEIN!');

        $refund = $orderService->postRefundOrder(29, $request);

        static::assertSame(26, $refund->getRefundId());
        static::assertSame(29, $refund->getOrderId());
        static::assertSame(true, $refund->isPartial());
        static::assertSame(false, $refund->hasShipping());
        static::assertSame(282., $refund->getAmount());
        static::assertSame(0., $refund->getShippingAmount());
        static::assertTrue(RefundStatus::PAID()->equals($refund->getStatus()));
        static::assertSame('NEIN!', $refund->getMessage());
        static::assertEquals(new \DateTime('2020-01-17T16:24:06+01:00'), $refund->getCreatedAt());
        static::assertEquals(new \DateTime('2020-01-17T16:24:06+01:00'), $refund->getUpdatedAt());
        static::assertCount(1, $refund->getItems());

        $item = $refund->getItems()[0];

        static::assertSame(2973481700, $item->getItemId());
        static::assertSame(2, $item->getQuantity());
        static::assertSame(141., $item->getUnitPrice()->getIncludingTaxes());
        static::assertSame(282., $refund->getTotalItemsPrice()->getIncludingTaxes());
        static::assertSame(282., $refund->getTotalGlobalPrice()->getIncludingTaxes());
        static::assertSame(0., $refund->getTotalShippingPrice()->getIncludingTaxes());
    }

    public function testGetOrderCreditNotes(): void
    {
        $orderService = $this->buildOrderService('user@wizaplace.com', 'password');
        $creditNotes = $orderService->getOrderCreditNotes(14);

        static::assertCount(2, $creditNotes);

        $creditNote = $creditNotes[0];

        static::assertSame(1, $creditNote->getRefundId());
    }

    public function testGetOrderCreditNote(): void
    {
        $orderService = $this->buildOrderService('user@wizaplace.com', 'password');
        $creditNote = $orderService->getOrderCreditNote(14, 1);

        $pdfHeader = '%PDF-1.4';
        $pdfContent = $creditNote->getContents();
        $this->assertStringStartsWith($pdfHeader, $pdfContent);
        $this->assertGreaterThan(\strlen($pdfHeader), \strlen($pdfContent));
    }
    public function testGetOrderWithBillingShippingAddressLabelAndComment(): void
    {
        $order = $this->buildOrderService()->getOrder(10);
        $this->assertSame("bComment", $order->getBillingAddress()->getComment());
        $this->assertSame("sComment", $order->getShippingAddress()->getComment());
        $this->assertSame("bLabel", $order->getBillingAddress()->getLabel());
        $this->assertSame("sLabel", $order->getShippingAddress()->getLabel());
    }

    public function testGetUserOrdersWithRefundedData(): void
    {
        $orderService = $this->buildOrderService();
        $orders = $orderService->getOrders();
        static::assertGreaterThanOrEqual(0, \count($orders));

        foreach ($orders as $order) {
            static::assertInternalType('boolean', $order->isRefunded());
        }
    }

    public function testGetUserOrderByIdWithRefundedData(): void
    {
        $order = $this->buildOrderService()->getOrder(2);

        static::assertFalse($order->isRefunded());
    }

    public function testGetOrdersWithBillingShippingAddressWhichReturnsLabelAndComment(): array
    {
        $apiClient = $this->buildApiClient();
        $apiClient->authenticate('customer-1@world-company.com', 'password-customer-1');
        $orderService = new OrderService($apiClient);
        $orders = $orderService->getOrders();

        $this->assertSame("billing comment", $orders[0]->getBillingAddress()->getComment());
        $this->assertSame("shipping Comment", $orders[0]->getShippingAddress()->getComment());
        $this->assertSame("home", $orders[0]->getBillingAddress()->getLabel());
        $this->assertSame("work", $orders[0]->getShippingAddress()->getLabel());

        $this->assertSame("bComment", $orders[4]->getBillingAddress()->getComment());
        $this->assertSame("sComment", $orders[4]->getShippingAddress()->getComment());
        $this->assertSame("bLabel", $orders[4]->getBillingAddress()->getLabel());
        $this->assertSame("sLabel", $orders[4]->getShippingAddress()->getLabel());

        return $orders;
    }

    public function testIsPaidCamelCase(): void
    {
        $order = $this->buildOrderService()->getOrder(2);

        static::assertNotNull($order->isPaid());
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

    public function testGetUserOrdersDisplayingBalanceTotal(): void
    {
        $apiClient = $this->buildApiClient();
        $apiClient->authenticate('customer-1@world-company.com', 'password-customer-1');
        $orderService = new OrderService($apiClient);

        $orders = $orderService->getOrders();
        static::assertSame(0.0, $orders[0]->getBalance());
        static::assertSame(0.0, $orders[1]->getBalance());
        static::assertSame(0.0, $orders[2]->getBalance());
    }

    public function testGetUserOrderByIdDisplayingBalanceTotal(): void
    {
        $apiClient = $this->buildApiClient();
        $apiClient->authenticate('customer-1@world-company.com', 'password-customer-1');
        $orderService = new OrderService($apiClient);

        $order = $orderService->getOrder(1);
        static::assertSame(0.0, $order->getBalance());
    }
}
