<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\Tests\User;

use Wizaplace\User\UserService;
use Wizaplace\Tests\ApiTestCase;

class UserServiceTest extends ApiTestCase
{
    public function testCreateUser()
    {
        $userEmail = 'user@example.com';
        $userPassword = 'password';

        // authenticate
        $client = $this->buildApiClient();
        $userService = new UserService($client);

        // create new user
        $userId = $userService->register($userEmail, $userPassword);

        // authenticate with newly created user
        $client->authenticate($userEmail, $userPassword);

        // fetch user
        $user = $userService->getProfileFromId($userId);

        $this->assertNotNull($user, 'User exists');
        $this->assertEquals($user->getEmail(), $userEmail);
    }
}
