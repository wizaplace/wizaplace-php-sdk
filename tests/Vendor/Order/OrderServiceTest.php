<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types=1);

namespace Wizaplace\SDK\Tests\Vendor\Order;

use Wizaplace\SDK\Tests\ApiTestCase;
use Wizaplace\SDK\Vendor\Order\Order;
use Wizaplace\SDK\Vendor\Order\OrderAddress;
use Wizaplace\SDK\Vendor\Order\OrderItem;
use Wizaplace\SDK\Vendor\Order\OrderService;
use Wizaplace\SDK\Vendor\Order\OrderStatus;
use Wizaplace\SDK\Vendor\Order\OrderTax;

class OrderServiceTest extends ApiTestCase
{
    public function testAcceptingAnOrder()
    {
        $orderService = $this->buildVendorOrderService();
        self::$historyContainer = [];

        $orderService->acceptOrder(5);

        $this->assertCount(1, self::$historyContainer); // @TODO : get the order before and after, and check the status
    }

    public function testDecliningAnOrder()
    {
        $orderService = $this->buildVendorOrderService();
        self::$historyContainer = [];

        $orderService->declineOrder(5);

        $this->assertCount(1, self::$historyContainer); // @TODO : get the order before and after, and check the status
    }

    public function testGetOrderById(): void
    {
        $order = $this->buildVendorOrderService()->getOrderById(5);

        $this->assertInstanceOf(Order::class, $order);
        $this->assertSame(5, $order->getOrderId());
        $this->assertSame(7, $order->getClientUserId());
        $this->assertSame('customer-1@world-company.com', $order->getClientEmail());
        $this->assertSame(67.9, $order->getTotal());
        $this->assertSame(0.0, $order->getTaxSubtotal());
        $this->assertSame(0.0, $order->getDiscountAmount());
        $this->assertSame(0.0, $order->getShippingCost());
        $this->assertSame([], $order->getShipmentsIds());
        $this->assertSame('', $order->getInvoiceNumber());
        $this->assertSame('', $order->getNotes());
        $this->assertSame([], $order->getPromotions());
        $this->assertTrue(OrderStatus::STANDBY_VENDOR()->equals($order->getStatus()));
        $this->assertTrue($order->needsShipping());
        $this->assertGreaterThan(1500000000, $order->getCreatedAt()->getTimestamp());

        $shippingAddress = $order->getShippingAddress();
        $this->assertInstanceOf(OrderAddress::class, $shippingAddress);
        $this->assertSame('40 rue Laure Diebold', $shippingAddress->getAddress());
        $this->assertSame('', $shippingAddress->getAddress2());
        $this->assertSame('Lyon', $shippingAddress->getCity());
        $this->assertSame('FR', $shippingAddress->getCountryCode());
        $this->assertSame('Paul', $shippingAddress->getFirstName());
        $this->assertSame('Martin', $shippingAddress->getLastName());
        $this->assertSame('01234567890', $shippingAddress->getPhoneNumber());
        $this->assertSame('69009', $shippingAddress->getZipCode());

        $billingAddress = $order->getBillingAddress();
        $this->assertInstanceOf(OrderAddress::class, $billingAddress);
        $this->assertSame('40 rue Laure Diebold', $billingAddress->getAddress());
        $this->assertSame('', $billingAddress->getAddress2());
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
        $this->assertSame(0, $tax->getPriority());
        $this->assertSame('445711', $tax->getRegNumber());
        $this->assertTrue($tax->doesPriceIncludesTax());
    }

    private function buildVendorOrderService(string $email = 'vendor@world-company.com', string $password = 'password-vendor'): OrderService
    {
        $apiClient = $this->buildApiClient();
        $apiClient->authenticate($email, $password);

        return new OrderService($apiClient);
    }
}
