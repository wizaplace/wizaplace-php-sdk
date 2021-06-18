<?php

/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Tests\Vendor\Order;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\HttpFoundation\Response;
use Wizaplace\SDK\Authentication\AuthenticationRequired;
use Wizaplace\SDK\Basket\BasketService;
use Wizaplace\SDK\Catalog\DeclinationId;
use Wizaplace\SDK\Exception\NotFound;
use Wizaplace\SDK\Order\OrderAdjustment;
use Wizaplace\SDK\Order\RefundStatus;
use Wizaplace\SDK\PaginatedData;
use Wizaplace\SDK\Shipping\Shipping;
use Wizaplace\SDK\Subscription\SubscriptionSummary;
use Wizaplace\SDK\Tests\ApiTestCase;
use Wizaplace\SDK\Transaction\Transaction;
use Wizaplace\SDK\Transaction\TransactionStatus;
use Wizaplace\SDK\Transaction\TransactionType;
use Wizaplace\SDK\Vendor\Order\AmountTaxesDetail;
use Wizaplace\SDK\Vendor\Order\CreateShipmentCommand;
use Wizaplace\SDK\Vendor\Order\Order;
use Wizaplace\SDK\Vendor\Order\OrderAddress;
use Wizaplace\SDK\Vendor\Order\OrderAttachment;
use Wizaplace\SDK\Vendor\Order\OrderAttachmentFilter;
use Wizaplace\SDK\Vendor\Order\OrderAttachmentType;
use Wizaplace\SDK\Vendor\Order\OrderItem;
use Wizaplace\SDK\Vendor\Order\OrderListFilter;
use Wizaplace\SDK\Vendor\Order\OrderService;
use Wizaplace\SDK\Vendor\Order\OrderStatus;
use Wizaplace\SDK\Vendor\Order\OrderSummary;
use Wizaplace\SDK\Vendor\Order\OrderTax;
use Wizaplace\SDK\Vendor\Order\Shipment;
use Wizaplace\SDK\Vendor\Order\Tax;

class OrderServiceTest extends ApiTestCase
{
    public function testAcceptingAnOrder()
    {
        $orderService = $this->buildVendorOrderService();

        $orderService->acceptOrder(5);

        static::assertTrue(OrderStatus::PROCESSING_SHIPPING()->equals($orderService->getOrderById(5)->getStatus()));
    }

    public function testDecliningAnOrderWithoutReason()
    {
        $orderService = $this->buildVendorOrderService();

        $orderService->declineOrder(5);

        $order = $orderService->getOrderById(5);
        static::assertTrue(OrderStatus::VENDOR_DECLINED()->equals($order->getStatus()));
        static::assertEmpty($order->getDeclineReason());
    }

    public function testDecliningAnOrderWithReason()
    {
        $orderService = $this->buildVendorOrderService();

        $orderService->declineOrder(5, 'Product out of stock');

        $order = $orderService->getOrderById(5);
        static::assertTrue(OrderStatus::VENDOR_DECLINED()->equals($order->getStatus()));
        static::assertSame('Product out of stock', $order->getDeclineReason());
    }

    public function testSetInvoiceNumber(): void
    {
        $orderService = $this->buildVendorOrderService();

        // approve order
        $orderService->acceptOrder(5, true, "F12000");
        $invoiceNumber = $orderService->getOrderById(5)->getInvoiceNumber();
        static::assertSame("F12000", $invoiceNumber);

        // set invoice number
        $orderService->setInvoiceNumber(5, "00072");
        $invoiceNumber = $orderService->getOrderById(5)->getInvoiceNumber();
        static::assertSame("00072", $invoiceNumber);
    }

    public function testListOrders(): void
    {
        $orders = $this
            ->buildVendorOrderService('admin@wizaplace.com', 'password')
            ->listOrders();

        static::assertContainsOnly(OrderSummary::class, $orders);
        static::assertTrue(\count($orders) >= 2);

        // To fix random sort
        usort(
            $orders,
            function (OrderSummary $a, OrderSummary $b): int {
                return $a->getOrderId() <=> $b->getOrderId();
            }
        );

        $order = array_shift($orders);
        static::assertSame(1, $order->getOrderId());
        static::assertSame(7, $order->getCustomerUserId());
        static::assertSame(3, $order->getCompanyId());
        static::assertSame('customer-1@world-company.com', $order->getCustomerEmail());
        static::assertSame('Paul', $order->getCustomerFirstName());
        static::assertSame('Martin', $order->getCustomerLastName());
        static::assertGreaterThan(1500000000, $order->getCreatedAt()->getTimestamp());
        static::assertFalse(OrderStatus::COMPLETED()->equals($order->getStatus()));
        static::assertInstanceOf(\DateTimeImmutable::class, $order->getLastStatusChange());

        $order = array_shift($orders);
        static::assertSame(2, $order->getOrderId());
        static::assertSame(7, $order->getCustomerUserId());
        static::assertSame(3, $order->getCompanyId());
        static::assertSame('customer-1@world-company.com', $order->getCustomerEmail());
        static::assertSame('Paul', $order->getCustomerFirstName());
        static::assertSame('Martin', $order->getCustomerLastName());
        static::assertGreaterThan(1500000000, $order->getCreatedAt()->getTimestamp());
        static::assertFalse(OrderStatus::STANDBY_VENDOR()->equals($order->getStatus()));
        static::assertInstanceOf(\DateTimeImmutable::class, $order->getLastStatusChange());
    }

    public function testGetVendorPaginatedOrders(): void
    {
        $orderService = $this->buildVendorOrderService('admin@wizaplace.com', 'password');

        $paginatedData = $orderService->getPaginatedOrders();

        static::assertContainsOnly(OrderSummary::class, $paginatedData->getItems());
        static::assertEquals(12, \count($paginatedData->getItems()));
        static::assertEquals(100, $paginatedData->getLimit());
        static::assertEquals(0, $paginatedData->getOffset());
        static::assertEquals(12, $paginatedData->getTotal());

        $paginatedData = $orderService->getPaginatedOrders(
            null,
            (new OrderListFilter())
                ->byItemPerPage(4)
        );

        static::assertContainsOnly(OrderSummary::class, $paginatedData->getItems());
        static::assertEquals(4, \count($paginatedData->getItems()));
        static::assertEquals(4, $paginatedData->getLimit());
        static::assertEquals(0, $paginatedData->getOffset());
        static::assertEquals(12, $paginatedData->getTotal());

        $paginatedData = $orderService->getPaginatedOrders(
            null,
            (new OrderListFilter())
                ->byItemPerPage(10)
                ->byPage(2)
        );

        static::assertContainsOnly(OrderSummary::class, $paginatedData->getItems());
        static::assertEquals(2, \count($paginatedData->getItems()));
        static::assertEquals(10, $paginatedData->getLimit());
        static::assertEquals(10, $paginatedData->getOffset());
        static::assertEquals(12, $paginatedData->getTotal());

        $paginatedData = $orderService->getPaginatedOrders(
            null,
            (new OrderListFilter())
                ->byItemPerPage(0)
        );

        static::assertContainsOnly(OrderSummary::class, $paginatedData->getItems());
        static::assertEquals(12, \count($paginatedData->getItems()));
        static::assertEquals(0, $paginatedData->getLimit());
        static::assertEquals(0, $paginatedData->getOffset());
        static::assertEquals(12, $paginatedData->getTotal());
    }

