<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types=1);

namespace Wizaplace\SDK\Tests\Vendor\Order;

use Wizaplace\SDK\Tests\ApiTestCase;
use Wizaplace\SDK\Vendor\Order\CreateShipmentCommand;
use Wizaplace\SDK\Vendor\Order\Order;
use Wizaplace\SDK\Vendor\Order\OrderAddress;
use Wizaplace\SDK\Vendor\Order\OrderItem;
use Wizaplace\SDK\Vendor\Order\OrderService;
use Wizaplace\SDK\Vendor\Order\OrderStatus;
use Wizaplace\SDK\Vendor\Order\OrderSummary;
use Wizaplace\SDK\Vendor\Order\OrderTax;
use Wizaplace\SDK\Vendor\Order\Shipment;

class OrderServiceTest extends ApiTestCase
{
    public function testAcceptingAnOrder()
    {
        $orderService = $this->buildVendorOrderService();

        $orderService->acceptOrder(5);

        $this->assertTrue(OrderStatus::PROCESSING_SHIPPING()->equals($orderService->getOrderById(5)->getStatus()));
    }

    public function testDecliningAnOrder()
    {
        $orderService = $this->buildVendorOrderService();

        $orderService->declineOrder(5);

        $this->assertTrue(OrderStatus::VENDOR_DECLINED()->equals($orderService->getOrderById(5)->getStatus()));
    }

    public function testSetInvoiceNumber(): void
    {
        $orderService = $this->buildVendorOrderService();

        $invoiceNumber = $orderService->getOrderById(5)->getInvoiceNumber();
        $this->assertSame("", $invoiceNumber);

        $orderService->setInvoiceNumber(5, "00072");

        $invoiceNumber = $orderService->getOrderById(5)->getInvoiceNumber();

        $this->assertSame("00072", $invoiceNumber);

        $this->expectException(\Throwable::class); // can't set the invoice number twice
        $orderService->setInvoiceNumber(5, "00073");
    }

    public function testListOrders(): void
    {
        $orders = $this->buildVendorOrderService()->listOrders();

        $this->assertContainsOnly(OrderSummary::class, $orders);
        $this->assertCount(2, $orders);

        $order = $orders[0];
        $this->assertSame(5, $order->getOrderId());
        $this->assertSame(7, $order->getCustomerUserId());
        $this->assertSame('customer-1@world-company.com', $order->getCustomerEmail());
        $this->assertGreaterThan(1500000000, $order->getCreatedAt()->getTimestamp());
        $this->assertTrue(OrderStatus::STANDBY_VENDOR()->equals($order->getStatus()));

        $order = $orders[1];
        $this->assertSame(4, $order->getOrderId());
        $this->assertSame(7, $order->getCustomerUserId());
        $this->assertSame('customer-1@world-company.com', $order->getCustomerEmail());
        $this->assertGreaterThan(1500000000, $order->getCreatedAt()->getTimestamp());
        $this->assertTrue(OrderStatus::COMPLETED()->equals($order->getStatus()));
    }

    public function testListOrdersWithFilter(): void
    {
        $orders = $this->buildVendorOrderService()->listOrders(OrderStatus::STANDBY_VENDOR());

        $this->assertContainsOnly(OrderSummary::class, $orders);
        $this->assertCount(1, $orders);

        $order = $orders[0];
        $this->assertSame(5, $order->getOrderId());
        $this->assertSame(7, $order->getCustomerUserId());
        $this->assertSame('customer-1@world-company.com', $order->getCustomerEmail());
        $this->assertGreaterThan(1500000000, $order->getCreatedAt()->getTimestamp());
        $this->assertTrue(OrderStatus::STANDBY_VENDOR()->equals($order->getStatus()));
    }

