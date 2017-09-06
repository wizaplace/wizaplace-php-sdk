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
use Wizaplace\User\UpdateUserCommand;
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

        // shipping address
        $this->assertSame('', $user->getShippingAddress()->getTitle());
        $this->assertSame('', $user->getShippingAddress()->getFirstName());
        $this->assertSame('', $user->getShippingAddress()->getLastName());
        $this->assertSame('', $user->getShippingAddress()->getCompany());
        $this->assertSame('', $user->getShippingAddress()->getPhone());
        $this->assertSame('', $user->getShippingAddress()->getAddress());
        $this->assertSame('', $user->getShippingAddress()->getAddressSecondLine());
        $this->assertSame('', $user->getShippingAddress()->getZipCode());
        $this->assertSame('', $user->getShippingAddress()->getCity());
        $this->assertSame('FR', $user->getShippingAddress()->getCountry());

        // billing address
        $this->assertSame('', $user->getBillingAddress()->getTitle());
        $this->assertSame('', $user->getBillingAddress()->getFirstName());
        $this->assertSame('', $user->getBillingAddress()->getLastName());
        $this->assertSame('', $user->getBillingAddress()->getCompany());
        $this->assertSame('', $user->getBillingAddress()->getPhone());
        $this->assertSame('', $user->getBillingAddress()->getAddress());
        $this->assertSame('', $user->getBillingAddress()->getAddressSecondLine());
        $this->assertSame('', $user->getBillingAddress()->getZipCode());
        $this->assertSame('', $user->getBillingAddress()->getCity());
        $this->assertSame('FR', $user->getBillingAddress()->getCountry());
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


        $userService->updateUser(
            (new UpdateUserCommand())
                ->setUserId($userId)
                ->setEmail('user43@example.com')
                ->setFirstName('Jacques')
                ->setLastName('Jules')
                ->setTitle(UserTitle::MR())
        );

        $client->authenticate('user43@example.com', 'password');

        $user = $userService->getProfileFromId($userId);
        $this->assertSame('user43@example.com', $user->getEmail());
        $this->assertTrue(UserTitle::MR()->equals($user->getTitle()));
        $this->assertSame('Jacques', $user->getFirstname());
        $this->assertSame('Jules', $user->getLastname());
    }

    public function testUpdateUserWithDefaultValuesOnly()
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

        $userService->updateUser(
            (new UpdateUserCommand())
                ->setUserId($userId)
                ->setEmail('user43@example.com')
                ->setFirstName('Jacques')
                ->setLastName('Jules')
        );

        $client->authenticate('user43@example.com', 'password');

        $user = $userService->getProfileFromId($userId);
        $this->assertSame('user43@example.com', $user->getEmail());
        $this->assertNull($user->getTitle());
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
