<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\SDK\Tests\User;

use Wizaplace\SDK\Authentication\AuthenticationRequired;
use Wizaplace\SDK\Authentication\BadCredentials;
use Wizaplace\SDK\Tests\ApiTestCase;
use Wizaplace\SDK\User\UpdateUserAddressCommand;
use Wizaplace\SDK\User\UpdateUserAddressesCommand;
use Wizaplace\SDK\User\UpdateUserCommand;
use Wizaplace\SDK\User\UserAlreadyExists;
use Wizaplace\SDK\User\UserService;
use Wizaplace\SDK\User\UserTitle;

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
        $this->assertSame(null, $user->getBirthday());

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
        $this->assertSame(null, $user->getBirthday());


        $userService->updateUser(
            (new UpdateUserCommand())
                ->setUserId($userId)
                ->setEmail('user43@example.com')
                ->setFirstName('Jacques')
                ->setLastName('Jules')
                ->setTitle(UserTitle::MR())
                ->setBirthday(\DateTime::createFromFormat('Y-m-d', '1963-02-17'))
        );

        $client->authenticate('user43@example.com', 'password');

        $user = $userService->getProfileFromId($userId);
        $this->assertSame('user43@example.com', $user->getEmail());
        $this->assertTrue(UserTitle::MR()->equals($user->getTitle()));
        $this->assertSame('Jacques', $user->getFirstname());
        $this->assertSame('Jules', $user->getLastname());
        $this->assertSame('1963-02-17', $user->getBirthday()->format('Y-m-d'));
    }

    public function testUpdateUserAddresses()
    {
        $client = $this->buildApiClient();
        $userService = new UserService($client);

        // create new user
        $userId = $userService->register('user12@example.com', 'password', 'Jean', 'Paul');

        $client->authenticate('user12@example.com', 'password');
        $user = $userService->getProfileFromId($userId);
        $this->assertSame('', $user->getShippingAddress()->getTitle());
        $this->assertSame('Jean', $user->getShippingAddress()->getFirstName());
        $this->assertSame('Paul', $user->getShippingAddress()->getLastName());

        $this->assertSame('', $user->getBillingAddress()->getTitle());
        $this->assertSame('Jean', $user->getBillingAddress()->getFirstName());
        $this->assertSame('Paul', $user->getBillingAddress()->getLastName());


        $userService->updateUserAdresses(
            (new UpdateUserAddressesCommand())
                ->setUserId($userId)
                ->setShippingAddress(
                    (new UpdateUserAddressCommand())
                        ->setTitle(UserTitle::MR())
                        ->setFirstName('Pierre')
                        ->setLastName('Jacques')
                        ->setCountry('FR')
                        ->setCity('Lyon')
                        ->setAddress('24 rue de la gare')
                        ->setAddressSecondLine('1er étage')
                        ->setCompany('Wizaplace')
                        ->setPhone('0123456798')
                        ->setZipCode('69009')
                )
                ->setBillingAddress(
                    (new UpdateUserAddressCommand())
                        ->setTitle(UserTitle::MRS())
                        ->setFirstName('Jeanne')
                        ->setLastName('Paulette')
                        ->setCountry('GB')
                        ->setCity('Lyon')
                        ->setAddress('24 rue de la gare')
                        ->setAddressSecondLine('1er étage')
                        ->setCompany('Wizaplace')
                        ->setPhone('0123456798')
                        ->setZipCode('69009')
                )
        );

        $user = $userService->getProfileFromId($userId);

        $this->assertEquals(UserTitle::MR(), $user->getShippingAddress()->getTitle());
        $this->assertSame('Pierre', $user->getShippingAddress()->getFirstName());
        $this->assertSame('Jacques', $user->getShippingAddress()->getLastName());
        $this->assertSame('FR', $user->getShippingAddress()->getCountry());
        $this->assertSame('Lyon', $user->getShippingAddress()->getCity());
        $this->assertSame('24 rue de la gare', $user->getShippingAddress()->getAddress());
        $this->assertSame('1er étage', $user->getShippingAddress()->getAddressSecondLine());
        $this->assertSame('Wizaplace', $user->getShippingAddress()->getCompany());
        $this->assertSame('0123456798', $user->getShippingAddress()->getPhone());
        $this->assertSame('69009', $user->getShippingAddress()->getZipCode());

        $this->assertEquals(UserTitle::MRS(), $user->getBillingAddress()->getTitle());
        $this->assertSame('Jeanne', $user->getBillingAddress()->getFirstName());
        $this->assertSame('Paulette', $user->getBillingAddress()->getLastName());
        $this->assertSame('GB', $user->getBillingAddress()->getCountry());
        $this->assertSame('Lyon', $user->getBillingAddress()->getCity());
        $this->assertSame('24 rue de la gare', $user->getBillingAddress()->getAddress());
        $this->assertSame('1er étage', $user->getBillingAddress()->getAddressSecondLine());
        $this->assertSame('Wizaplace', $user->getBillingAddress()->getCompany());
        $this->assertSame('0123456798', $user->getBillingAddress()->getPhone());
        $this->assertSame('69009', $user->getBillingAddress()->getZipCode());
    }

    public function testUpdateUserAddressesWithMissingFields()
    {
        $client = $this->buildApiClient();
        $userService = new UserService($client);

        // create new user
        $userId = $userService->register('user13@example.com', 'password', 'Jean', 'Paul');

        $client->authenticate('user13@example.com', 'password');
        $user = $userService->getProfileFromId($userId);
        $this->assertSame('', $user->getShippingAddress()->getTitle());
        $this->assertSame('Jean', $user->getShippingAddress()->getFirstName());
        $this->assertSame('Paul', $user->getShippingAddress()->getLastName());

        $this->assertSame('', $user->getBillingAddress()->getTitle());
        $this->assertSame('Jean', $user->getBillingAddress()->getFirstName());
        $this->assertSame('Paul', $user->getBillingAddress()->getLastName());


        $userService->updateUserAdresses(
            (new UpdateUserAddressesCommand())
                ->setUserId($userId)
                ->setShippingAddress(
                    (new UpdateUserAddressCommand())
                        ->setTitle(UserTitle::MR())
                )
                ->setBillingAddress(
                    (new UpdateUserAddressCommand())
                        ->setFirstName('Jeanne')
                        ->setLastName('Paulette')
                )
        );

        $user = $userService->getProfileFromId($userId);

        $this->assertEquals(UserTitle::MR(), $user->getShippingAddress()->getTitle());
        $this->assertSame('Jean', $user->getShippingAddress()->getFirstName());
        $this->assertSame('Paul', $user->getShippingAddress()->getLastName());
        $this->assertSame('FR', $user->getShippingAddress()->getCountry());
        $this->assertSame('', $user->getShippingAddress()->getCity());
        $this->assertSame('', $user->getShippingAddress()->getAddress());
        $this->assertSame('', $user->getShippingAddress()->getAddressSecondLine());
        $this->assertSame('', $user->getShippingAddress()->getCompany());
        $this->assertSame('', $user->getShippingAddress()->getPhone());
        $this->assertSame('', $user->getShippingAddress()->getZipCode());

        $this->assertEquals('', $user->getBillingAddress()->getTitle());
        $this->assertSame('Jeanne', $user->getBillingAddress()->getFirstName());
        $this->assertSame('Paulette', $user->getBillingAddress()->getLastName());
        $this->assertSame('FR', $user->getBillingAddress()->getCountry());
        $this->assertSame('', $user->getBillingAddress()->getCity());
        $this->assertSame('', $user->getBillingAddress()->getAddress());
        $this->assertSame('', $user->getBillingAddress()->getAddressSecondLine());
        $this->assertSame('', $user->getBillingAddress()->getCompany());
        $this->assertSame('', $user->getBillingAddress()->getPhone());
        $this->assertSame('', $user->getBillingAddress()->getZipCode());
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
        $this->assertSame(null, $user->getBirthday());

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
        $this->assertNull($user->getBirthday());
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