    public function testGetOrderById(): void
    {
        $order = $this->buildVendorOrderService()->getOrderById(5);

        $this->assertInstanceOf(Order::class, $order);
        $this->assertSame(5, $order->getOrderId());
        $this->assertSame(7, $order->getCustomerUserId());
        $this->assertSame('customer-1@world-company.com', $order->getCustomerEmail());
        $this->assertSame(66.7, $order->getTotal());
        $this->assertSame(0.0, $order->getTaxSubtotal());
        $this->assertSame(0.0, $order->getDiscountAmount());
        $this->assertSame(0.0, $order->getShippingCost());
        $this->assertSame([], $order->getShipmentsIds());
        $this->assertSame('', $order->getInvoiceNumber());
        $this->assertSame('', $order->getNotes());
        $this->assertTrue(OrderStatus::STANDBY_VENDOR()->equals($order->getStatus()));
        $this->assertTrue($order->needsShipping());
        $this->assertGreaterThan(1500000000, $order->getCreatedAt()->getTimestamp());

        $shippingAddress = $order->getShippingAddress();
        $this->assertInstanceOf(OrderAddress::class, $shippingAddress);
        $this->assertSame('40 rue Laure Diebold', $shippingAddress->getAddress());
        $this->assertSame('', $shippingAddress->getComplementaryAddress());
        $this->assertSame('Lyon', $shippingAddress->getCity());
        $this->assertSame('FR', $shippingAddress->getCountryCode());
        $this->assertSame('Paul', $shippingAddress->getFirstName());
        $this->assertSame('Martin', $shippingAddress->getLastName());
        $this->assertSame('01234567890', $shippingAddress->getPhoneNumber());
        $this->assertSame('69009', $shippingAddress->getZipCode());

        $billingAddress = $order->getBillingAddress();
        $this->assertInstanceOf(OrderAddress::class, $billingAddress);
        $this->assertSame('40 rue Laure Diebold', $billingAddress->getAddress());
        $this->assertSame('', $billingAddress->getComplementaryAddress());
        $this->assertSame('Lyon', $billingAddress->getCity());
        $this->assertSame('FR', $billingAddress->getCountryCode());
        $this->assertSame('Paul', $billingAddress->getFirstName());
        $this->assertSame('Martin', $billingAddress->getLastName());
        $this->assertSame('01234567890', $billingAddress->getPhoneNumber());
        $this->assertSame('69009', $billingAddress->getZipCode());

        $items = $order->getItems();
        $this->assertContainsOnly(OrderItem::class, $items);
        $this->assertCount(1, $items);
        /** @var OrderItem $item */
        $item = reset($items);
        $this->assertSame(2085640488, $item->getItemId());
        $this->assertSame(1, $item->getProductId());
        $this->assertSame(0.0, $item->getDiscountAmount());
        $this->assertSame('978020137962', $item->getCode());
        $this->assertSame(67.9, $item->getPrice());
        $this->assertSame(1, $item->getQuantity());
        $this->assertSame(0, $item->getQuantityShipped());
        $this->assertSame(1, $item->getQuantityToShip());
        $optionsVariantsIds = $item->getOptionsVariantsIds();
        $this->assertCount(0, $optionsVariantsIds);

        $taxes = $order->getTaxes();
        $this->assertContainsOnly(OrderTax::class, $taxes);
        $this->assertCount(1, $taxes);
        $tax = reset($taxes);
        $this->assertSame('TVA 2.1%', $tax->getDescription());
        $this->assertSame(2.1, $tax->getRateValue());
        $this->assertSame(1.3966, $tax->getTaxSubtotal());
        $this->assertTrue($tax->doesPriceIncludesTax());
    }

    public function testCreateShipment()
    {
        $orderService = $this->buildVendorOrderService();

        $orderId = 5;
        $orderService->acceptOrder($orderId);

        $order = $orderService->getOrderById($orderId);

        $itemsShipped = [];
        foreach ($order->getItems() as $item) {
            $itemsShipped[$item->getItemId()] = $item->getQuantityToShip();
        }

        $shipmentId = $orderService->createShipment(
            (new CreateShipmentCommand($orderId, '0ABC0123456798'))
            ->setComment('great shipment')
            ->setShippedQuantityByItemId($itemsShipped)
        );

        $this->assertGreaterThan(0, $shipmentId);

        $order = $orderService->getOrderById($orderId);
        $this->assertSame([$shipmentId], $order->getShipmentsIds());

        $shipments = $orderService->listShipments($orderId);
        $this->assertContainsOnly(Shipment::class, $shipments);
        $this->assertCount(1, $shipments);
        $this->assertSame($shipmentId, $shipments[0]->getShipmentId());
        $this->assertSame($orderId, $shipments[0]->getOrderId());
        $this->assertSame('great shipment', $shipments[0]->getComment());
        $this->assertSame($itemsShipped, $shipments[0]->getShippedQuantityByItemId());
        $this->assertSame('TNT Express', $shipments[0]->getShippingName());
        $this->assertSame('0ABC0123456798', $shipments[0]->getTrackingNumber());
        $this->assertSame(1, $shipments[0]->getShippingId());
        $this->assertGreaterThanOrEqual(1500000000, $shipments[0]->getCreatedAt()->getTimestamp());

        $shipment = $orderService->getShipmentById($shipmentId);
        $this->assertInstanceOf(Shipment::class, $shipment);
        $this->assertEquals($shipments[0], $shipment);
    }

    private function buildVendorOrderService(string $email = 'vendor@world-company.com', string $password = 'password-vendor'): OrderService
    {
        $apiClient = $this->buildApiClient();
        $apiClient->authenticate($email, $password);

        return new OrderService($apiClient);
    }
}