    public function testListOrdersWithFilter(): void
    {
        $orders = $this
            ->buildVendorOrderService('admin@wizaplace.com', 'password')
            ->getPaginatedOrders(OrderStatus::STANDBY_VENDOR());

        static::assertContainsOnly(OrderSummary::class, $orders->getItems());
        static::assertEquals(2, $orders->getTotal());

        $order = $orders->getItems()[0];
        static::assertSame(10, $order->getOrderId());
        static::assertSame(7, $order->getCustomerUserId());
        static::assertSame(3, $order->getCompanyId());
        static::assertSame('customer-1@world-company.com', $order->getCustomerEmail());
        static::assertGreaterThan(1500000000, $order->getCreatedAt()->getTimestamp());
        static::assertTrue(OrderStatus::STANDBY_VENDOR()->equals($order->getStatus()));
    }

    public function testFilterByCompanyIdWithNoResult(): void
    {
        $orders = $this
            ->buildVendorOrderService("admin@wizaplace.com", "password")
            ->getPaginatedOrders(
                OrderStatus::COMPLETED(),
                (new OrderListFilter())
                    ->byCompanyIds([4])
            );

        static::assertCount(0, $orders->getItems());
    }

    public function testFilterByCompanyIdWithAResult(): void
    {
        $orders = $this
            ->buildVendorOrderService("admin@wizaplace.com", "password")
            ->getPaginatedOrders(
                OrderStatus::STANDBY_VENDOR(),
                (new OrderListFilter())
                ->byCompanyIds([3])
            );

        static::assertContainsOnly(OrderSummary::class, $orders->getItems());
        static::assertEquals(2, $orders->getTotal());
    }

    public function testFilterByLastStatusChange(): void
    {
        $orders = $this
            ->buildVendorOrderService('admin@wizaplace.com', 'password')
            ->getPaginatedOrders(
                OrderStatus::STANDBY_VENDOR(),
                (new OrderListFilter())
                    ->byLastStatusChangeIsAfter(new \DateTime("2021-04-16T00:00:00"))
                    ->byLastStatusChangeIsBefore(new \DateTime("2021-05-16T13:37:36"))
            );

        static::assertEquals(2, $orders->getTotal());
    }

    public function testFilterByItemsPerPageAndPage(): void
    {
        $orders = $this
            ->buildVendorOrderService('admin@wizaplace.com', 'password')
            ->getPaginatedOrders(
                OrderStatus::COMPLETED(),
                (new OrderListFilter())
                    ->byItemPerPage(2)
                    ->byPage(1)
            );

        $secendOrderId = $orders->getItems()[1]->getOrderId();
        static::assertEquals(5, $orders->getTotal());

        $orders = $this
            ->buildVendorOrderService('admin@wizaplace.com', 'password')
            ->getPaginatedOrders(
                OrderStatus::COMPLETED(),
                (new OrderListFilter())
                    ->byItemPerPage(1)
                    ->byPage(2)
            );

        static::assertEquals(5, $orders->getTotal());
        static::assertSame($secendOrderId, $orders->getItems()[0]->getOrderId());
    }

    public function testFilterByItemsPerCreatedAfter(): void
    {
        static::assertCount(
            2,
            $this->buildVendorOrderService()
                ->listOrders(
                    OrderStatus::COMPLETED(),
                    (new OrderListFilter())
                        ->byCreatedAfter(new \DateTime('2021-06-16T16:00:00+02:00'))
                )
        );
    }

    public function testFilterByItemsPerCreatedBefore(): void
    {
        static::assertCount(
            3,
            $this->buildVendorOrderService()
                ->listOrders(
                    OrderStatus::COMPLETED(),
                    (new OrderListFilter())
                        ->byCreatedBefore(new \DateTime('2021-06-16T16:00:00+02:00'))
                )
        );
    }

    public function testGetOrderById(): void
    {
        $order = $this
            ->buildVendorOrderService('vendor@world-company.com', static::VALID_PASSWORD)
            ->getOrderById(5);

        static::assertInstanceOf(Order::class, $order);
        static::assertSame(5, $order->getOrderId());
        static::assertSame(7, $order->getCustomerUserId());
        static::assertSame(3, $order->getCompanyId());
        static::assertSame('customer-1@world-company.com', $order->getCustomerEmail());
        static::assertSame('Paul', $order->getCustomerFirstName());
        static::assertSame('Martin', $order->getCustomerLastName());
        static::assertSame(66.7, $order->getTotal());
        static::assertSame(0.0, $order->getTaxSubtotal());
        static::assertSame(1.2, $order->getDiscountAmount());
        static::assertSame(0.0, $order->getShippingCost());
        static::assertSame([], $order->getShipmentsIds());
        static::assertSame('', $order->getInvoiceNumber());
        static::assertSame('', $order->getNotes());
        static::assertTrue(OrderStatus::STANDBY_VENDOR()->equals($order->getStatus()));
        static::assertTrue($order->needsShipping());
        static::assertGreaterThan(1500000000, $order->getCreatedAt()->getTimestamp());
        static::assertInstanceOf(\DateTimeImmutable::class, $order->getLastStatusChange());
        static::assertSame(0.0, $order->getMarketplaceDiscountTotal());
        static::assertSame(66.7, $order->getCustomerTotal());

        // Vendor info
        $this->assertSame(true, $order->isCustomerProfessional());
        $this->assertSame('User company', $order->getCustomerCompany());
        $this->assertSame('100200300', $order->getCustomerLegalIdentifier());
        $this->assertSame('FR416871594', $order->getCustomerIntraEuropeanCommunityVat());
        $this->assertSame('Title', $order->getCustomerJobTitle());
        $this->assertSame('This is a comment', $order->getCustomerAccountComment());
        $this->assertSame('EXT001', $order->getCustomerExternalIdentifier());

        $shippingAddress = $order->getShippingAddress();
        static::assertInstanceOf(OrderAddress::class, $shippingAddress);
        static::assertSame('40 rue Laure Diebold', $shippingAddress->getAddress());
        static::assertSame('3ème étage', $shippingAddress->getComplementaryAddress());
        static::assertSame('Lyon', $shippingAddress->getCity());
        static::assertSame('FR', $shippingAddress->getCountryCode());
        static::assertSame('Paul', $shippingAddress->getFirstName());
        static::assertSame('Martin', $shippingAddress->getLastName());
        static::assertSame('01234567890', $shippingAddress->getPhoneNumber());
        static::assertSame('69009', $shippingAddress->getZipCode());

        $billingAddress = $order->getBillingAddress();
        static::assertInstanceOf(OrderAddress::class, $billingAddress);
        static::assertSame('40 rue Laure Diebold', $billingAddress->getAddress());
        static::assertSame('3ème étage', $billingAddress->getComplementaryAddress());
        static::assertSame('Lyon', $billingAddress->getCity());
        static::assertSame('FR', $billingAddress->getCountryCode());
        static::assertSame('Paul', $billingAddress->getFirstName());
        static::assertSame('Martin', $billingAddress->getLastName());
        static::assertSame('01234567890', $billingAddress->getPhoneNumber());
        static::assertSame('69009', $billingAddress->getZipCode());

        $items = $order->getItems();
        static::assertContainsOnly(OrderItem::class, $items);
        static::assertCount(1, $items);
        /** @var OrderItem $item */
        $item = reset($items);
        static::assertSame(2085640488, $item->getItemId());
        static::assertSame(1, $item->getProductId());
        static::assertSame(0.0, $item->getDiscountAmount());
        static::assertSame('978020137962', $item->getCode());
        static::assertSame(67.9, $item->getPrice());
        static::assertSame(1, $item->getQuantity());
        static::assertSame(0, $item->getQuantityShipped());
        static::assertSame(1, $item->getQuantityToShip());
        $optionsVariantsIds = $item->getOptionsVariantsIds();
        static::assertCount(0, $optionsVariantsIds);
        static::assertSame('1_0', $item->getDeclinationId());

        $taxes = $order->getTaxes();
        static::assertContainsOnly(OrderTax::class, $taxes);
        static::assertCount(1, $taxes);
        $tax = reset($taxes);
        static::assertSame('TVA 2.1%', $tax->getDescription());
        static::assertSame(2.1, $tax->getRateValue());
        static::assertSame(1.3966, $tax->getTaxSubtotal());
        static::assertTrue($tax->doesPriceIncludesTax());
    }

