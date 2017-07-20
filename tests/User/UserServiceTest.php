<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\Tests\User;

use Wizaplace\Tests\ApiTestCase;
use Wizaplace\User\UserAlreadyExists;
use Wizaplace\User\UserService;

class UserServiceTest extends ApiTestCase
{
    public function testCreateUser()
    {
        $userEmail = 'user@example.com';
        $userPassword = 'password';

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

    public function testCreateAlreadyExistingUser()
    {
        $client = $this->buildApiClient();
        $userService = new UserService($client);

        // create already existing user
        $this->expectException(UserAlreadyExists::class);
        $userService->register('user@wizaplace.com', 'whatever');
    }
}
