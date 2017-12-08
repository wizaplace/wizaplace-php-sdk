<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types=1);

namespace Wizaplace\SDK\Tests\Vendor\Order;

use Wizaplace\SDK\Tests\ApiTestCase;
use Wizaplace\SDK\Vendor\Order\OrderService;

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

    private function buildVendorOrderService(string $email = 'vendor@world-company.com', string $password = 'password-vendor'): OrderService
    {
        $apiClient = $this->buildApiClient();
        $apiClient->authenticate($email, $password);

        return new OrderService($apiClient);
    }
}
