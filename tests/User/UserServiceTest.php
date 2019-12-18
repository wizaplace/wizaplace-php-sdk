<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\SDK\Tests\User;

use GuzzleHttp\Psr7\Uri;
use Wizaplace\SDK\Authentication\ApiKey;
use Wizaplace\SDK\Authentication\AuthenticationRequired;
use Wizaplace\SDK\Authentication\BadCredentials;
use Wizaplace\SDK\Tests\ApiTestCase;
use Wizaplace\SDK\User\RegisterUserCommand;
use Wizaplace\SDK\User\UpdateUserAddressCommand;
use Wizaplace\SDK\User\UpdateUserAddressesCommand;
use Wizaplace\SDK\User\UpdateUserCommand;
use Wizaplace\SDK\User\UserAddress;
use Wizaplace\SDK\User\UserAlreadyExists;
use Wizaplace\SDK\User\UserService;
use Wizaplace\SDK\User\UserTitle;
use Wizaplace\SDK\User\UserType;

/**
 * @see UserService
 */
final class UserServiceTest extends ApiTestCase
{
    public function testCreateUser(): void
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

        static::assertNotNull($user, 'User exists');
        static::assertSame($userEmail, $user->getEmail());
        static::assertSame($userId, $user->getId());
        static::assertNull($user->getTitle());
        static::assertSame('', $user->getFirstname());
        static::assertSame('', $user->getLastname());
        static::assertNull($user->getBirthday());
        static::assertNull($user->getCurrencyCode());
        static::assertNull($user->getPhone());
        static::assertNull($user->getCompanyId());
        static::assertFalse($user->isVendor());
        static::assertSame(UserType::CLIENT()->getValue(), $user->getType()->getValue());

        // shipping address
        static::assertNull($user->getShippingAddress()->getTitle());
        static::assertSame('', $user->getShippingAddress()->getFirstName());
        static::assertSame('', $user->getShippingAddress()->getLastName());
        static::assertSame('', $user->getShippingAddress()->getCompany());
        static::assertSame('', $user->getShippingAddress()->getPhone());
        static::assertSame('', $user->getShippingAddress()->getAddress());
        static::assertSame('', $user->getShippingAddress()->getAddressSecondLine());
        static::assertSame('', $user->getShippingAddress()->getZipCode());
        static::assertSame('', $user->getShippingAddress()->getCity());
        static::assertSame('FR', $user->getShippingAddress()->getCountry());