    public function testGetOrderByIdCarriagePaid(): void
    {
        $order = $this
            ->buildVendorOrderService()
            ->getOrderById(15)
        ;
        static::assertTrue($order->isCarriagePaid());
    }

    public function testGetOrderComments(): void
    {
        $order = $this->buildVendorOrderService()->getOrderById(4);

        static::assertSame('Please deliver at the front desk of my company.', $order->getComment());
        $items = $order->getItems();
        static::assertSame('Please, gift wrap this product.', reset($items)->getComment());
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
            ->setLabelUrl('http://mondialrelay.com/shipment-created')
            ->setShippedQuantityByItemId($itemsShipped)
        );

        static::assertGreaterThan(0, $shipmentId);

        $order = $orderService->getOrderById($orderId);
        static::assertSame([$shipmentId], $order->getShipmentsIds());

        $shipments = $orderService->listShipments($orderId);
        static::assertContainsOnly(Shipment::class, $shipments);
        static::assertCount(1, $shipments);
        static::assertSame($shipmentId, $shipments[0]->getShipmentId());
        static::assertSame($orderId, $shipments[0]->getOrderId());
        static::assertSame('great shipment', $shipments[0]->getComment());
        static::assertSame($itemsShipped, $shipments[0]->getShippedQuantityByItemId());
        static::assertSame('TNT Express', $shipments[0]->getShippingName());
        static::assertSame('0ABC0123456798', $shipments[0]->getTrackingNumber());
        static::assertSame('http://mondialrelay.com/shipment-created', $shipments[0]->getLabelUrl());
        static::assertSame(1, $shipments[0]->getShippingId());
        static::assertGreaterThanOrEqual(1500000000, $shipments[0]->getCreatedAt()->getTimestamp());

