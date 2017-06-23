<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\Tests\User;

use Wizaplace\Tests\ApiTestCase;

class UserServiceTest extends ApiTestCase
{
    public function testAuthentication()
    {
        $servicesFactory = $this->buildServicesFactory();
        $userService = $servicesFactory->userService();
        $apiKey = $userService->authenticate("admin@wizaplace.com", "password");
        $this->assertNotNull($apiKey);

        // Test an authenticated call.
        // If the authentication did not "register" properly, we will get an exception and the test will fail.
        $servicesFactory->orderService()->getOrders();
    }


}
