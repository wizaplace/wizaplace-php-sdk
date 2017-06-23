<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\Tests\User;

use Wizaplace\Order\OrderService;
use Wizaplace\Tests\ApiTestCase;
use Wizaplace\User\UserService;

class UserServiceTest extends ApiTestCase
{
    public function testAuthentication()
    {
        $apiClient = $this->buildApiClient();
        $userService = new UserService($apiClient);
        $orderService = new OrderService($apiClient);

        $apiKey = $userService->authenticate("admin@wizaplace.com", "password");
        $this->assertNotNull($apiKey);

        // Test an authenticated call.
        // If the authentication did not "register" properly, we will get an exception and the test will fail.
        $orderService->getOrders();
    }
}
