<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\Tests\User;

use Wizaplace\Authentication\AuthenticationRequired;
use Wizaplace\Authentication\BadCredentials;
use Wizaplace\Tests\ApiTestCase;
use Wizaplace\User\UserAlreadyExists;
use Wizaplace\User\UserService;
use Wizaplace\User\UserTitle;

/**
 * @see UserService
 */
final class UserServiceTest extends ApiTestCase
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
        $this->assertSame($userEmail, $user->getEmail());
        $this->assertSame($userId, $user->getId());
        $this->assertSame(null, $user->getTitle());
        $this->assertSame('', $user->getFirstname());
        $this->assertSame('', $user->getLastname());
        $this->assertSame([
            'title' => '',
            'firstname' => '',
            'lastname' => '',
            'company' => '',
            'phone' => '',
            'address' => '',
            'address_2' => '',
            'zipcode' => '',
            'city' => '',
            'country' => 'FR',
            37 => 3,
            38 => 3,
            40 => '',
            39 => '',
        ], $user->getShippingAddress());
        $this->assertSame([
            'title' => '',
            'firstname' => '',
            'lastname' => '',
            'company' => '',
            'phone' => '',
            'address' => '',
            'address_2' => '',
            'zipcode' => '',
            'city' => '',
            'country' => 'FR',
            37 => 3,
            38 => 3,
            40 => '',
            39 => '',
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

    public function testUpdateUser()
    {
        $client = $this->buildApiClient();
        $userService = new UserService($client);

        // create new user
        $userId = $userService->register('user42@example.com', 'password', 'Jean', 'Paul');

        $client->authenticate('user42@example.com', 'password');
        $user = $userService->getProfileFromId($userId);
        $this->assertSame('user42@example.com', $user->getEmail());
        $this->assertSame(null, $user->getTitle());
        $this->assertSame('Jean', $user->getFirstname());
        $this->assertSame('Paul', $user->getLastname());

        $userService->updateUser($userId, 'user43@example.com', 'Jacques', 'Jules', UserTitle::MR());

        $client->authenticate('user43@example.com', 'password');

        $user = $userService->getProfileFromId($userId);
        $this->assertSame('user43@example.com', $user->getEmail());
        $this->assertTrue(UserTitle::MR()->equals($user->getTitle()));
        $this->assertSame('Jacques', $user->getFirstname());
        $this->assertSame('Jules', $user->getLastname());
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
