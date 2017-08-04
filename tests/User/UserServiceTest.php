<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\Tests\User;

use Wizaplace\Authentication\AuthenticationRequired;
use Wizaplace\Authentication\BadCredentials;
use Wizaplace\Tests\ApiTestCase;
use Wizaplace\User\UserAlreadyExists;
use Wizaplace\User\UserService;

/**
 * @see UserService
 */
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
        $this->assertEquals($userEmail, $user->getEmail());
        $this->assertEquals($userId, $user->getId());
        $this->assertEquals('', $user->getFirstname());
        $this->assertEquals('', $user->getLastname());
        $this->assertEquals([
            38 => null,
            'firstname' => '',
            'lastname' => '',
            40 => null,
            'phone' => '',
            'address' => '',
            'address_2' => '',
            'zipcode' => '',
            'city' => '',
            'country' => 'FR',
        ], $user->getShippingAddress());
        $this->assertEquals([
            37 => null,
            'firstname' => '',
            'lastname' => '',
            39 => null,
            'phone' => '',
            'address' => '',
            'address_2' => '',
            'zipcode' => '',
            'city' => '',
            'country' => 'FR',
        ], $user->getBillingAddress());
    }

    public function testCreateAlreadyExistingUser()
    {
        $client = $this->buildApiClient();
        $userService = new UserService($client);

        // create already existing user
        $this->expectException(UserAlreadyExists::class);
        $userService->register('user@wizaplace.com', 'whatever');
    }

    public function testRecoverPassword()
    {
        $client = $this->buildApiClient();
        $userService = new UserService($client);

        $userEmail = 'user1237@example.com';
        $userPassword = 'password';
        $userService->register($userEmail, $userPassword);


        $this->assertNull($userService->recoverPassword($userEmail));
    }

    public function testRecoverPasswordForNonExistingEmail()
    {
        $client = $this->buildApiClient();
        $userService = new UserService($client);

        $this->assertNull($userService->recoverPassword('404@example.com'));
    }

    public function testChangePassword()
    {
        $userEmail = 'user1236@example.com';
        $userPassword = 'password';

        $client = $this->buildApiClient();
        $userService = new UserService($client);

        // create new user
        $userId = $userService->register($userEmail, $userPassword);

        // authenticate with newly created user
        $client->authenticate($userEmail, $userPassword);

        $userService->changePassword($userId, 'hunter2');

        $this->assertNotEmpty($client->authenticate($userEmail, 'hunter2'));
        $this->expectException(BadCredentials::class);
        $this->assertNotEmpty($client->authenticate($userEmail, $userPassword));
    }

    public function testChangePasswordAnonymously()
    {
        $this->expectException(AuthenticationRequired::class);
        (new UserService($this->buildApiClient()))->changePassword(1, 'hunter2');
    }
}