        $shipment = $orderService->getShipmentById($shipmentId);
        static::assertInstanceOf(Shipment::class, $shipment);
        static::assertEquals($shipments[0], $shipment);
    }

    public function testListTaxes(): void
    {
        $taxes = $this->buildVendorOrderService()->listTaxes();

        static::assertContainsOnly(Tax::class, $taxes);
        static::assertNotEmpty($taxes);

        foreach ($taxes as $tax) {
            static::assertInternalType('int', $tax->getId());
            static::assertGreaterThan(0, $tax->getId());

            static::assertInternalType('string', $tax->getName());
            static::assertNotEmpty($tax->getName());
        }
    }

    /**
     * @dataProvider lastStatusChangeProvider
     * @param mixed $lastStatusChange The lastStatusChange variable to test
     * @param null|string $expected case to assert (null, Throwable::class, DateTimeImmutable::class)
     */
    public function testDenormalizeLastStatusChange($lastStatusChange, ?string $expected): void
    {
        $orderData = \json_decode('{"order_id":5,"company_id":3,"user_id":7,"basket_id":"c8512874-2a4a-4ed3-8e66-708c2fa54c1a","total":66.7,"discount":1.2,"shipping_cost":0.0,"timestamp":1551876306,"status":"P","notes":"","promotions":[],"b_firstname":"Paul","b_lastname":"Martin","b_company":"","b_address":"40 rue Laure Diebold","b_address_2":"3\u00e8me \u00e9tage","b_city":"Lyon","b_country":"FR","b_zipcode":"69009","b_phone":"01234567890","s_firstname":"Paul","s_lastname":"Martin","s_company":"","s_address":"40 rue Laure Diebold","s_address_2":"3\u00e8me \u00e9tage","s_city":"Lyon","s_country":"FR","s_zipcode":"69009","s_phone":"01234567890","s_pickup_point_id":"","email":"customer-1@world-company.com","decline_reason":null,"invoice_date":"","products":{"2085640488":{"item_id":"2085640488","product_id":1,"price":67.9,"amount":1,"comment":"","extra":{"combinations":null},"discount":0.0,"green_tax":"0.00","shipped_amount":0,"shipment_amount":"1","selected_code":"978020137962","supplier_ref":"INFO-001"}},"taxes":{"2":{"rate_type":"P","rate_value":"2.100","price_includes_tax":"Y","regnumber":"445711","priority":0,"tax_subtotal":1.3966,"description":"TVA 2.1%","applies":{"P_2085640488":1.3966}}},"tax_subtotal":0.0,"need_shipping":true,"shipping":[{"shipping_id":1,"status":"A","shipping":"TNT Express","delivery_time":"24h","rates":[{"amount":0,"value":null},{"amount":1,"value":null}],"specific_rate":false,"description":"<p>Code : TNT01<\/p>\r\n<p>Type : Livraison &agrave; domicile <br \/> Mode : EXP<\/p>\r\n<p>Tel : 08 25 03 30 33<\/p>\r\n<p>Email :<\/p>\r\n<p>Adresse : 58 Avenue Leclerc <br \/> 69007Lyon<br \/>France<\/p>\r\n<p>URL tracking : http:\/\/www.tnt.fr\/suivi<\/p>\r\n<p>Service : Transport express au domicile, au travail ou en relais colis.<\/p>"}],"shipment_ids":[],"invoice_number":"","last_status_change":"2019-03-06T13:45:36+01:00","customer_firstname":"Paul","customer_lastname":"Martin","payment":{"type":"manual","processorName":null,"commitmentDate":null,"processorInformation":{}},"workflow":"workflow_order_validation_pending_vendor_validation_processing"}', true);
        $orderData['last_status_change'] = $lastStatusChange;

        switch ($expected) {
            case null:
                static::assertNull((new Order($orderData))->getLastStatusChange());
                break;

            case \Throwable::class:
                $this->expectException(\Throwable::class);
                new Order($orderData);
                break;

            case \DateTimeImmutable::class:
                static::assertInstanceOf($expected, (new Order($orderData))->getLastStatusChange());
                break;

            default:
                throw new \Exception('$expected value "$expected" is not a valid test case : null, Throwable::class, DateTimeImmutable::class');
        }
    }

    public function lastStatusChangeProvider(): array
    {
        return [
            [null, null],
            ['', null],
            ['          ', null],
            [0, \Throwable::class],
            ['0', \Throwable::class],
            ['qsfsdfsdf', \Throwable::class],
            ['00-00-0000 00:00:00', \DateTimeImmutable::class],
            ['08-03-2019T12:13:15Z', \DateTimeImmutable::class],
        ];
    }

    public function testGetOrderAmountsTaxesDetails(): void
    {
        $orderService = $this->buildVendorOrderService();

        $order = $orderService->getOrderById(9);

        static::assertInstanceOf(AmountTaxesDetail::class, $order->getTotalsTaxesDetail());
        static::assertInstanceOf(AmountTaxesDetail::class, $order->getShippingCostsTaxesDetail());
        static::assertInstanceOf(AmountTaxesDetail::class, $order->getCommissionsTaxesDetail());
        static::assertInstanceOf(AmountTaxesDetail::class, $order->getVendorShareTaxesDetail());

        static::assertSame(53.0852, $order->getTotalsTaxesDetail()->getExcludingTaxes());
        static::assertSame(1.1148, $order->getTotalsTaxesDetail()->getTaxes());
        static::assertSame(54.2, $order->getTotalsTaxesDetail()->getIncludingTaxes());

        static::assertSame(0.0, $order->getShippingCostsTaxesDetail()->getExcludingTaxes());
        static::assertSame(0.0, $order->getShippingCostsTaxesDetail()->getTaxes());
        static::assertSame(0.0, $order->getShippingCostsTaxesDetail()->getIncludingTaxes());

        static::assertSame(0.56, $order->getCommissionsTaxesDetail()->getExcludingTaxes());
        static::assertSame(0.112, $order->getCommissionsTaxesDetail()->getTaxes());
        static::assertSame(0.672, $order->getCommissionsTaxesDetail()->getIncludingTaxes());

        static::assertSame(52.5252, $order->getVendorShareTaxesDetail()->getExcludingTaxes());
        static::assertSame(1.0028, $order->getVendorShareTaxesDetail()->getTaxes());
        static::assertSame(53.528, $order->getVendorShareTaxesDetail()->getIncludingTaxes());
    }

    public function testGetOrdersAmountsTaxesDetails(): void
    {
        $orders = $this
            ->buildVendorOrderService('admin@wizaplace.com', 'password')
            ->getPaginatedOrders(OrderStatus::COMPLETED())
            ->getItems();

        $expectedOrders = $this->expectedOrdersAmounts();
        static::assertContainsOnly(OrderSummary::class, $orders);
        static::assertCount(5, $orders);

        foreach ($orders as $order) {
            static::assertTrue(\array_key_exists($order->getOrderId(), $expectedOrders));

            static::assertSame($expectedOrders[$order->getOrderId()]['totals']['excludingTaxes'], $order->getTotalsTaxesDetail()->getExcludingTaxes());
            static::assertSame($expectedOrders[$order->getOrderId()]['totals']['taxes'], $order->getTotalsTaxesDetail()->getTaxes());
            static::assertSame($expectedOrders[$order->getOrderId()]['totals']['includingTaxes'], $order->getTotalsTaxesDetail()->getIncludingTaxes());

            static::assertSame($expectedOrders[$order->getOrderId()]['shippingCosts']['excludingTaxes'], $order->getShippingCostsTaxesDetail()->getExcludingTaxes());
            static::assertSame($expectedOrders[$order->getOrderId()]['shippingCosts']['taxes'], $order->getShippingCostsTaxesDetail()->getTaxes());
            static::assertSame($expectedOrders[$order->getOrderId()]['shippingCosts']['includingTaxes'], $order->getShippingCostsTaxesDetail()->getIncludingTaxes());

            static::assertSame($expectedOrders[$order->getOrderId()]['commissions']['excludingTaxes'], $order->getCommissionsTaxesDetail()->getExcludingTaxes());
            static::assertSame($expectedOrders[$order->getOrderId()]['commissions']['taxes'], $order->getCommissionsTaxesDetail()->getTaxes());
            static::assertSame($expectedOrders[$order->getOrderId()]['commissions']['includingTaxes'], $order->getCommissionsTaxesDetail()->getIncludingTaxes());

            static::assertSame($expectedOrders[$order->getOrderId()]['vendorShare']['excludingTaxes'], $order->getVendorShareTaxesDetail()->getExcludingTaxes());
            static::assertSame($expectedOrders[$order->getOrderId()]['vendorShare']['taxes'], $order->getVendorShareTaxesDetail()->getTaxes());
            static::assertSame($expectedOrders[$order->getOrderId()]['vendorShare']['includingTaxes'], $order->getVendorShareTaxesDetail()->getIncludingTaxes());
        }
    }

    /**
     * @dataProvider orderWithStatus
     */
    public function testGetOrderWithStatus(int $orderId, string $status): void
    {
        $orderService = $this->buildVendorOrderService('admin@wizaplace.com', 'password');
        $order = $orderService->getOrderById($orderId);
        static::assertInstanceOf(Order::class, $order);
        static::assertEquals($status, $order->getStatus());
    }

    public function testCreateOrderAdjustments(): void
    {
        $vendorOrderService = $this->buildVendorOrderService();
        $vendorOrderService->createOrderAdjustment(10, 3230927120, 10);
        static::assertSame(9.01, $vendorOrderService->getOrderById(10)->getTotal());
    }

    public function testGetOrderAdjustments(): void
    {
        $orderService = $this->buildVendorOrderService();
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

    public function orderWithStatus(): array
    {
        return [
            [11, OrderStatus::BILLING_FAILED()],
            [1, OrderStatus::STANDBY_BILLING()],
            [6, OrderStatus::INCOMPLETED()],
        ];
    }

    public function testSetDetailsOnAnOrder(): void
    {
        $orderService = $this->buildVendorOrderService();
        $details = "Voici un nouveau commentaire de vendeur sur la commande";
        $orderService->setOrderDetails(5, $details);
        static::assertSame($details, $orderService->getOrderById(5)->getDetails());
    }

    public function expectedOrdersAmounts(): array
    {
        return [
            4 => [
                'totals' => [
                    'excludingTaxes' => 66.5034,
                    'taxes' => 1.3966,
                    'includingTaxes' => 67.9,
                ],
                'shippingCosts' => [
                    'excludingTaxes' => 0.0,
                    'taxes' => 0.0,
                    'includingTaxes' => 0.0,
                ],
                'commissions' => [
                    'excludingTaxes' => 0.6742,
                    'taxes' => 0.1348,
                    'includingTaxes' => 0.809,
                ],
                'vendorShare' => [
                    'excludingTaxes' => 65.8292,
                    'taxes' => 1.2618,
                    'includingTaxes' => 67.091,
                ],
            ],
            7 => [
                'totals' => [
                    'excludingTaxes' => 66.5034,
                    'taxes' => 1.3966,
                    'includingTaxes' => 67.9,
                ],
                'shippingCosts' => [
                    'excludingTaxes' => 0.0,
                    'taxes' => 0.0,
                    'includingTaxes' => 0.0,
                ],
                'commissions' => [
                    'excludingTaxes' => 0.6742,
                    'taxes' => 0.1348,
                    'includingTaxes' => 0.809,
                ],
                'vendorShare' => [
                    'excludingTaxes' => 65.8292,
                    'taxes' => 1.2618,
                    'includingTaxes' => 67.091,
                ],
            ],
            8 => [
                'totals' => [
                    'excludingTaxes' => 144.3682,
                    'taxes' => 3.0317,
                    'includingTaxes' => 147.3999,
                ],
                'shippingCosts' => [
                    'excludingTaxes' => 0.0,
                    'taxes' => 0.0,
                    'includingTaxes' => 0.0,
                ],
                'commissions' => [
                    'excludingTaxes' => 1.3367,
                    'taxes' => 0.2673,
                    'includingTaxes' => 1.604,
                ],
                'vendorShare' => [
                    'excludingTaxes' => 143.0315,
                    'taxes' => 2.7644,
                    'includingTaxes' => 145.7959,
                ],
            ],
            9 => [
                'totals' => [
                    'excludingTaxes' => 53.0852,
                    'taxes' => 1.1148,
                    'includingTaxes' => 54.2,
                ],
                'shippingCosts' => [
                    'excludingTaxes' => 0.0,
                    'taxes' => 0.0,
                    'includingTaxes' => 0.0,
                ],
                'commissions' => [
                    'excludingTaxes' => 0.56,
                    'taxes' => 0.112,
                    'includingTaxes' => 0.672,
                ],
                'vendorShare' => [
                    'excludingTaxes' => 52.5252,
                    'taxes' => 1.0028,
                    'includingTaxes' => 53.528,
                ],
            ],
            12 => [
                'totals' => [
                    'excludingTaxes' => 53.0852,
                    'taxes' => 1.1148,
                    'includingTaxes' => 54.2,
                ],
                'shippingCosts' => [
                    'excludingTaxes' => 0.0,
                    'taxes' => 0.0,
                    'includingTaxes' => 0.0,
                ],
                'commissions' => [
                    'excludingTaxes' => 0.56,
                    'taxes' => 0.112,
                    'includingTaxes' => 0.672,
                ],
                'vendorShare' => [
                    'excludingTaxes' => 52.5252,
                    'taxes' => 1.0028,
                    'includingTaxes' => 53.528,
                ],
            ],
        ];
    }

    public function testGetAnOrderWithMarketplaceDiscount(): void
    {
        $order = $this->buildVendorOrderService()->getOrderById(12);
        static::assertSame(10.0, $order->getMarketplaceDiscountTotal());
        static::assertSame(5.5, $order->getCustomerTotal());
    }

    public function testGetTransactions(): void
    {
        $transactions = $this->buildVendorOrderService()->getTransactions(12);
        static::assertCount(1, $transactions);

        static::assertInstanceOf(Transaction::class, $transactions[0]);
        static::assertSame(36, \strlen($transactions[0]->getId()));
        static::assertSame("a123456789", $transactions[0]->getTransactionReference());
        static::assertEquals(TransactionType::TRANSFER(), $transactions[0]->getType());
        static::assertEquals(TransactionStatus::SUCCESS(), $transactions[0]->getStatus());
        static::assertSame(10.0, $transactions[0]->getAmount());
        static::assertSame("LemonWay", $transactions[0]->getProcessorName());
        static::assertNull($transactions[0]->getProcessorInformation());
    }

    /**
     * @param string $userEmail
     * @param string $password
     * @param bool $throwException determine si la requête va échouer.
     * @param int $exceptionCode code de retour attendu si la requête échoue.
     *
     * @throws GuzzleException
     * @throws AuthenticationRequired
     *
     * @dataProvider providerDownloadPdfInvoice
     */
    public function testDownloadPdfInvoice(string $userEmail, string $password, bool $throwException, int $exceptionCode): void
    {
        // Le client ne peut pas utiliser cet endpoint.
        if ($throwException) {
            static::expectException(ClientException::class);
            static::expectExceptionCode($exceptionCode);
        }

        $pdf = $this->buildVendorOrderService($userEmail, $password)->downloadPdfInvoice(4);

        // Les marchants et les admins ne lèvent pas d'exceptions.
        if (false === $throwException) {
            $pdfHeader = '%PDF-1.4';
            $pdfContents = $pdf->getContents();
            static::assertStringStartsWith($pdfHeader, $pdfContents);
            static::assertGreaterThan(mb_strlen($pdfHeader), mb_strlen($pdfContents));
        }
    }

    public function providerDownloadPdfInvoice(): array
    {
        return [
            ['user@wizaplace.com', 'password', true, 403],
            ['admin@wizaplace.com', 'password', false, 0],
            ['vendor@world-company.com', 'password-vendor', false, 0],
        ];
    }

    public function testGetOrdersWithSubscription(): void
    {
        $orders = $this
            ->buildVendorOrderService("admin@wizaplace.com", "password")
            ->getPaginatedOrders();

        static::assertEquals(12, $orders->getTotal());
        static::assertUuid(($orders->getItems()[0])->getSubscriptionId());
        static::assertNull(($orders->getItems()[1])->getSubscriptionId());
    }

    public function testGetOrdersWithIsPaid(): void
    {
        $orders = $this
            ->buildVendorOrderService("admin@wizaplace.com", "password")
            ->getPaginatedOrders();

        static::assertEquals(12, $orders->getTotal());
        static::assertFalse($orders->getItems()[0]->isPaid());
        static::assertTrue($orders->getItems()[1]->isPaid());
    }

    public function testGetSubscriptions(): void
    {
        $subscriptions = $this->buildVendorOrderService("admin@wizaplace.com", "password")->getSubscriptions(210011);

        static::assertCount(2, $subscriptions);
        static::assertInstanceOf(SubscriptionSummary::class, $subscriptions[0]);
        static::assertInstanceOf(SubscriptionSummary::class, $subscriptions[1]);
    }

    public function testGetVendorOrderCreditNotes(): void
    {
        $orderService = $this->buildVendorOrderService('admin@wizaplace.com', 'password');
        $creditNotes = $orderService->getOrderCreditNotes(14);

        static::assertCount(2, $creditNotes);

        $creditNote = $creditNotes[0];

        static::assertSame(1, $creditNote->getRefundId());
    }

    public function testGetVendorOrderCreditNote(): void
    {
        $orderService = $this->buildVendorOrderService('admin@wizaplace.com', 'password');
        $creditNote = $orderService->getOrderCreditNote(14, 2);

        $pdfHeader = '%PDF-1.4';
        $pdfContent = $creditNote->getContents();
        static::assertStringStartsWith($pdfHeader, $pdfContent);
        static::assertGreaterThan(\strlen($pdfHeader), \strlen($pdfContent));
    }

    public function testVendorGetOrderRefunds(): void
    {
        $orderService = $this->buildVendorOrderService('admin@wizaplace.com', 'password');
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

    public function testVendorGetOrderRefund(): void
    {
        $orderService = $this->buildVendorOrderService('admin@wizaplace.com', 'password');
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

    public function testGetAnOrderById(): void
    {
        $order = $this->buildVendorOrderService()->getOrderById(7);

        static::assertInstanceOf(Order::class, $order);
        $shippingAddress = $order->getShippingAddress();
        $shipping = $order->getShipping()[0];
        static::assertSame([], $order->getShipmentsIds());

        static::assertInstanceOf(OrderAddress::class, $shippingAddress);
        static::assertSame('University of Southern California', $shippingAddress->getCompany());

        static::assertInstanceOf(Shipping::class, $shipping);
        static::assertNotNull($shipping->getId());
        static::assertNotNull($shipping->getName());
        static::assertNotNull($shipping->getDeliveryTime());
        static::assertTrue($shipping->isEnabled());
        static::assertNotNull($shipping->getRates());
        static::assertNotNull($shipping->getDescription());
        static::assertSame(1, $shipping->getId());
        static::assertSame(40, $shipping->getPosition());
        static::assertSame("TNT Express", $shipping->getName());
        static::assertSame("24h", $shipping->getDeliveryTime());
    }

    public function testGetOrderShippingAddress(): void
    {
        $order = $this->buildVendorOrderService()->getOrderById(12);

        static::assertInstanceOf(Order::class, $order);

        $shippingAddress = $order->getShippingAddress();
        static::assertInstanceOf(OrderAddress::class, $shippingAddress);
        static::assertSame('mr', $shippingAddress->getTitle()->getValue());

        $billingAddress = $order->getBillingAddress();
        static::assertInstanceOf(OrderAddress::class, $billingAddress);
        static::assertSame('mr', $billingAddress->getTitle()->getValue());
    }

    public function testPostOrderAttachment(): void
    {
        $orderService = $this->buildVendorOrderService();

        $file = $this->mockUploadedFile('minimal.pdf');

        $orderAttachmentData = [
            'name' => 'Faridoux',
            'type' => OrderAttachmentType::CUSTOMER_INVOICE(),
            'file' => $file->getStream(),
            'filename' => $file->getClientFilename(),
        ];

        $orderAttachment = $orderService->postOrderAttachment(1, $orderAttachmentData);

        static::assertTrue(\is_string($orderAttachment->getId()));
        static::assertSame('Faridoux', $orderAttachment->getName());
        static::assertSame('minimal.pdf', $orderAttachment->getFilename());
        static::assertEquals(OrderAttachmentType::CUSTOMER_INVOICE(), $orderAttachment->getType());
        static::assertTrue(\is_string($orderAttachment->getUrl()));
        static::assertSame(6, $orderAttachment->getCreatedBy());
        static::assertSame(6, $orderAttachment->getUpdatedBy());
        static::assertEquals(new \DateTime('2020-04-06T13:50:34+02:00'), $orderAttachment->getCreatedAt());
        static::assertEquals(new \DateTime('2020-04-06T13:50:34+02:00'), $orderAttachment->getUpdatedAt());
    }

    public function testGetOrderAttachment(): void
    {
        $orderService = $this->buildVendorOrderService();

        $file = $this->mockUploadedFile('minimal.pdf');

        $orderAttachmentData = [
            'name' => 'Faridoux',
            'type' => OrderAttachmentType::CUSTOMER_INVOICE(),
            'file' => $file->getStream(),
            'filename' => $file->getClientFilename(),
        ];

        $orderAttachment = $orderService->postOrderAttachment(1, $orderAttachmentData);

        $getOrderAttachment = $orderService->getOrderAttachment(1, $orderAttachment->getId());

        static::assertSame($orderAttachment->getId(), $getOrderAttachment->getId());
        static::assertSame($orderAttachment->getName(), $getOrderAttachment->getName());
        static::assertSame($orderAttachment->getFilename(), $getOrderAttachment->getFilename());
        static::assertEquals($orderAttachment->getType(), $getOrderAttachment->getType());
        static::assertSame($orderAttachment->getUrl(), $getOrderAttachment->getUrl());
        static::assertEquals($orderAttachment->getCreatedAt(), $getOrderAttachment->getCreatedAt());
        static::assertEquals($orderAttachment->getUpdatedAt(), $getOrderAttachment->getUpdatedAt());
        static::assertSame($orderAttachment->getCreatedBy(), $getOrderAttachment->getCreatedBy());
        static::assertSame($orderAttachment->getUpdatedBy(), $getOrderAttachment->getUpdatedBy());
    }

    public function testListOrderAttachments(): void
    {
        $orderService = $this->buildVendorOrderService();

        $file = $this->mockUploadedFile('minimal.pdf');

        $orderAttachmentData = [
            'name' => 'Faridoux',
            'type' => OrderAttachmentType::CUSTOMER_INVOICE(),
            'file' => $file->getStream(),
            'filename' => $file->getClientFilename(),
        ];

        $orderService->postOrderAttachment(1, $orderAttachmentData);
        $orderService->postOrderAttachment(1, $orderAttachmentData);
        $orderAttachmentData['type'] = OrderAttachmentType::DELIVERY_BILL();
        $orderService->postOrderAttachment(1, $orderAttachmentData);
        $orderAttachment1 = $orderService->postOrderAttachment(1, $orderAttachmentData);
        $orderAttachment2 = $orderService->postOrderAttachment(1, $orderAttachmentData);

        $listOrderAttachments = $orderService->listOrderAttachment(
            1,
            (new OrderAttachmentFilter())
                ->setOffset(1)
                ->setLimit(4)
                ->setType(OrderAttachmentType::DELIVERY_BILL())
        );

        $items = $listOrderAttachments->getItems();
        static::assertTrue($listOrderAttachments instanceof PaginatedData);
        static::assertSame(1, $listOrderAttachments->getOffset());
        static::assertSame(4, $listOrderAttachments->getLimit());
        static::assertSame(2, $listOrderAttachments->getTotal());
        static::assertCount(2, $items);

        static::assertInstanceOf(OrderAttachment::class, $items[0]);
        static::assertSame($orderAttachment1->getId(), $items[0]->getId());
        static::assertSame($orderAttachment1->getName(), $items[0]->getName());
        static::assertSame($orderAttachment1->getFilename(), $items[0]->getFilename());
        static::assertEquals($orderAttachment1->getType(), $items[0]->getType());
        static::assertSame($orderAttachment1->getUrl(), $items[0]->getUrl());
        static::assertEquals($orderAttachment1->getCreatedAt(), $items[0]->getCreatedAt());
        static::assertEquals($orderAttachment1->getUpdatedAt(), $items[0]->getUpdatedAt());
        static::assertSame($orderAttachment1->getCreatedBy(), $items[0]->getCreatedBy());
        static::assertSame($orderAttachment1->getUpdatedBy(), $items[0]->getUpdatedBy());

        static::assertInstanceOf(OrderAttachment::class, $items[1]);
        static::assertSame($orderAttachment2->getId(), $items[1]->getId());
        static::assertSame($orderAttachment2->getName(), $items[1]->getName());
        static::assertSame($orderAttachment2->getFilename(), $items[1]->getFilename());
        static::assertEquals($orderAttachment2->getType(), $items[1]->getType());
        static::assertSame($orderAttachment2->getUrl(), $items[1]->getUrl());
        static::assertEquals($orderAttachment2->getCreatedAt(), $items[1]->getCreatedAt());
        static::assertEquals($orderAttachment2->getUpdatedAt(), $items[1]->getUpdatedAt());
        static::assertSame($orderAttachment2->getCreatedBy(), $items[1]->getCreatedBy());
        static::assertSame($orderAttachment2->getUpdatedBy(), $items[1]->getUpdatedBy());
    }

    public function testUpdateOrderAttachment(): void
    {
        $orderService = $this->buildVendorOrderService();

        $file = $this->mockUploadedFile('minimal.pdf');

        $orderAttachmentData = [
            'name' => 'Faridoux',
            'type' => OrderAttachmentType::CUSTOMER_INVOICE(),
            'file' => $file->getStream(),
            'filename' => $file->getClientFilename(),
        ];

        $orderAttachment = $orderService->postOrderAttachment(1, $orderAttachmentData);

        $payload = [
            'name' => 'TeamFaridouxRPZ',
            'type' => OrderAttachmentType::DELIVERY_BILL(),
        ];

        $orderAttachmentUpdated = $orderService->updateOrderAttachment(1, $orderAttachment->getId(), $payload);

        static::assertSame($orderAttachment->getId(), $orderAttachmentUpdated->getId());
        static::assertSame('TeamFaridouxRPZ', $orderAttachmentUpdated->getName());
        static::assertSame('minimal.pdf', $orderAttachmentUpdated->getFilename());
        static::assertEquals(OrderAttachmentType::DELIVERY_BILL(), $orderAttachmentUpdated->getType());
        static::assertEquals($orderAttachment->getCreatedAt(), $orderAttachmentUpdated->getCreatedAt());
        static::assertNotEquals($orderAttachment->getUpdatedAt(), $orderAttachmentUpdated->getUpdatedAt());
        static::assertSame($orderAttachment->getCreatedBy(), $orderAttachmentUpdated->getCreatedBy());
    }

    public function testDeleteOrderAttachment(): void
    {
        $orderService = $this->buildVendorOrderService();

        $file = $this->mockUploadedFile('minimal.pdf');

        $orderAttachmentData = [
            'name' => 'Faridoux',
            'type' => OrderAttachmentType::CUSTOMER_INVOICE(),
            'file' => $file->getStream(),
            'filename' => $file->getClientFilename(),
        ];

        $orderAttachment = $orderService->postOrderAttachment(1, $orderAttachmentData);

        $orderService->deleteOrderAttachment(1, $orderAttachment->getId());

        $this->expectExceptionCode(Response::HTTP_NOT_FOUND);
        $this->expectExceptionMessage('Order #1 or attachment #' . $orderAttachment->getId() . ' not found');
        $orderService->getOrderAttachment(1, $orderAttachment->getId());
    }

    public function testPutOrderMarkAsPaidNotFound(): void
    {
        static::expectException(NotFound::class);

        $orderService = $this->buildVendorOrderService('admin@wizaplace.com', 'password');
        $orderService->orderMarkAsPaid(4756687);
    }

    public function testPutOrderMarkAsPaidUnauthorized(): void
    {
        static::expectException(ClientException::class);
        static::expectExceptionCode(401);

        $orderService = $this->buildVendorOrderService();

        $orderService->orderMarkAsPaid(13);
    }

    public function testPutOrderMarkAsPaid(): void
    {
        $apiClient = $this->buildApiClient();
        $apiClient->authenticate('customer-1@world-company.com', 'password-customer-1');
        $basketService = new BasketService($apiClient);
        $orderService = new OrderService($apiClient);
        $basket = $basketService->createEmptyBasket();
        $basketService->addProductToBasket($basket->getId(), new DeclinationId('1'), 1);
        $basket = $basketService->getBasket($basket->getId());
        $availablePayments = $basketService->getPayments($basket->getId());
        $selectedPayment = $availablePayments[0]->getId();
        $redirectUrl = 'https://demo.loc/order/confirm';
        $cssUrl = 'https://demo.loc/custom.css';

        $paymentInformation = $basketService->checkout(
            $basket->getId(),
            $selectedPayment,
            true,
            $redirectUrl,
            $cssUrl
        );

        $orders = $paymentInformation->getOrders();
        $orderService_admin = $this->buildVendorOrderService('admin@wizaplace.com', 'password');
        $order = $orderService_admin->getOrderById($orders[0]->getId());
        static::assertFalse($order->isPaid());
        $orderService_admin->orderMarkAsPaid($order->getOrderId());
        $order = $orderService_admin->getOrderById($order->getOrderId());
        static::assertTrue($order->isPaid());
    }

    public function testGetOrdersWithRefundData(): void
    {
        $orders = $this
            ->buildVendorOrderService("admin@wizaplace.com", "password")
            ->getPaginatedOrders();

        static::assertEquals(0, $orders->getTotal());
        foreach ($orders as $order) {
            static::assertInternalType('boolean', $order->isRefunded());
        }
    }

    public function testGetOrderByIdWithRefundData(): void
    {
        $orderService = $this->buildVendorOrderService("admin@wizaplace.com", "password");

        $order = $orderService->getOrderById(13);

        static::assertFalse($order->isRefunded());
    }

    private function buildVendorOrderService(string $email = 'vendor@world-company.com', string $password = 'password-vendor'): OrderService
    {
        $apiClient = $this->buildApiClient();
        $apiClient->authenticate($email, $password);

        return new OrderService($apiClient);
    }

    public function testGetOrdersWithBalance(): void
    {
        $orders = $this
            ->buildVendorOrderService("admin@wizaplace.com", "password")
            ->getPaginatedOrders();

        static::assertEquals(0, $orders->getTotal());
        foreach ($orders as $order) {
            static::assertSame(0.0, $order->getBalance());
        }
    }

    public function testGetOrderByIdWithBalance(): void
    {
        $orderService = $this->buildVendorOrderService("admin@wizaplace.com", "password");

        $order = $orderService->getOrderById(1);

        static::assertSame(0.0, $order->getBalance());
    }

    public function testSetInvoiceNumberAfterOnlyAccept(): void
    {
        $orderService = $this->buildVendorOrderService();

        $orderService->approveOrder(5);
        // set invoice number
        $orderService->setInvoiceNumber(5, "00072");
        $invoiceNumber = $orderService->getOrderById(5)->getInvoiceNumber();
        static::assertSame("00072", $invoiceNumber);
    }

    public function testMarkAsDeliveredShipment(): void
    {
        $orderService = $this->buildVendorOrderService("admin@wizaplace.com", "password");
        $orderId = 31;
        $order = $orderService->getOrderById($orderId);
        $items = $order->getItems();
        static::assertCount(1, $items);

        /** @var OrderItem $item */
        $item = reset($items);
        $itemId = $item->getItemId();
        static::assertSame(10, $item->getQuantity());
        static::assertSame(0, $item->getQuantityShipped());
        static::assertSame(10, $item->getQuantityToShip());

        $itemsShipped[$itemId] = 4;
        $firstShipmentId = $orderService->createShipment(
            (new CreateShipmentCommand($orderId, '0ABC0123456798'))
                ->setComment('great shipment')
                ->setLabelUrl('http://mondialrelay.com/shipment-created')
                ->setShippedQuantityByItemId($itemsShipped)
        );

        static::assertGreaterThan(0, $firstShipmentId);
        $shipment = $orderService->getShipmentById($firstShipmentId);
        static::assertInstanceOf(Shipment::class, $shipment);
        static::assertNull($shipment->getDeliveredDate());

        $itemsShipped[$itemId] = 6;
        $secondShipmentId = $orderService->createShipment(
            (new CreateShipmentCommand($orderId, '0ABC0123456798'))
                ->setComment('great shipment')
                ->setLabelUrl('http://mondialrelay.com/shipment-created')
                ->setShippedQuantityByItemId($itemsShipped)
        );

        static::assertGreaterThan(0, $secondShipmentId);
        $shipment = $orderService->getShipmentById($secondShipmentId);
        static::assertInstanceOf(Shipment::class, $shipment);
        static::assertNull($shipment->getDeliveredDate());

        $shipments = $orderService->listShipments($orderId);
        static::assertCount(2, $shipments);

        $orderService->shipmentMarkAsDelivered($firstShipmentId);
        $shipment = $orderService->getShipmentById($firstShipmentId);
        static::assertNotNull($shipment->getDeliveredDate());

        $orderService->shipmentMarkAsDelivered($secondShipmentId);
        $shipment = $orderService->getShipmentById($secondShipmentId);
        static::assertNotNull($shipment->getDeliveredDate());
    }

    public function testMarkOrderAsDelivered(): void
    {
        $orderService = $this->buildVendorOrderService("admin@wizaplace.com", "password");
        $orderId = 33;
        $order = $orderService->getOrderById($orderId);
        $items = $order->getItems();
        static::assertCount(1, $items);

        /** @var OrderItem $item */
        $item = reset($items);
        $itemId = $item->getItemId();
        static::assertSame(10, $item->getQuantity());
        static::assertSame(0, $item->getQuantityShipped());
        static::assertSame(10, $item->getQuantityToShip());

        $itemsShipped[$itemId] = 4;
        $firstShipmentId = $orderService->createShipment(
            (new CreateShipmentCommand($orderId, '0ABC0123456798'))
                ->setComment('great shipment')
                ->setLabelUrl('http://mondialrelay.com/shipment-created')
                ->setShippedQuantityByItemId($itemsShipped)
        );

        static::assertGreaterThan(0, $firstShipmentId);
        $shipment = $orderService->getShipmentById($firstShipmentId);
        static::assertInstanceOf(Shipment::class, $shipment);
        static::assertNull($shipment->getDeliveredDate());

        $itemsShipped[$itemId] = 6;
        $secondShipmentId = $orderService->createShipment(
            (new CreateShipmentCommand($orderId, '0ABC0123456798'))
                ->setComment('great shipment')
                ->setLabelUrl('http://mondialrelay.com/shipment-created')
                ->setShippedQuantityByItemId($itemsShipped)
        );

        static::assertGreaterThan(0, $secondShipmentId);
        $shipment = $orderService->getShipmentById($secondShipmentId);
        static::assertInstanceOf(Shipment::class, $shipment);
        static::assertNull($shipment->getDeliveredDate());

        $shipments = $orderService->listShipments($orderId);
        static::assertCount(2, $shipments);

        $orderService->orderMarkAsDelivered($orderId);

        $shipments = $orderService->listShipments($orderId);
        foreach ($shipments as $shipment) {
            static::assertNotNull($shipment->getDeliveredDate());
        }
    }

    public function testDoNotCreateInvoiceNumber(): void
    {
        $orderService = $this->buildVendorOrderService();

        // Call the endpoint [PUT] /order/{orderId}
        $orderService->acceptOrder(5, false, '', false);
        $invoiceNumber = $orderService->getOrderById(5)->getInvoiceNumber();
        static::assertEmpty($invoiceNumber);
    }

    public function testDoNotCreateInvoice(): void
    {
        $orderService = $this->buildVendorOrderService();

        // Call the endpoint [PUT] /order/{orderId}
        $orderService->acceptOrder(5, false, '', false);
        $doNotCreateInvoice = $orderService->getOrderById(5)->isDoNotCreateInvoice();
        static::assertTrue($doNotCreateInvoice);
    }

    public function testPatchExtra(): void
    {
        $orderService = $this->buildVendorOrderService("admin@wizaplace.com", "Windows.98");

        $extra = ['field' => 'value1', 'field2' => 'value2'];
        $orderService->patchExtra(5, $extra);

        $order = $orderService->getOrderById(5);
        static::assertSame($extra, $order->getExtra());
    }

    public function testGetOrdersDisplayingParentOrderId(): void
    {
        $orders = $this
            ->buildVendorOrderService("admin@wizaplace.com", "password")
            ->getPaginatedOrders();

        static::assertSame(14, $orders->getItems()[0]->getParentOrderId());
        static::assertSame(14, $orders->getItems()[1]->getParentOrderId());
    }

    public function testGetOrderByIdDisplayingParentOrderId(): void
    {
        $orderChild1 = $this->buildVendorOrderService("admin@wizaplace.com", "Windows.98")->getOrderById(15);
        $orderChild2 = $this->buildVendorOrderService("admin@wizaplace.com", "Windows.98")->getOrderById(16);

        static::assertSame(14, $orderChild1->getParentOrderId());
        static::assertSame(14, $orderChild2->getParentOrderId());
    }
}
