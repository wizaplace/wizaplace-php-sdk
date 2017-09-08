<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\SDK\Tests;

use Wizaplace\SDK\Authentication\BadCredentials;
use Wizaplace\SDK\Order\OrderService;

final class ApiClientTest extends ApiTestCase
{
    public function testAuthentication()
    {
        $apiClient = $this->buildApiClient();
        $orderService = new OrderService($apiClient);

        $apiKey = $apiClient->authenticate("admin@wizaplace.com", "password");
        $this->assertNotNull($apiKey);

        // Test an authenticated call.
        // If the authentication did not "register" properly, we will get an exception and the test will fail.
        $orderService->getOrders();
    }

    public function testBadCredentialsAuthentication()
    {
        $apiClient = $this->buildApiClient();

        $this->expectException(BadCredentials::class);
        $apiClient->authenticate("admin@wizaplace.com", "wrongPassword");
    }
}