        // billing address
        static::assertNull($user->getBillingAddress()->getTitle());
        static::assertSame('', $user->getBillingAddress()->getFirstName());
        static::assertSame('', $user->getBillingAddress()->getLastName());
        static::assertSame('', $user->getBillingAddress()->getCompany());
        static::assertSame('', $user->getBillingAddress()->getPhone());
        static::assertSame('', $user->getBillingAddress()->getAddress());
        static::assertSame('', $user->getBillingAddress()->getAddressSecondLine());
        static::assertSame('', $user->getBillingAddress()->getZipCode());
        static::assertSame('', $user->getBillingAddress()->getCity());
        static::assertSame('FR', $user->getBillingAddress()->getCountry());
    }

    public function testCreateUserWithAddresses(): void
    {
        $userEmail = 'user@example.com';
        $userPassword = 'password';
        $userFistname = 'John';
        $userLastname = 'Doe';
        $userBilling = new UserAddress([
            'title'     => UserTitle::MR()->getValue(),
            'firstname' => $userFistname,
            'lastname'  => $userLastname,
            'company'   => "Company_b",
            'phone'     => "Phone_b",
            'address'   => "Address_b",
            'address_2' => "Address 2_b",
            'zipcode'   => "Zipcode_b",
            'city'      => "City_b",
            'country'   => "FR",
        ]);
        $userShipping = new UserAddress([
            'title'     => UserTitle::MR()->getValue(),
            'firstname' => $userFistname,
            'lastname'  => $userLastname,
            'company'   => "Company_s",
            'phone'     => "Phone_s",
            'address'   => "Address_s",
            'address_2' => "Address 2_s",
            'zipcode'   => "Zipcode_s",
            'city'      => "City_s",
            'country'   => "FR",
            'division_code' => "FR-69",
        ]);

        $client = $this->buildApiClient();
        $userService = new UserService($client);

        // create new user
        $userId = $userService->register($userEmail, $userPassword, $userFistname, $userLastname, $userBilling, $userShipping);

        // authenticate with newly created user
        $client->authenticate($userEmail, $userPassword);

        // fetch user
        $user = $userService->getProfileFromId($userId);

        static::assertNotNull($user, 'User exists');
        static::assertSame($userEmail, $user->getEmail());
        static::assertSame($userId, $user->getId());
        static::assertNull($user->getTitle());
        static::assertSame($userFistname, $user->getFirstname());
        static::assertSame($userLastname, $user->getLastname());
        static::assertNull($user->getBirthday());
        static::assertNull($user->getPhone());
        static::assertNull($user->getCompanyId());
        static::assertFalse($user->isVendor());
        static::assertSame(UserType::CLIENT()->getValue(), $user->getType()->getValue());

        // shipping address
        static::assertSame($userShipping->getTitle()->getValue(), $user->getShippingAddress()->getTitle()->getValue());
        static::assertSame($userShipping->getFirstName(), $user->getShippingAddress()->getFirstName());
        static::assertSame($userShipping->getLastName(), $user->getShippingAddress()->getLastName());
        static::assertSame($userShipping->getCompany(), $user->getShippingAddress()->getCompany());
        static::assertSame($userShipping->getPhone(), $user->getShippingAddress()->getPhone());
        static::assertSame($userShipping->getAddress(), $user->getShippingAddress()->getAddress());
        static::assertSame($userShipping->getAddressSecondLine(), $user->getShippingAddress()->getAddressSecondLine());
        static::assertSame($userShipping->getZipCode(), $user->getShippingAddress()->getZipCode());
        static::assertSame($userShipping->getCity(), $user->getShippingAddress()->getCity());
        static::assertSame($userShipping->getCountry(), $user->getShippingAddress()->getCountry());
        static::assertSame($userShipping->getDivisionCode(), $user->getShippingAddress()->getDivisionCode());

        // billing address
        static::assertSame($userBilling->getTitle()->getValue(), $user->getBillingAddress()->getTitle()->getValue());
        static::assertSame($userBilling->getFirstName(), $user->getBillingAddress()->getFirstName());
        static::assertSame($userBilling->getLastName(), $user->getBillingAddress()->getLastName());
        static::assertSame($userBilling->getCompany(), $user->getBillingAddress()->getCompany());
        static::assertSame($userBilling->getPhone(), $user->getBillingAddress()->getPhone());
        static::assertSame($userBilling->getAddress(), $user->getBillingAddress()->getAddress());
        static::assertSame($userBilling->getAddressSecondLine(), $user->getBillingAddress()->getAddressSecondLine());
        static::assertSame($userBilling->getZipCode(), $user->getBillingAddress()->getZipCode());
        static::assertSame($userBilling->getCity(), $user->getBillingAddress()->getCity());
        static::assertSame($userBilling->getCountry(), $user->getBillingAddress()->getCountry());
    }

    public function testCreateUserWithAnAddress(): void
    {
        $userEmail = 'user@example.com';
        $userPassword = 'password';
        $userFistname = 'John';
        $userLastname = 'Doe';
        $userBilling = new UserAddress([
            'title'     => UserTitle::MR()->getValue(),
            'firstname' => $userFistname,
            'lastname'  => $userLastname,
            'company'   => "Company_b",
            'phone'     => "Phone_b",
            'address'   => "Address_b",
            'address_2' => "Address 2_b",
            'zipcode'   => "Zipcode_b",
            'city'      => "City_b",
            'country'   => "FR",
        ]);
        $userShipping = new UserAddress([
            'title'     => UserTitle::MR()->getValue(),
            'firstname' => $userFistname,
            'lastname'  => $userLastname,
            'company'   => "Company_s",
            'phone'     => "Phone_s",
            'address'   => "Address_s",
            'address_2' => "Address 2_s",
            'zipcode'   => "Zipcode_s",
            'city'      => "City_s",
            'country'   => "FR",
        ]);

        $client = $this->buildApiClient();
        $userService = new UserService($client);

        // create new user
        $this->expectException(\Exception::class);
        $userService->register($userEmail, $userPassword, $userFistname, $userLastname, $userBilling);

        // create new user
        $this->expectException(\Exception::class);
        $userService->register($userEmail, $userPassword, $userFistname, $userLastname, null, $userShipping);
    }

    public function testCreateUserOnlyWithCompanyName(): void
    {
        $userEmail = 'user123@example.com';
        $userPassword = 'password';
        $userFistname = 'Paul';
        $userLastname = 'Jean';
        $userBilling = new UserAddress([
            'title'     => UserTitle::MR()->getValue(),
            'firstname' => $userFistname,
            'lastname'  => $userLastname,
            'company'   => "Company_b",
            'phone'     => "",
            'address'   => "",
            'address_2' => "",
            'zipcode'   => "",
            'city'      => "",
            'country'   => "",
        ]);
        $userShipping = new UserAddress([
            'title'     => UserTitle::MR()->getValue(),
            'firstname' => $userFistname,
            'lastname'  => $userLastname,
            'company'   => "Company_s",
            'phone'     => "",
            'address'   => "",
            'address_2' => "",
            'zipcode'   => "",
            'city'      => "",
            'country'   => "",
        ]);

        $client = $this->buildApiClient();
        $userService = new UserService($client);

        // create new user
        $userId = $userService->register($userEmail, $userPassword, $userFistname, $userLastname, $userBilling, $userShipping);

        // authenticate with newly created user
        $client->authenticate($userEmail, $userPassword);

        // fetch user
        $user = $userService->getProfileFromId($userId);

        static::assertNotNull($user, 'User exists');
        static::assertSame($userEmail, $user->getEmail());
        static::assertSame($userId, $user->getId());
        static::assertNull($user->getTitle());
        static::assertSame($userFistname, $user->getFirstname());
        static::assertSame($userLastname, $user->getLastname());
        static::assertNull($user->getPhone());
        static::assertNull($user->getBirthday());
        static::assertNull($user->getCompanyId());
        static::assertFalse($user->isVendor());
        static::assertSame(UserType::CLIENT()->getValue(), $user->getType()->getValue());

        //shipping adress
        static::assertSame($userShipping->getTitle()->getValue(), $user->getShippingAddress()->getTitle()->getValue());
        static::assertSame($userShipping->getFirstName(), $user->getShippingAddress()->getFirstName());
        static::assertSame($userShipping->getLastName(), $user->getShippingAddress()->getLastName());
        static::assertSame($userShipping->getCompany(), $user->getShippingAddress()->getCompany());
        static::assertSame($userShipping->getPhone(), $user->getShippingAddress()->getPhone());
        static::assertSame($userShipping->getAddress(), $user->getShippingAddress()->getAddress());
        static::assertSame($userShipping->getAddressSecondLine(), $user->getShippingAddress()->getAddressSecondLine());
        static::assertSame($userShipping->getZipCode(), $user->getShippingAddress()->getZipCode());
        static::assertSame($userShipping->getCity(), $user->getShippingAddress()->getCity());
        static::assertSame('FR', $user->getShippingAddress()->getCountry());



        //billing adress
        static::assertSame($userBilling->getTitle()->getValue(), $user->getBillingAddress()->getTitle()->getValue());
        static::assertSame($userBilling->getFirstName(), $user->getBillingAddress()->getFirstName());
        static::assertSame($userBilling->getLastName(), $user->getBillingAddress()->getLastName());
        static::assertSame($userBilling->getCompany(), $user->getBillingAddress()->getCompany());
        static::assertSame($userBilling->getPhone(), $user->getBillingAddress()->getPhone());
        static::assertSame($userBilling->getAddress(), $user->getBillingAddress()->getAddress());
        static::assertSame($userBilling->getAddressSecondLine(), $user->getBillingAddress()->getAddressSecondLine());
        static::assertSame($userBilling->getZipCode(), $user->getBillingAddress()->getZipCode());
        static::assertSame($userBilling->getCity(), $user->getBillingAddress()->getCity());
        static::assertSame('FR', $user->getBillingAddress()->getCountry());
    }

    public function testCreateUserWithFullInfos(): void
    {
        $addressCommand = (new UpdateUserAddressCommand())
            ->setTitle(UserTitle::MRS())
            ->setFirstName('Jane')
            ->setLastName('Doe')
            ->setPhone('0123456789')
            ->setAddress('24 rue de la gare')
            ->setCompany('Wizaplace')
            ->setZipCode('69009')
            ->setCity('Lyon')
            ->setCountry('France');

        $userCommand = (new RegisterUserCommand())
            ->setEmail('user@example.com')
            ->setPassword('password')
            ->setFirstName('Jane')
            ->setLastName('Doe')
            ->setPhone('0102030405')
            ->setBirthday(\DateTime::createFromFormat('Y-m-d', '1998-07-12'))
            ->setTitle(UserTitle::MRS())
            ->setBilling($addressCommand)
            ->setShipping($addressCommand);

        $client = $this->buildApiClient();
        $userService = new UserService($client);

        // create new user
        $userId = $userService->registerWithFullInfos($userCommand);

        // authenticate with newly created user
        $client->authenticate($userCommand->getEmail(), $userCommand->getPassword());

        // fetch user
        $user = $userService->getProfileFromId($userId);

        static::assertNotNull($user, 'User exists');
        static::assertSame($userCommand->getEmail(), $user->getEmail());
        static::assertSame($userId, $user->getId());
        static::assertTrue($userCommand->getTitle()->equals($user->getTitle()));
        static::assertSame($userCommand->getFirstName(), $user->getFirstname());
        static::assertSame($userCommand->getLastName(), $user->getLastname());
        static::assertSame('1998-07-12', $user->getBirthday()->format('Y-m-d'));
        static::assertSame('0102030405', $user->getPhone());
        static::assertNull($user->getCompanyId());
        static::assertFalse($user->isVendor());
        static::assertSame(UserType::CLIENT()->getValue(), $user->getType()->getValue());

        // shipping address
        static::assertTrue($addressCommand->getTitle()->equals($user->getShippingAddress()->getTitle()));
        static::assertSame($addressCommand->getFirstName(), $user->getShippingAddress()->getFirstName());
        static::assertSame($addressCommand->getLastName(), $user->getShippingAddress()->getLastName());
        static::assertSame($addressCommand->getCompany(), $user->getShippingAddress()->getCompany());
        static::assertSame($addressCommand->getPhone(), $user->getShippingAddress()->getPhone());
        static::assertSame($addressCommand->getAddress(), $user->getShippingAddress()->getAddress());
        static::assertSame('', $user->getShippingAddress()->getAddressSecondLine());
        static::assertSame($addressCommand->getZipCode(), $user->getShippingAddress()->getZipCode());
        static::assertSame($addressCommand->getCity(), $user->getShippingAddress()->getCity());
        static::assertSame('Fr', $user->getShippingAddress()->getCountry());

        // billing address
        static::assertTrue($addressCommand->getTitle()->equals($user->getBillingAddress()->getTitle()));
        static::assertSame($addressCommand->getFirstName(), $user->getBillingAddress()->getFirstName());
        static::assertSame($addressCommand->getLastName(), $user->getBillingAddress()->getLastName());
        static::assertSame($addressCommand->getCompany(), $user->getBillingAddress()->getCompany());
        static::assertSame($addressCommand->getPhone(), $user->getBillingAddress()->getPhone());
        static::assertSame($addressCommand->getAddress(), $user->getBillingAddress()->getAddress());
        static::assertSame('', $user->getBillingAddress()->getAddressSecondLine());
        static::assertSame($addressCommand->getZipCode(), $user->getBillingAddress()->getZipCode());
        static::assertSame($addressCommand->getCity(), $user->getBillingAddress()->getCity());
        static::assertSame('Fr', $user->getBillingAddress()->getCountry());
    }

    public function testCreateAlreadyExistingUser(): void
    {
        $client = $this->buildApiClient();
        $userService = new UserService($client);

        // create already existing user
        $this->expectException(UserAlreadyExists::class);
        $userService->register('user@wizaplace.com', 'whatever');
    }

    public function testUpdateUser(): void
    {
        $client = $this->buildApiClient();
        $userService = new UserService($client);

        // create new user
        $userId = $userService->register('user42@example.com', 'password', 'Jean', 'Paul');

        $client->authenticate('user42@example.com', 'password');
        $user = $userService->getProfileFromId($userId);
        static::assertSame('user42@example.com', $user->getEmail());
        static::assertNull($user->getTitle());
        static::assertSame('Jean', $user->getFirstname());
        static::assertSame('Paul', $user->getLastname());
        static::assertNull($user->getBirthday());
        static::assertNull($user->getCompanyId());
        static::assertFalse($user->isVendor());
        static::assertSame(UserType::CLIENT()->getValue(), $user->getType()->getValue());
        static::assertNull($user->getCurrencyCode());

        $userService->updateUser(
            (new UpdateUserCommand())
                ->setUserId($userId)
                ->setEmail('user43@example.com')
                ->setFirstName('Jacques')
                ->setLastName('Jules')
                ->setTitle(UserTitle::MR())
                ->setPhone('0102030405')
                ->setBirthday(\DateTime::createFromFormat('Y-m-d', '1963-02-17'))
                ->setCurrencyCode('EUR')
        );

        $client->authenticate('user43@example.com', 'password');

        $user = $userService->getProfileFromId($userId);
        static::assertSame('user43@example.com', $user->getEmail());
        static::assertTrue(UserTitle::MR()->equals($user->getTitle()));
        static::assertSame('Jacques', $user->getFirstname());
        static::assertSame('Jules', $user->getLastname());
        static::assertSame('1963-02-17', $user->getBirthday()->format('Y-m-d'));
        static::assertSame('0102030405', $user->getPhone());
        static::assertNull($user->getCompanyId());
        static::assertFalse($user->isVendor());
        static::assertSame(UserType::CLIENT()->getValue(), $user->getType()->getValue());
        static::assertSame('EUR', $user->getCurrencyCode());
    }

    public function testPatchUser(): void
    {
        $client = $this->buildApiClient();
        $userService = new UserService($client);

        $userId = $userService->register('user65@example.com', 'password', 'Jean', 'Paul');
        $client->authenticate('user65@example.com', 'password');

        $userService->patchUser(
            (new UpdateUserCommand())
                ->setUserId($userId)
                ->setCurrencyCode('EUR')
                ->setPhone('0102030405')
        );

        $client->authenticate('user65@example.com', 'password');
        $user = $userService->getProfileFromId($userId);
        static::assertSame('EUR', $user->getCurrencyCode());
        static::assertSame('0102030405', $user->getPhone());
    }

    public function testUpdateUserAddresses(): void
    {
        $client = $this->buildApiClient();
        $userService = new UserService($client);

        // create new user
        $userId = $userService->register('user12@example.com', 'password', 'Jean', 'Paul');

        $client->authenticate('user12@example.com', 'password');
        $user = $userService->getProfileFromId($userId);
        static::assertNull($user->getShippingAddress()->getTitle());
        static::assertSame('Jean', $user->getShippingAddress()->getFirstName());
        static::assertSame('Paul', $user->getShippingAddress()->getLastName());

        static::assertNull($user->getBillingAddress()->getTitle());
        static::assertSame('Jean', $user->getBillingAddress()->getFirstName());
        static::assertSame('Paul', $user->getBillingAddress()->getLastName());


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
                        ->setDivisionCode('FR-69')
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

        static::assertTrue(UserTitle::MR()->equals($user->getShippingAddress()->getTitle()));
        static::assertSame('Pierre', $user->getShippingAddress()->getFirstName());
        static::assertSame('Jacques', $user->getShippingAddress()->getLastName());
        static::assertSame('FR', $user->getShippingAddress()->getCountry());
        static::assertSame('Lyon', $user->getShippingAddress()->getCity());
        static::assertSame('24 rue de la gare', $user->getShippingAddress()->getAddress());
        static::assertSame('1er étage', $user->getShippingAddress()->getAddressSecondLine());
        static::assertSame('Wizaplace', $user->getShippingAddress()->getCompany());
        static::assertSame('0123456798', $user->getShippingAddress()->getPhone());
        static::assertSame('69009', $user->getShippingAddress()->getZipCode());
        static::assertSame('FR-69', $user->getShippingAddress()->getDivisionCode());

        static::assertTrue(UserTitle::MRS()->equals($user->getBillingAddress()->getTitle()));
        static::assertSame('Jeanne', $user->getBillingAddress()->getFirstName());
        static::assertSame('Paulette', $user->getBillingAddress()->getLastName());
        static::assertSame('GB', $user->getBillingAddress()->getCountry());
        static::assertSame('Lyon', $user->getBillingAddress()->getCity());
        static::assertSame('24 rue de la gare', $user->getBillingAddress()->getAddress());
        static::assertSame('1er étage', $user->getBillingAddress()->getAddressSecondLine());
        static::assertSame('Wizaplace', $user->getBillingAddress()->getCompany());
        static::assertSame('0123456798', $user->getBillingAddress()->getPhone());
        static::assertSame('69009', $user->getBillingAddress()->getZipCode());
    }

    public function testUpdateUserAddressesWithMissingFields(): void
    {
        $client = $this->buildApiClient();
        $userService = new UserService($client);

        // create new user
        $userId = $userService->register('user13@example.com', 'password', 'Jean', 'Paul');

        $client->authenticate('user13@example.com', 'password');
        $user = $userService->getProfileFromId($userId);
        static::assertNull($user->getShippingAddress()->getTitle());
        static::assertSame('Jean', $user->getShippingAddress()->getFirstName());
        static::assertSame('Paul', $user->getShippingAddress()->getLastName());

        static::assertNull($user->getBillingAddress()->getTitle());
        static::assertSame('Jean', $user->getBillingAddress()->getFirstName());
        static::assertSame('Paul', $user->getBillingAddress()->getLastName());


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

        static::assertTrue(UserTitle::MR()->equals($user->getShippingAddress()->getTitle()));
        static::assertSame('Jean', $user->getShippingAddress()->getFirstName());
        static::assertSame('Paul', $user->getShippingAddress()->getLastName());
        static::assertSame('FR', $user->getShippingAddress()->getCountry());
        static::assertSame('', $user->getShippingAddress()->getCity());
        static::assertSame('', $user->getShippingAddress()->getAddress());
        static::assertSame('', $user->getShippingAddress()->getAddressSecondLine());
        static::assertSame('', $user->getShippingAddress()->getCompany());
        static::assertSame('', $user->getShippingAddress()->getPhone());
        static::assertSame('', $user->getShippingAddress()->getZipCode());
        static::assertSame('', $user->getShippingAddress()->getDivisionCode());

        static::assertNull($user->getBillingAddress()->getTitle());
        static::assertSame('Jeanne', $user->getBillingAddress()->getFirstName());
        static::assertSame('Paulette', $user->getBillingAddress()->getLastName());
        static::assertSame('FR', $user->getBillingAddress()->getCountry());
        static::assertSame('', $user->getBillingAddress()->getCity());
        static::assertSame('', $user->getBillingAddress()->getAddress());
        static::assertSame('', $user->getBillingAddress()->getAddressSecondLine());
        static::assertSame('', $user->getBillingAddress()->getCompany());
        static::assertSame('', $user->getBillingAddress()->getPhone());
        static::assertSame('', $user->getBillingAddress()->getZipCode());
    }

    public function testUpdateUserAddressesWithMissingFieldsWhenFullFieldsBefore(): void
    {
        $client = $this->buildApiClient();
        $userService = new UserService($client);

        // create new user
        $userId = $userService->register('user123@example.com', 'password', 'Paul', 'Jacques');

        $client->authenticate('user123@example.com', 'password');

        //test juste après création du compte
        $user = $userService->getProfileFromId($userId);
        static::assertNull($user->getShippingAddress()->getTitle());
        static::assertSame('Paul', $user->getShippingAddress()->getFirstName());
        static::assertSame('Jacques', $user->getShippingAddress()->getLastName());

        static::assertNull($user->getBillingAddress()->getTitle());
        static::assertSame('Paul', $user->getBillingAddress()->getFirstName());
        static::assertSame('Jacques', $user->getBillingAddress()->getLastName());

        //ajout d'adresses et de nom d'entreprise
        $userService->updateUserAdresses(
            (new UpdateUserAddressesCommand())
                ->setUserId($userId)
                ->setShippingAddress(
                    (new UpdateUserAddressCommand())
                        ->setTitle(UserTitle::MR())
                        ->setAddress('49 rue des chemins')
                        ->setCompany('Universite de Cambridge')
                        ->setAddressSecondLine('9e étage')
                        ->setZipCode('69009')
                        ->setDivisionCode('FR-69')
                )
                ->setBillingAddress(
                    (new UpdateUserAddressCommand())
                        ->setFirstName('Paul')
                        ->setLastName('Jacques')
                        ->setAddress('49 rue des chemins')
                        ->setCompany('Universite de Cambridge')
                        ->setAddressSecondLine('9e étage')
                        ->setZipCode('69009')
                )
        );

        $user = $userService->getProfileFromId($userId);

        static::assertTrue(UserTitle::MR()->equals($user->getShippingAddress()->getTitle()));

        static::assertSame('Paul', $user->getShippingAddress()->getFirstName());
        static::assertSame('Jacques', $user->getShippingAddress()->getLastName());
        static::assertSame('Universite de Cambridge', $user->getShippingAddress()->getCompany());
        static::assertSame('49 rue des chemins', $user->getShippingAddress()->getAddress());
        static::assertSame('9e étage', $user->getShippingAddress()->getAddressSecondLine());
        static::assertSame('69009', $user->getShippingAddress()->getZipCode());
        static::assertSame('FR-69', $user->getShippingAddress()->getDivisionCode());


        static::assertNull($user->getBillingAddress()->getTitle());

        static::assertSame('Paul', $user->getBillingAddress()->getFirstName());
        static::assertSame('Jacques', $user->getBillingAddress()->getLastName());
        static::assertSame('Universite de Cambridge', $user->getShippingAddress()->getCompany());
        static::assertSame('49 rue des chemins', $user->getBillingAddress()->getAddress());
        static::assertSame('9e étage', $user->getBillingAddress()->getAddressSecondLine());
        static::assertSame('69009', $user->getBillingAddress()->getZipCode());

        $userService->updateUserAdresses(
            (new UpdateUserAddressesCommand())
                ->setUserId($userId)
                ->setShippingAddress(
                    (new UpdateUserAddressCommand())
                        ->setAddress('')
                        ->setAddressSecondLine('')
                        ->setCompany('')
                        ->setZipCode('')
                )
                ->setBillingAddress(
                    (new UpdateUserAddressCommand())
                        ->setAddress('')
                        ->setAddressSecondLine('')
                        ->setCompany('')
                        ->setZipCode('')
                )
        );

        $user = $userService->getProfileFromId($userId);

        static::assertTrue(UserTitle::MR()->equals($user->getShippingAddress()->getTitle()));
        static::assertSame('Paul', $user->getShippingAddress()->getFirstName());
        static::assertSame('Jacques', $user->getShippingAddress()->getLastName());
        static::assertSame('', $user->getShippingAddress()->getAddress());
        static::assertSame('', $user->getShippingAddress()->getAddressSecondLine());
        static::assertSame('', $user->getShippingAddress()->getCompany());
        static::assertSame('', $user->getShippingAddress()->getZipCode());

        static::assertNull($user->getBillingAddress()->getTitle());
        static::assertSame('Paul', $user->getBillingAddress()->getFirstName());
        static::assertSame('Jacques', $user->getBillingAddress()->getLastName());
        static::assertSame('', $user->getBillingAddress()->getAddress());
        static::assertSame('', $user->getBillingAddress()->getAddressSecondLine());
        static::assertSame('', $user->getBillingAddress()->getCompany());
        static::assertSame('', $user->getBillingAddress()->getZipCode());
    }

    public function testUpdateUserWithDefaultValuesOnly()
    {
        $client = $this->buildApiClient();
        $userService = new UserService($client);

        // create new user
        $userId = $userService->register('user42@example.com', 'password', 'Jean', 'Paul');

        $client->authenticate('user42@example.com', 'password');
        $user = $userService->getProfileFromId($userId);
        static::assertSame('user42@example.com', $user->getEmail());
        static::assertNull($user->getTitle());
        static::assertSame('Jean', $user->getFirstname());
        static::assertSame('Paul', $user->getLastname());
        static::assertNull($user->getBirthday());
        static::assertSame(UserType::CLIENT()->getValue(), $user->getType()->getValue());

        $userService->updateUser(
            (new UpdateUserCommand())
                ->setUserId($userId)
                ->setEmail('user43@example.com')
                ->setFirstName('Jacques')
                ->setLastName('Jules')
        );

        $client->authenticate('user43@example.com', 'password');

        $user = $userService->getProfileFromId($userId);
        static::assertSame('user43@example.com', $user->getEmail());
        static::assertNull($user->getTitle());
        static::assertSame('Jacques', $user->getFirstname());
        static::assertSame('Jules', $user->getLastname());
        static::assertNull($user->getBirthday());
        static::assertSame(UserType::CLIENT()->getValue(), $user->getType()->getValue());
    }

    public function testRecoverPassword(): void
    {
        $client = $this->buildApiClient();
        $userService = new UserService($client);

        $userEmail = 'user1237@example.com';
        $userPassword = 'password';
        $userService->register($userEmail, $userPassword);


        static::assertNull($userService->recoverPassword($userEmail));
    }

    public function testRecoverPasswordWithCustomUrl(): void
    {
        $client = $this->buildApiClient();
        $userService = new UserService($client);

        $userEmail = 'user1237@example.com';
        $userPassword = 'password';
        $userService->register($userEmail, $userPassword);

        static::assertNull($userService->recoverPassword($userEmail, new Uri('https://marketplace.example.com/recover-password=token')));
    }

    public function testRecoverPasswordForNonExistingEmail(): void
    {
        $client = $this->buildApiClient();
        $userService = new UserService($client);

        static::assertNull($userService->recoverPassword('404@example.com'));
    }

    public function testChangePassword(): void
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

        static::assertNotEmpty($client->authenticate($userEmail, 'hunter2'));
        $this->expectException(BadCredentials::class);
        static::assertNotEmpty($client->authenticate($userEmail, $userPassword));
    }

    public function testChangePasswordAnonymously(): void
    {
        $this->expectException(AuthenticationRequired::class);
        (new UserService($this->buildApiClient()))->changePassword(1, 'hunter2');
    }

    public function testChangePasswordWithToken(): void
    {
        $client = $this->buildApiClient();
        $userService = new UserService($client);

        $cannotLogin = false;
        try {
            $client->authenticate('customer-4@world-company.com', 'newPassword');
        } catch (BadCredentials $e) {
            $cannotLogin = true;
        }
        static::assertTrue($cannotLogin);

        $userService->changePasswordWithRecoveryToken(md5('fake_secret_token'), 'newPassword');

        $apiKey = $client->authenticate('customer-4@world-company.com', 'newPassword');
        static::assertInstanceOf(ApiKey::class, $apiKey);
    }

    public function testGetUserCompany(): void
    {
        $apiClient = $this->buildApiClient();

        $userId = ($apiClient->authenticate('vendor@world-company.com', 'password-vendor'))->getId();

        $user = (new UserService($apiClient))->getProfileFromId($userId);
        $companyId = $user->getCompanyId();

        static::assertInternalType('int', $companyId);
        static::assertGreaterThan(0, $companyId);
        static::assertTrue($user->isVendor());
    }

    public function testUpdateUserWithJsonFormat(): void
    {
        $addressCommand = new UpdateUserAddressCommand();
        $addressCommand
            ->setTitle(UserTitle::MR())
            ->setFirstName('Paul')
            ->setLastName('Jacques')
            ->setPhone('0123456789')
            ->setAddress('24 rue de la gare')
            ->setCompany('Wizaplace')
            ->setZipCode('69009')
            ->setCity('Lyon')
            ->setCountry('France');

        $userCommand = new RegisterUserCommand();
        $userCommand->setEmail('user974@example.com');
        $userCommand->setPassword('password');
        $userCommand->setFirstName('Paul');
        $userCommand->setLastName('Jacques');
        $userCommand->setBirthday(\DateTime::createFromFormat('Y-m-d', '1997-07-12'));
        $userCommand->setTitle(UserTitle::MR());
        $userCommand->setBilling($addressCommand);
        $userCommand->setShipping($addressCommand);

        $client = $this->buildApiClient();
        $userService = new UserService($client);
        // create new user
        $userId = $userService->registerWithFullInfos($userCommand);

        $client->authenticate('user974@example.com', 'password');

        $userService->updateUser(
            (new UpdateUserCommand())
                ->setUserId($userId)
                ->setEmail('user479@example.com')
                ->setFirstName('Paul')
                ->setLastName('Emploi')
                ->setBirthday(null)
        );
        $client->authenticate('user479@example.com', 'password');

        $user = $userService->getProfileFromId($userId);
        static::assertSame('user479@example.com', $user->getEmail());
        static::assertSame(UserTitle::MR()->getValue(), $user->getTitle()->getValue());
        static::assertSame('Paul', $user->getFirstname());
        static::assertSame('Emploi', $user->getLastname());
        static::assertNull($user->getBirthday());
        static::assertSame(UserType::CLIENT()->getValue(), $user->getType()->getValue());
    }

    public function testGetPendingCompanyId(): void
    {
        $apiClient = $this->buildApiClient();
        $userId = ($apiClient->authenticate("test2@test.fr", 'test'))->getId();

        $user = (new UserService($apiClient))->getProfileFromId($userId);

        static::assertSame(8, $user->getPendingCompanyId());
        static::assertNull($user->getCompanyId());
    }

    public function testGetExternalIdentifier(): void
    {
        $apiClient = $this->buildApiClient();
        $userId = ($apiClient->authenticate("user@wizaplace.com", 'password'))->getId();

        $user = (new UserService($apiClient))->getProfileFromId($userId);

        static::assertSame("id externe", $user->getExternalIdentifier());
    }

    public function testRegisterUserExternalIdentifier(): void
    {
        $apiClient = $this->buildApiClient();

        $registerUserCommand = (new RegisterUserCommand())
            ->setEmail('external@identifier.com')
            ->setPassword("password")
            ->setTitle(UserTitle::MR())
            ->setBirthday(new \DateTimeImmutable("2019-11-18"))
            ->setShipping(new UpdateUserAddressCommand())
            ->setBilling(new UpdateUserAddressCommand())
            ->setExternalIdentifier("id_externe")
        ;

        $userId = (new UserService($apiClient))->registerWithFullInfos($registerUserCommand);

        $apiClient->authenticate("external@identifier.com", 'password');

        $user = (new UserService($apiClient))->getProfileFromId($userId);

        static::assertSame("id_externe", $user->getExternalIdentifier());
    }

    public function testUpdateUserExternalIdentifier(): void
    {
        $client = $this->buildApiClient();
        $userService = new UserService($client);

        // create new user
        $userId = $userService->register('user4211@example.com', 'password', 'Jean', 'Paul');

        $client->authenticate('user4211@example.com', 'password');
        $user = $userService->getProfileFromId($userId);
        static::assertSame('user4211@example.com', $user->getEmail());

        $userService->updateUser(
            (new UpdateUserCommand())
                ->setUserId($userId)
                ->setEmail('user4211@example.com')
                ->setFirstName('Paul')
                ->setLastName('Emploi')
                ->setBirthday(null)
                ->setExternalIdentifier('externalIdentified0012')
        );

        $client->authenticate('user4211@example.com', 'password');

        $user = $userService->getProfileFromId($userId);
        static::assertSame('user4211@example.com', $user->getEmail());
        static::assertSame('externalIdentified0012', $user->getExternalIdentifier());
    }

    public function testPatchUserExternalIdentifier(): void
    {
        $client = $this->buildApiClient();
        $userService = new UserService($client);

        // create new user
        $userId = $userService->register('user42114@example.com', 'password', 'Jean', 'Paul');

        $client->authenticate('user42114@example.com', 'password');
        $user = $userService->getProfileFromId($userId);
        static::assertSame('user42114@example.com', $user->getEmail());

        $userService->patchUser(
            (new UpdateUserCommand())
                ->setUserId($userId)
                ->setCurrencyCode('EUR')
                ->setPhone('0102030405')
                ->setExternalIdentifier('externalIdentified0012')
        );

        $client->authenticate('user42114@example.com', 'password');

        $user = $userService->getProfileFromId($userId);
        static::assertSame('user42114@example.com', $user->getEmail());
        static::assertSame('externalIdentified0012', $user->getExternalIdentifier());
    }

    public function testRegisterUserWithIsProfessional(): void
    {
        $apiClient = $this->buildApiClient();

        $registerUserCommand = (new RegisterUserCommand())
            ->setEmail('isprofesionnal01@toto.com')
            ->setPassword("password")
            ->setTitle(UserTitle::MR())
            ->setBirthday(new \DateTimeImmutable("2019-11-18"))
            ->setShipping(new UpdateUserAddressCommand())
            ->setBilling(new UpdateUserAddressCommand())
            ->setIsProfessional(true)
        ;

        $userId = (new UserService($apiClient))->registerWithFullInfos($registerUserCommand);
        $apiClient->authenticate("isprofesionnal01@toto.com", 'password');
        $user = (new UserService($apiClient))->getProfileFromId($userId);

        static::assertTrue($user->isProfessional());
    }

    public function testUpdateUserIsProfessional(): void
    {
        $client = $this->buildApiClient();
        $userService = new UserService($client);

        // create new user
        $registerUserCommand = (new RegisterUserCommand())
            ->setEmail('userprofessional11@example.com')
            ->setPassword("password")
            ->setTitle(UserTitle::MR())
            ->setBirthday(new \DateTimeImmutable("2019-11-18"))
            ->setShipping(new UpdateUserAddressCommand())
            ->setBilling(new UpdateUserAddressCommand())
            ->setIsProfessional(true)
        ;

        $userId = $userService->registerWithFullInfos($registerUserCommand);

        $client->authenticate('userprofessional11@example.com', 'password');
        $user = $userService->getProfileFromId($userId);
        static::assertTrue($user->isProfessional());

        // update user
        $userService->updateUser(
            (new UpdateUserCommand())
                ->setUserId($userId)
                ->setEmail('userprofessional11@example.com')
                ->setFirstName('Paul')
                ->setLastName('Professional')
                ->setIsProfessional(false)
        );

        $client->authenticate('userprofessional11@example.com', 'password');
        $user = $userService->getProfileFromId($userId);

        static::assertFalse($user->isProfessional());
    }

    public function testPatchUserIsProfessional(): void
    {
        $client = $this->buildApiClient();
        $userService = new UserService($client);

        // create new user
        $registerUserCommand = (new RegisterUserCommand())
            ->setEmail('userprofessional15@example.com')
            ->setPassword("password")
            ->setTitle(UserTitle::MR())
            ->setBirthday(new \DateTimeImmutable("2019-11-18"))
            ->setShipping(new UpdateUserAddressCommand())
            ->setBilling(new UpdateUserAddressCommand())
            ->setIsProfessional(true)
        ;

        $userId = $userService->registerWithFullInfos($registerUserCommand);

        $client->authenticate('userprofessional15@example.com', 'password');
        $user = $userService->getProfileFromId($userId);
        static::assertTrue($user->isProfessional());

        // update user
        $userService->patchUser(
            (new UpdateUserCommand())
                ->setUserId($userId)
                ->setCurrencyCode('EUR')
                ->setPhone('0102030405')
                ->setIsProfessional(false)
        );

        $client->authenticate('userprofessional15@example.com', 'password');
        $user = $userService->getProfileFromId($userId);
        static::assertFalse($user->isProfessional());
    }

    public function testRegisterUserIntraEuropeanCommunityVAT(): void
    {
        $apiClient = $this->buildApiClient();

        $registerUserCommand = (new RegisterUserCommand())
            ->setEmail('testIntraEuropeanCommunityVAT11@test.com')
            ->setPassword("password")
            ->setTitle(UserTitle::MR())
            ->setBirthday(new \DateTimeImmutable("2019-11-18"))
            ->setShipping(new UpdateUserAddressCommand())
            ->setBilling(new UpdateUserAddressCommand())
            ->setIsProfessional(true)
            ->setIntraEuropeanCommunityVAT("X1234567890")
        ;

        $userId = (new UserService($apiClient))->registerWithFullInfos($registerUserCommand);

        $apiClient->authenticate("testIntraEuropeanCommunityVAT11@test.com", 'password');

        $user = (new UserService($apiClient))->getProfileFromId($userId);

        static::assertSame("X1234567890", $user->getIntraEuropeanCommunityVAT());
    }

    public function testUpdateUserIntraEuropeanCommunityVAT(): void
    {
        $client = $this->buildApiClient();
        $userService = new UserService($client);

        // create new user
        $userId = $userService->register('testIntraEuropeanCommunityVAT01@test.com', 'password', 'Jean', 'Paul');

        $client->authenticate('testIntraEuropeanCommunityVAT01@test.com', 'password');
        $user = $userService->getProfileFromId($userId);
        static::assertSame('testIntraEuropeanCommunityVAT01@test.com', $user->getEmail());

        $userService->updateUser(
            (new UpdateUserCommand())
                ->setUserId($userId)
                ->setEmail('testIntraEuropeanCommunityVAT01@test.com')
                ->setFirstName('Paul')
                ->setLastName('Emploi')
                ->setBirthday(null)
                ->setIsProfessional(true)
                ->setIntraEuropeanCommunityVAT('X1234567890')
        );

        $client->authenticate('testIntraEuropeanCommunityVAT01@test.com', 'password');

        $user = $userService->getProfileFromId($userId);
        static::assertSame('testIntraEuropeanCommunityVAT01@test.com', $user->getEmail());
        static::assertSame('X1234567890', $user->getIntraEuropeanCommunityVAT());
    }

    public function testPatchUserIntraEuropeanCommunityVAT(): void
    {
        $client = $this->buildApiClient();
        $userService = new UserService($client);

        // create new user
        $userId = $userService->register('testIntraEuropeanCommunityVAT04@test.com', 'password', 'Jean', 'Paul');

        $client->authenticate('testIntraEuropeanCommunityVAT04@test.com', 'password');
        $user = $userService->getProfileFromId($userId);
        static::assertSame('testIntraEuropeanCommunityVAT04@test.com', $user->getEmail());

        $userService->patchUser(
            (new UpdateUserCommand())
                ->setUserId($userId)
                ->setIsProfessional(true)
                ->setIntraEuropeanCommunityVAT('X1234567890')
        );

        $client->authenticate('testIntraEuropeanCommunityVAT04@test.com', 'password');

        $user = $userService->getProfileFromId($userId);
        static::assertSame('testIntraEuropeanCommunityVAT04@test.com', $user->getEmail());
        static::assertSame('X1234567890', $user->getIntraEuropeanCommunityVAT());
    }

    public function testRegisterUserCompany(): void
    {
        $apiClient = $this->buildApiClient();

        $registerUserCommand = (new RegisterUserCommand())
            ->setEmail('testCompany11@test.com')
            ->setPassword("password")
            ->setTitle(UserTitle::MR())
            ->setBirthday(new \DateTimeImmutable("2019-11-18"))
            ->setShipping(new UpdateUserAddressCommand())
            ->setBilling(new UpdateUserAddressCommand())
            ->setIsProfessional(true)
            ->setCompany("wizaplace")
        ;

        $userId = (new UserService($apiClient))->registerWithFullInfos($registerUserCommand);

        $apiClient->authenticate("testCompany11@test.com", 'password');

        $user = (new UserService($apiClient))->getProfileFromId($userId);

        static::assertSame("wizaplace", $user->getCompany());
    }

    public function testUpdateUserCompany(): void
    {
        $client = $this->buildApiClient();
        $userService = new UserService($client);

        // create new user
        $userId = $userService->register('testCompany12@test.com', 'password', 'Jean', 'Paul');

        $client->authenticate('testCompany12@test.com', 'password');
        $user = $userService->getProfileFromId($userId);
        static::assertSame('testCompany12@test.com', $user->getEmail());

        $userService->updateUser(
            (new UpdateUserCommand())
                ->setUserId($userId)
                ->setEmail('testCompany12@test.com')
                ->setFirstName('Paul')
                ->setLastName('Emploi')
                ->setBirthday(null)
                ->setIsProfessional(true)
                ->setCompany('wizaplace')
        );

        $client->authenticate('testCompany12@test.com', 'password');

        $user = $userService->getProfileFromId($userId);
        static::assertSame('testCompany12@test.com', $user->getEmail());
        static::assertSame('wizaplace', $user->getCompany());
    }

    public function testPatchUserCompany(): void
    {
        $client = $this->buildApiClient();
        $userService = new UserService($client);

        // create new user
        $userId = $userService->register('testCompany13@test.com', 'password', 'Jean', 'Paul');

        $client->authenticate('testCompany13@test.com', 'password');
        $user = $userService->getProfileFromId($userId);
        static::assertSame('testCompany13@test.com', $user->getEmail());

        $userService->patchUser(
            (new UpdateUserCommand())
                ->setUserId($userId)
                ->setIsProfessional(true)
                ->setCompany('wizaplace')
        );

        $client->authenticate('testCompany13@test.com', 'password');

        $user = $userService->getProfileFromId($userId);
        static::assertSame('testCompany13@test.com', $user->getEmail());
        static::assertSame('wizaplace', $user->getCompany());
    }

    public function testRegisterUserJobTitle(): void
    {
        $apiClient = $this->buildApiClient();

        $registerUserCommand = (new RegisterUserCommand())
            ->setEmail('testJobTitle11@test.com')
            ->setPassword("password")
            ->setTitle(UserTitle::MR())
            ->setBirthday(new \DateTimeImmutable("2019-11-18"))
            ->setShipping(new UpdateUserAddressCommand())
            ->setBilling(new UpdateUserAddressCommand())
            ->setIsProfessional(true)
            ->setJobTitle("wizaplace")
        ;

        $userId = (new UserService($apiClient))->registerWithFullInfos($registerUserCommand);

        $apiClient->authenticate("testJobTitle11@test.com", 'password');

        $user = (new UserService($apiClient))->getProfileFromId($userId);

        static::assertSame("wizaplace", $user->getJobTitle());
    }

    public function testUpdateUserJobTitle(): void
    {
        $client = $this->buildApiClient();
        $userService = new UserService($client);

        // create new user
        $userId = $userService->register('testJobTitle12@test.com', 'password', 'Jean', 'Paul');

        $client->authenticate('testJobTitle12@test.com', 'password');
        $user = $userService->getProfileFromId($userId);
        static::assertSame('testJobTitle12@test.com', $user->getEmail());

        $userService->updateUser(
            (new UpdateUserCommand())
                ->setUserId($userId)
                ->setEmail('testJobTitle12@test.com')
                ->setFirstName('Paul')
                ->setLastName('Emploi')
                ->setBirthday(null)
                ->setIsProfessional(true)
                ->setJobTitle('wizaplace')
        );

        $client->authenticate('testJobTitle12@test.com', 'password');

        $user = $userService->getProfileFromId($userId);
        static::assertSame('testJobTitle12@test.com', $user->getEmail());
        static::assertSame('wizaplace', $user->getJobTitle());
    }

    public function testPatchUserJobTitle(): void
    {
        $client = $this->buildApiClient();
        $userService = new UserService($client);

        // create new user
        $userId = $userService->register('testJobTitle13@test.com', 'password', 'Jean', 'Paul');

        $client->authenticate('testJobTitle13@test.com', 'password');
        $user = $userService->getProfileFromId($userId);
        static::assertSame('testJobTitle13@test.com', $user->getEmail());

        $userService->patchUser(
            (new UpdateUserCommand())
                ->setUserId($userId)
                ->setIsProfessional(true)
                ->setJobTitle('wizaplace')
        );

        $client->authenticate('testJobTitle13@test.com', 'password');

        $user = $userService->getProfileFromId($userId);
        static::assertSame('testJobTitle13@test.com', $user->getEmail());
        static::assertSame('wizaplace', $user->getJobTitle());
    }

    public function testRegisterUserComment(): void
    {
        $apiClient = $this->buildApiClient();

        $registerUserCommand = (new RegisterUserCommand())
            ->setEmail('testComment11@test.com')
            ->setPassword("password")
            ->setTitle(UserTitle::MR())
            ->setBirthday(new \DateTimeImmutable("2019-11-18"))
            ->setShipping(new UpdateUserAddressCommand())
            ->setBilling(new UpdateUserAddressCommand())
            ->setIsProfessional(true)
            ->setComment("confirmed client")
        ;

        $userId = (new UserService($apiClient))->registerWithFullInfos($registerUserCommand);

        $apiClient->authenticate("testComment11@test.com", 'password');

        $user = (new UserService($apiClient))->getProfileFromId($userId);

        static::assertSame("confirmed client", $user->getComment());
    }

    public function testUpdateUserComment(): void
    {
        $client = $this->buildApiClient();
        $userService = new UserService($client);

        // create new user
        $userId = $userService->register('testComment12@test.com', 'password', 'Jean', 'Paul');

        $client->authenticate('testComment12@test.com', 'password');
        $user = $userService->getProfileFromId($userId);
        static::assertSame('testComment12@test.com', $user->getEmail());

        $userService->updateUser(
            (new UpdateUserCommand())
                ->setUserId($userId)
                ->setEmail('testComment12@test.com')
                ->setFirstName('Paul')
                ->setLastName('Emploi')
                ->setBirthday(null)
                ->setIsProfessional(true)
                ->setComment('confirmed client')
        );

        $client->authenticate('testComment12@test.com', 'password');

        $user = $userService->getProfileFromId($userId);
        static::assertSame('testComment12@test.com', $user->getEmail());
        static::assertSame('confirmed client', $user->getComment());
    }

    public function testPatchUserComment(): void
    {
        $client = $this->buildApiClient();
        $userService = new UserService($client);

        // create new user
        $userId = $userService->register('testComment13@test.com', 'password', 'Jean', 'Paul');

        $client->authenticate('testComment13@test.com', 'password');
        $user = $userService->getProfileFromId($userId);
        static::assertSame('testComment13@test.com', $user->getEmail());

        $userService->patchUser(
            (new UpdateUserCommand())
                ->setUserId($userId)
                ->setIsProfessional(true)
                ->setComment('confirmed client')
        );

        $client->authenticate('testComment13@test.com', 'password');

        $user = $userService->getProfileFromId($userId);
        static::assertSame('testComment13@test.com', $user->getEmail());
        static::assertSame('confirmed client', $user->getComment());
    }

    public function testRegisterUserLegalIdentifier(): void
    {
        $apiClient = $this->buildApiClient();

        $registerUserCommand = (new RegisterUserCommand())
            ->setEmail('testLegalIdentifier13@test.com')
            ->setPassword("password")
            ->setTitle(UserTitle::MR())
            ->setBirthday(new \DateTimeImmutable("2019-11-18"))
            ->setShipping(new UpdateUserAddressCommand())
            ->setBilling(new UpdateUserAddressCommand())
            ->setIsProfessional(true)
            ->setLegalIdentifier("wizaplace")
        ;

        $userId = (new UserService($apiClient))->registerWithFullInfos($registerUserCommand);

        $apiClient->authenticate("testLegalIdentifier13@test.com", 'password');

        $user = (new UserService($apiClient))->getProfileFromId($userId);

        static::assertSame("wizaplace", $user->getLegalIdentifier());
    }

    public function testUpdateUserLegalIdentifier(): void
    {
        $client = $this->buildApiClient();
        $userService = new UserService($client);

        // create new user
        $userId = $userService->register('testLegalIdentifier14@test.com', 'password', 'Jean', 'Paul');

        $client->authenticate('testLegalIdentifier14@test.com', 'password');
        $user = $userService->getProfileFromId($userId);
        static::assertSame('testLegalIdentifier14@test.com', $user->getEmail());

        $userService->updateUser(
            (new UpdateUserCommand())
                ->setUserId($userId)
                ->setEmail('testLegalIdentifier14@test.com')
                ->setFirstName('Paul')
                ->setLastName('Emploi')
                ->setBirthday(null)
                ->setIsProfessional(true)
                ->setLegalIdentifier('wizaplace')
        );

        $client->authenticate('testLegalIdentifier14@test.com', 'password');

        $user = $userService->getProfileFromId($userId);
        static::assertSame('testLegalIdentifier14@test.com', $user->getEmail());
        static::assertSame('wizaplace', $user->getLegalIdentifier());
    }

    public function testPatchUserLegalIdentifier(): void
    {
        $client = $this->buildApiClient();
        $userService = new UserService($client);

        // create new user
        $userId = $userService->register('testLegalIdentifier16@test.com', 'password', 'Jean', 'Paul');

        $client->authenticate('testLegalIdentifier16@test.com', 'password');
        $user = $userService->getProfileFromId($userId);
        static::assertSame('testLegalIdentifier16@test.com', $user->getEmail());

        $userService->patchUser(
            (new UpdateUserCommand())
                ->setUserId($userId)
                ->setIsProfessional(true)
                ->setLegalIdentifier('wizaplace')
        );

        $client->authenticate('testLegalIdentifier16@test.com', 'password');

        $user = $userService->getProfileFromId($userId);
        static::assertSame('testLegalIdentifier16@test.com', $user->getEmail());
        static::assertSame('wizaplace', $user->getLegalIdentifier());
    }

    public function testRegisterUserLoyaltyIdentifier(): void
    {
        $apiClient = $this->buildApiClient();

        $registerUserCommand = (new RegisterUserCommand())
            ->setEmail('testLoyaltyIdentifier@test.com')
            ->setPassword("password")
            ->setTitle(UserTitle::MR())
            ->setBirthday(new \DateTimeImmutable("2019-11-18"))
            ->setShipping(new UpdateUserAddressCommand())
            ->setBilling(new UpdateUserAddressCommand())
            ->setIsProfessional(true)
            ->setLoyaltyIdentifier("wizaplace")
        ;

        $userId = (new UserService($apiClient))->registerWithFullInfos($registerUserCommand);

        $apiClient->authenticate("testLoyaltyIdentifier@test.com", 'password');

        $user = (new UserService($apiClient))->getProfileFromId($userId);

        static::assertSame("wizaplace", $user->getLoyaltyIdentifier());
    }

    public function testUpdateUserLoyaltyIdentifier(): void
    {
        $client = $this->buildApiClient();
        $userService = new UserService($client);

        // create new user
        $userId = $userService->register('testLoyaltyIdentifierUpdate@test.com', 'password', 'Jean', 'Paul');

        $client->authenticate('testLoyaltyIdentifierUpdate@test.com', 'password');
        $user = $userService->getProfileFromId($userId);
        static::assertSame('testLoyaltyIdentifierUpdate@test.com', $user->getEmail());

        $userService->updateUser(
            (new UpdateUserCommand())
                ->setUserId($userId)
                ->setEmail('testLoyaltyIdentifierUpdate@test.com')
                ->setFirstName('Paul')
                ->setLastName('Emploi')
                ->setBirthday(null)
                ->setIsProfessional(true)
                ->setLoyaltyIdentifier('wizaplace')
        );

        $client->authenticate('testLoyaltyIdentifierUpdate@test.com', 'password');

        $user = $userService->getProfileFromId($userId);
        static::assertSame('testLoyaltyIdentifierUpdate@test.com', $user->getEmail());
        static::assertSame('wizaplace', $user->getLoyaltyIdentifier());
    }

    public function testPatchUserLoyaltyIdentifier(): void
    {
        $client = $this->buildApiClient();
        $userService = new UserService($client);

        // create new user
        $userId = $userService->register('testLoyaltyIdentifierPatch@test.com', 'password', 'Jean', 'Paul');

        $client->authenticate('testLoyaltyIdentifierPatch@test.com', 'password');
        $user = $userService->getProfileFromId($userId);
        static::assertSame('testLoyaltyIdentifierPatch@test.com', $user->getEmail());

        $userService->patchUser(
            (new UpdateUserCommand())
                ->setUserId($userId)
                ->setIsProfessional(true)
                ->setLoyaltyIdentifier('wizaplace')
        );

        $client->authenticate('testLoyaltyIdentifierPatch@test.com', 'password');

        $user = $userService->getProfileFromId($userId);
        static::assertSame('testLoyaltyIdentifierPatch@test.com', $user->getEmail());
        static::assertSame('wizaplace', $user->getLoyaltyIdentifier());
    }
}
