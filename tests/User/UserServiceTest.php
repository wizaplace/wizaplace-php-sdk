<?php

/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Tests\User;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Uri;
use Wizaplace\SDK\AbstractService;
use Wizaplace\SDK\ApiClient;
use Wizaplace\SDK\Authentication\ApiKey;
use Wizaplace\SDK\Authentication\AuthenticationRequired;
use Wizaplace\SDK\Authentication\BadCredentials;
use Wizaplace\SDK\Company\CompanyRegistration;
use Wizaplace\SDK\Company\CompanyService;
use Wizaplace\SDK\PaginatedData;
use Wizaplace\SDK\Subscription\SubscriptionSummary;
use Wizaplace\SDK\Tests\ApiTestCase;
use Wizaplace\SDK\User\AddressBookService;
use Wizaplace\SDK\User\RegisterUserCommand;
use Wizaplace\SDK\User\UpdateUserAddressCommand;
use Wizaplace\SDK\User\UpdateUserAddressesCommand;
use Wizaplace\SDK\User\UpdateUserCommand;
use Wizaplace\SDK\User\UserAddress;
use Wizaplace\SDK\User\UserAlreadyExists;
use Wizaplace\SDK\User\UserFilters;
use Wizaplace\SDK\User\UserService;
use Wizaplace\SDK\User\UserTitle;
use Wizaplace\SDK\User\UserType;
use Wizaplace\SDK\User\Nationality;

/**
 * @see UserService
 */
final class UserServiceTest extends ApiTestCase
{
    /** @var ApiClient */
    private $client;

    /** @var UserService */
    private $userService;

    public function setUp(): void
    {
        parent::setUp();

        $this->client = $this->buildApiClient();
        $this->userService = new UserService($this->client);
    }

    public function testCreateUser(): void
    {
        $userEmail = 'user53@example.com';
        $userPassword = static::VALID_PASSWORD;

        // create new user
        $userId = $this->userService->register($userEmail, $userPassword);

        // authenticate with newly created user
        $this->client->authenticate($userEmail, $userPassword);

        // fetch user
        $user = $this->userService->getProfileFromId($userId);

        static::assertNotNull($user, 'User exists');
        static::assertSame($userEmail, $user->getEmail());
        static::assertSame($userId, $user->getId());
        static::assertNull($user->getTitle());
        static::assertSame('', $user->getFirstname());
        static::assertSame('', $user->getLastname());
        static::assertNull($user->getBirthday());
        static::assertNull($user->getCurrencyCode());
        static::assertSame('', $user->getPhone());
        static::assertNull($user->getCompanyId());
        static::assertFalse($user->isVendor());
        static::assertSame(UserType::CLIENT()->getValue(), $user->getType()->getValue());
        static::assertInstanceOf(\DateTimeImmutable::class, $user->getRegisteredAt());

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

    public function testCreateUserWithInvalidPassword()
    {
        static::expectException(ClientException::class);

        $this->userService->register('user106@example.com', static::INVALID_PASSWORD);
    }

    public function testCreateUserWithAddresses(): void
    {
        $userEmail = 'user111@example.com';
        $userPassword = static::VALID_PASSWORD;
        $userFistname = 'John';
        $userLastname = 'Doe';
        $userBilling = new UserAddress(
            [
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
            ]
        );
        $userShipping = new UserAddress(
            [
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
            ]
        );

        // create new user
        $userId = $this->userService->register($userEmail, $userPassword, $userFistname, $userLastname, $userBilling, $userShipping);

        // authenticate with newly created user
        $this->client->authenticate($userEmail, $userPassword);

        // fetch user
        $user = $this->userService->getProfileFromId($userId);

        static::assertNotNull($user, 'User exists');
        static::assertSame($userEmail, $user->getEmail());
        static::assertSame($userId, $user->getId());
        static::assertNull($user->getTitle());
        static::assertSame($userFistname, $user->getFirstname());
        static::assertSame($userLastname, $user->getLastname());
        static::assertNull($user->getBirthday());
        static::assertSame('', $user->getPhone());
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
        $userEmail = 'user194@example.com';
        $userPassword = static::VALID_PASSWORD;
        $userFistname = 'John';
        $userLastname = 'Doe';
        $userBilling = new UserAddress(
            [
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
            ]
        );
        $userShipping = new UserAddress(
            [
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
            ]
        );

        // create new user
        static::expectException(\Exception::class);
        $this->userService->register($userEmail, $userPassword, $userFistname, $userLastname, $userBilling);

        // create new user
        static::expectException(\Exception::class);
        $this->userService->register($userEmail, $userPassword, $userFistname, $userLastname, null, $userShipping);
    }

    public function testCreateUserOnlyWithCompanyName(): void
    {
        $userEmail = 'user238@example.com';
        $userPassword = static::VALID_PASSWORD;
        $userFistname = 'Paul';
        $userLastname = 'Jean';
        $userBilling = new UserAddress(
            [
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
            ]
        );
        $userShipping = new UserAddress(
            [
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
            ]
        );

        // create new user
        $userId = $this->userService->register($userEmail, $userPassword, $userFistname, $userLastname, $userBilling, $userShipping);

        // authenticate with newly created user
        $this->client->authenticate($userEmail, $userPassword);

        // fetch user
        $user = $this->userService->getProfileFromId($userId);

        static::assertNotNull($user, 'User exists');
        static::assertSame($userEmail, $user->getEmail());
        static::assertSame($userId, $user->getId());
        static::assertNull($user->getTitle());
        static::assertSame($userFistname, $user->getFirstname());
        static::assertSame($userLastname, $user->getLastname());
        static::assertSame('', $user->getPhone());
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
            ->setEmail('user331@example.com')
            ->setPassword(static::VALID_PASSWORD)
            ->setFirstName('Jane')
            ->setLastName('Doe')
            ->setPhone('0102030405')
            ->setBirthday(\DateTime::createFromFormat('Y-m-d', '1998-07-12'))
            ->setTitle(UserTitle::MRS())
            ->setBilling($addressCommand)
            ->setShipping($addressCommand);

        // create new user
        $userId = $this->userService->registerWithFullInfos($userCommand);

        // authenticate with newly created user
        $this->client->authenticate($userCommand->getEmail(), $userCommand->getPassword());

        // fetch user
        $user = $this->userService->getProfileFromId($userId);

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
        $this->userService->register('user389@example.com', static::VALID_PASSWORD);

        // create already existing user
        $this->expectException(UserAlreadyExists::class);
        $this->userService->register('user389@example.com', static::VALID_PASSWORD);
    }

    public function testUpdateUser(): void
    {
        // create new user
        $userId = $this->userService->register(
            'user400@example.com',
            static::VALID_PASSWORD,
            'Jean',
            'Paul'
        );

        $this->client->authenticate('user400@example.com', static::VALID_PASSWORD);
        $user = $this->userService->getProfileFromId($userId);

        static::assertSame('user400@example.com', $user->getEmail());
        static::assertNull($user->getTitle());
        static::assertSame('Jean', $user->getFirstname());
        static::assertSame('Paul', $user->getLastname());
        static::assertNull($user->getBirthday());
        static::assertSame('fr', $user->getLanguage());
        static::assertNull($user->getCompanyId());
        static::assertFalse($user->isVendor());
        static::assertSame(UserType::CLIENT()->getValue(), $user->getType()->getValue());
        static::assertNull($user->getCurrencyCode());

        $this->userService->updateUser(
            (new UpdateUserCommand())
                ->setUserId($userId)
                ->setEmail('user423@example.com')
                ->setFirstName('Jacques')
                ->setLastName('Jules')
                ->setTitle(UserTitle::MR())
                ->setPhone('0102030405')
                ->setBirthday(\DateTime::createFromFormat('Y-m-d', '1963-02-17'))
                ->setLanguage('en')
                ->setCurrencyCode('EUR')
        );

        $this->client->authenticate('user423@example.com', static::VALID_PASSWORD);

        $user = $this->userService->getProfileFromId($userId);
        static::assertSame('user423@example.com', $user->getEmail());
        static::assertTrue(UserTitle::MR()->equals($user->getTitle()));
        static::assertSame('Jacques', $user->getFirstname());
        static::assertSame('Jules', $user->getLastname());
        static::assertSame('1963-02-17', $user->getBirthday()->format('Y-m-d'));
        static::assertSame('0102030405', $user->getPhone());
        static::assertSame('en', $user->getLanguage());
        static::assertNull($user->getCompanyId());
        static::assertFalse($user->isVendor());
        static::assertSame(UserType::CLIENT()->getValue(), $user->getType()->getValue());
        static::assertSame('EUR', $user->getCurrencyCode());
    }

    public function testPatchUser(): void
    {
        $userId = $this->userService->register('user451@example.com', static::VALID_PASSWORD, 'Jean', 'Paul');
        $this->client->authenticate('user451@example.com', static::VALID_PASSWORD);

        $this->userService->patchUser(
            (new UpdateUserCommand())
                ->setUserId($userId)
                ->setCurrencyCode('EUR')
                ->setPhone('0102030405')
        );

        $this->client->authenticate('user451@example.com', static::VALID_PASSWORD);
        $user = $this->userService->getProfileFromId($userId);
        static::assertSame('EUR', $user->getCurrencyCode());
        static::assertSame('0102030405', $user->getPhone());
    }

    public function testUpdateUserAddresses(): void
    {
        // create new user
        $userId = $this->userService->register('user470@example.com', static::VALID_PASSWORD, 'Jean', 'Paul');

        $this->client->authenticate('user470@example.com', static::VALID_PASSWORD);
        $user = $this->userService->getProfileFromId($userId);
        static::assertNull($user->getShippingAddress()->getTitle());
        static::assertSame('Jean', $user->getShippingAddress()->getFirstName());
        static::assertSame('Paul', $user->getShippingAddress()->getLastName());

        static::assertNull($user->getBillingAddress()->getTitle());
        static::assertSame('Jean', $user->getBillingAddress()->getFirstName());
        static::assertSame('Paul', $user->getBillingAddress()->getLastName());

        $this->userService->updateUserAdresses(
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

        $user = $this->userService->getProfileFromId($userId);

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
        // create new user
        $userId = $this->userService->register(
            'user544@example.com',
            static::VALID_PASSWORD,
            'Jean',
            'Paul'
        );

        $this->client->authenticate('user544@example.com', static::VALID_PASSWORD);
        $user = $this->userService->getProfileFromId($userId);
        static::assertNull($user->getShippingAddress()->getTitle());
        static::assertSame('Jean', $user->getShippingAddress()->getFirstName());
        static::assertSame('Paul', $user->getShippingAddress()->getLastName());

        static::assertNull($user->getBillingAddress()->getTitle());
        static::assertSame('Jean', $user->getBillingAddress()->getFirstName());
        static::assertSame('Paul', $user->getBillingAddress()->getLastName());

        $this->userService->updateUserAdresses(
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

        $user = $this->userService->getProfileFromId($userId);

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

    public function testUpdateUserAddressesWithLabelAndComment(): void
    {
        $client = $this->buildApiClient();
        $userService = new UserService($client);
        $userId = $userService->register('Paul2@example.com', 'password', 'Jean', 'Paul');
        $client->authenticate('Paul2@example.com', 'password');

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
                        ->setLabel('Domicile')
                        ->setComment('Près de la garre')
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
                        ->setLabel('Bureau')
                        ->setComment('Près de la poste')
                )
        );
        $user = $userService->getProfileFromId($userId);

        static::assertSame('Domicile', $user->getShippingAddress()->getLabel());
        static::assertSame('Près de la garre', $user->getShippingAddress()->getComment());
        static::assertSame('Bureau', $user->getBillingAddress()->getLabel());
        static::assertSame('Près de la poste', $user->getBillingAddress()->getComment());
    }

    public function testCreateUserAddressesWithNullFields(): void
    {
        $client = $this->buildApiClient();
        $addressBookService = new AddressBookService($client);

        $client->authenticate('user@wizaplace.com', static::VALID_PASSWORD);

        $adddressdata = ['firstname' => null];
        $addressId = $addressBookService->createAddressInAddressBook(3, $adddressdata);
        static::assertUuid($addressId);

        $listAddressBook = $addressBookService->listAddressBook(3);
        static::assertSame('', $listAddressBook->getItems()[0]->getFirstName());
    }

    public function testUpdateUserAddressesWithAllFields(): void
    {
        $client = $this->buildApiClient();
        $userService = new UserService($client);
        $addressBookService = new AddressBookService($client);
        // create new user
        $userId = $userService->register('Jean123@example.com', 'password', 'Jean', 'Paul');

        $client->authenticate('Jean123@example.com', 'password');

        // create new address
        $adddressdata = [
            'label' => 'Domicile',
            'firstname' => 'firstname',
            'lastname' => 'lastname',
            'title' => UserTitle::MR(),
            'company' => 'ACME',
            'phone' => '20000',
            'address' => '40 rue Laure Diebold',
            'address_2' => '3ème étage',
            'city' => 'Lyon',
            'zipcode' => '69009',
            'country' => 'FR',
            'division_code' => 'FR-03',
            'comment' => 'Près de la poste'
        ];

        $addressId = $addressBookService->createAddressInAddressBook($userId, $adddressdata);

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
                        ->setLabel('Domicile')
                        ->setComment('Près de la garre')
                        ->setId($addressId)
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
                        ->setLabel('Bureau')
                        ->setComment('Près de la poste')
                        ->setId($addressId)
                )
        );
        $user = $userService->getProfileFromId($userId);

        static::assertSame($addressId, $user->getShippingAddress()->getId());
        static::assertSame('Domicile', $user->getShippingAddress()->getLabel());
        static::assertSame('firstname', $user->getShippingAddress()->getFirstName());
        static::assertSame('lastname', $user->getShippingAddress()->getLastName());
        static::assertSame('mr', $user->getShippingAddress()->getTitle()->getValue());
        static::assertSame('ACME', $user->getShippingAddress()->getCompany());
        static::assertSame('20000', $user->getShippingAddress()->getPhone());
        static::assertSame('40 rue Laure Diebold', $user->getShippingAddress()->getAddress());
        static::assertSame('3ème étage', $user->getShippingAddress()->getAddressSecondLine());
        static::assertSame('Lyon', $user->getShippingAddress()->getCity());
        static::assertSame('69009', $user->getShippingAddress()->getZipCode());
        static::assertSame('FR', $user->getShippingAddress()->getCountry());
        static::assertSame('FR-03', $user->getShippingAddress()->getDivisionCode());
        static::assertSame('Près de la poste', $user->getShippingAddress()->getComment());

        static::assertSame($addressId, $user->getBillingAddress()->getId());
        static::assertSame('Domicile', $user->getBillingAddress()->getLabel());
        static::assertSame('firstname', $user->getBillingAddress()->getFirstName());
        static::assertSame('lastname', $user->getBillingAddress()->getLastName());
        static::assertSame('mr', $user->getBillingAddress()->getTitle()->getValue());
        static::assertSame('ACME', $user->getBillingAddress()->getCompany());
        static::assertSame('20000', $user->getBillingAddress()->getPhone());
        static::assertSame('40 rue Laure Diebold', $user->getBillingAddress()->getAddress());
        static::assertSame('3ème étage', $user->getBillingAddress()->getAddressSecondLine());
        static::assertSame('Lyon', $user->getBillingAddress()->getCity());
        static::assertSame('69009', $user->getBillingAddress()->getZipCode());
        static::assertSame('FR', $user->getBillingAddress()->getCountry());
        static::assertSame('FR-03', $user->getBillingAddress()->getDivisionCode());
        static::assertSame('Près de la poste', $user->getBillingAddress()->getComment());
    }

    public function testUpdateUserAddressesWithMissingFieldsWhenFullFieldsBefore(): void
    {
        // create new user
        $userId = $this->userService->register(
            'user604@example.com',
            static::VALID_PASSWORD,
            'Paul',
            'Jacques'
        );

        $this->client->authenticate('user604@example.com', static::VALID_PASSWORD);

        //test juste après création du compte
        $user = $this->userService->getProfileFromId($userId);
        static::assertNull($user->getShippingAddress()->getTitle());
        static::assertSame('Paul', $user->getShippingAddress()->getFirstName());
        static::assertSame('Jacques', $user->getShippingAddress()->getLastName());

        static::assertNull($user->getBillingAddress()->getTitle());
        static::assertSame('Paul', $user->getBillingAddress()->getFirstName());
        static::assertSame('Jacques', $user->getBillingAddress()->getLastName());

        //ajout d'adresses et de nom d'entreprise
        $this->userService->updateUserAdresses(
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

        $user = $this->userService->getProfileFromId($userId);

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

        $this->userService->updateUserAdresses(
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

        $user = $this->userService->getProfileFromId($userId);

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
        // create new user
        $userId = $this->userService->register('user708@example.com', static::VALID_PASSWORD, 'Jean', 'Paul');

        $this->client->authenticate('user708@example.com', static::VALID_PASSWORD);
        $user = $this->userService->getProfileFromId($userId);
        static::assertSame('user708@example.com', $user->getEmail());
        static::assertNull($user->getTitle());
        static::assertSame('Jean', $user->getFirstname());
        static::assertSame('Paul', $user->getLastname());
        static::assertNull($user->getBirthday());
        static::assertSame(UserType::CLIENT()->getValue(), $user->getType()->getValue());

        $this->userService->updateUser(
            (new UpdateUserCommand())
                ->setUserId($userId)
                ->setEmail('user722@example.com')
                ->setFirstName('Jacques')
                ->setLastName('Jules')
        );

        $this->client->authenticate('user722@example.com', static::VALID_PASSWORD);

        $user = $this->userService->getProfileFromId($userId);
        static::assertSame('user722@example.com', $user->getEmail());
        static::assertNull($user->getTitle());
        static::assertSame('Jacques', $user->getFirstname());
        static::assertSame('Jules', $user->getLastname());
        static::assertNull($user->getBirthday());
        static::assertSame(UserType::CLIENT()->getValue(), $user->getType()->getValue());
    }

    public function testRecoverPassword(): void
    {
        $userEmail = 'user740@example.com';
        $userPassword = static::VALID_PASSWORD;
        $this->userService->register($userEmail, $userPassword);

        static::assertNull($this->userService->recoverPassword($userEmail));
    }

    public function testRecoverPasswordWithCustomUrl(): void
    {
        $userEmail = 'user749@example.com';
        $userPassword = static::VALID_PASSWORD;
        $this->userService->register($userEmail, $userPassword);

        static::assertNull($this->userService->recoverPassword($userEmail, new Uri('https://marketplace.example.com/recover-password=token')));
    }

    public function testRecoverPasswordForNonExistingEmail(): void
    {
        static::assertNull($this->userService->recoverPassword('user758@example.com'));
    }

    public function testChangePassword(): void
    {
        $userEmail = 'user763@example.com';
        $userPassword = static::VALID_PASSWORD;

        // create new user
        $userId = $this->userService->register($userEmail, $userPassword);

        // authenticate with newly created user
        $this->client->authenticate($userEmail, $userPassword);

        $this->userService->changePassword($userId, 'W1nt3r.Hunt3r');

        static::assertNotEmpty($this->client->authenticate($userEmail, 'W1nt3r.Hunt3r'));
        static::expectException(BadCredentials::class);
        static::assertNotEmpty($this->client->authenticate($userEmail, $userPassword));
    }

    public function testChangePasswordWithInvalidPassword(): void
    {
        $userEmail = 'user781@example.com';
        $userPassword = static::VALID_PASSWORD;

        // create new user
        $userId = $this->userService->register($userEmail, $userPassword);

        // authenticate with newly created user
        $this->client->authenticate($userEmail, $userPassword);

        static::expectException(ClientException::class);
        $this->userService->changePassword($userId, static::INVALID_PASSWORD);
    }

    public function testChangePasswordAnonymously(): void
    {
        static::expectException(AuthenticationRequired::class);
        $this->userService->changePassword(1, static::VALID_PASSWORD);
    }

    public function testChangePasswordWithToken(): void
    {
        $cannotLogin = false;
        try {
            $this->client->authenticate('user806@example.com', static::INVALID_PASSWORD);
        } catch (BadCredentials $e) {
            $cannotLogin = true;
        }
        static::assertTrue($cannotLogin);

        $this->userService->changePasswordWithRecoveryToken(md5('fake_secret_token'), 'W1nt3r.Hunt3r');

        $apiKey = $this->client->authenticate('user806@example.com', 'W1nt3r.Hunt3r');
        static::assertInstanceOf(ApiKey::class, $apiKey);
    }

    public function testGetUserCompany(): void
    {
        $userId = ($this->client->authenticate('user822@example.com', static::VALID_PASSWORD))->getId();

        $user = $this->userService->getProfileFromId($userId);
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
        $userCommand->setEmail('user845@example.com');
        $userCommand->setPassword(static::VALID_PASSWORD);
        $userCommand->setFirstName('Paul');
        $userCommand->setLastName('Jacques');
        $userCommand->setBirthday(\DateTime::createFromFormat('Y-m-d', '1997-07-12'));
        $userCommand->setTitle(UserTitle::MR());
        $userCommand->setBilling($addressCommand);
        $userCommand->setShipping($addressCommand);

        // create new user
        $userId = $this->userService->registerWithFullInfos($userCommand);

        $this->client->authenticate('user845@example.com', static::VALID_PASSWORD);

        $this->userService->updateUser(
            (new UpdateUserCommand())
                ->setUserId($userId)
                ->setEmail('user864@example.com')
                ->setFirstName('Paul')
                ->setLastName('Emploi')
                ->setBirthday(null)
        );
        $this->client->authenticate('user864@example.com', static::VALID_PASSWORD);

        $user = $this->userService->getProfileFromId($userId);
        static::assertSame('user864@example.com', $user->getEmail());
        static::assertSame(UserTitle::MR()->getValue(), $user->getTitle()->getValue());
        static::assertSame('Paul', $user->getFirstname());
        static::assertSame('Emploi', $user->getLastname());
        static::assertInstanceOf(\DateTimeImmutable::class, $user->getBirthday());
        static::assertSame(UserType::CLIENT()->getValue(), $user->getType()->getValue());
    }

    public function testGetPendingCompanyId(): void
    {
        $userId = ($this->client->authenticate('user880@example.com', static::VALID_PASSWORD))->getId();

        $user = $this->userService->getProfileFromId($userId);

        static::assertSame(8, $user->getPendingCompanyId());
        static::assertNull($user->getCompanyId());
    }

    public function testGetExternalIdentifier(): void
    {
        $userId = ($this->client->authenticate('user890@example.com', static::VALID_PASSWORD))->getId();
        $user = $this->userService->getProfileFromId($userId);

        static::assertSame("id externe", $user->getExternalIdentifier());
    }

    public function testRegisterUserExternalIdentifier(): void
    {
        $registerUserCommand = (new RegisterUserCommand())
            ->setEmail('user899@example.com')
            ->setPassword(static::VALID_PASSWORD)
            ->setTitle(UserTitle::MR())
            ->setBirthday(new \DateTimeImmutable("2019-11-18"))
            ->setShipping(new UpdateUserAddressCommand())
            ->setBilling(new UpdateUserAddressCommand())
            ->setExternalIdentifier("id_externe")
        ;

        $userId = $this->userService->registerWithFullInfos($registerUserCommand);
        $this->client->authenticate('user899@example.com', static::VALID_PASSWORD);
        $user = $this->userService->getProfileFromId($userId);

        static::assertSame("id_externe", $user->getExternalIdentifier());
    }

    public function testUpdateUserExternalIdentifier(): void
    {
        // create new user
        $userId = $this->userService->register('user918@example.com', static::VALID_PASSWORD, 'Jean', 'Paul');

        $this->client->authenticate('user918@example.com', static::VALID_PASSWORD);
        $user = $this->userService->getProfileFromId($userId);
        static::assertSame('user918@example.com', $user->getEmail());

        $this->userService->updateUser(
            (new UpdateUserCommand())
                ->setUserId($userId)
                ->setEmail('user918@example.com')
                ->setFirstName('Paul')
                ->setLastName('Emploi')
                ->setBirthday(null)
                ->setExternalIdentifier('externalIdentified0012')
        );

        $this->client->authenticate('user918@example.com', static::VALID_PASSWORD);

        $user = $this->userService->getProfileFromId($userId);
        static::assertSame('user918@example.com', $user->getEmail());
        static::assertSame('externalIdentified0012', $user->getExternalIdentifier());
    }

    public function testPatchUserExternalIdentifier(): void
    {
        // create new user
        $userId = $this->userService->register('user945@example.com', static::VALID_PASSWORD, 'Jean', 'Paul');

        $this->client->authenticate('user945@example.com', static::VALID_PASSWORD);
        $user = $this->userService->getProfileFromId($userId);
        static::assertSame('user945@example.com', $user->getEmail());

        $this->userService->patchUser(
            (new UpdateUserCommand())
                ->setUserId($userId)
                ->setCurrencyCode('EUR')
                ->setPhone('0102030405')
                ->setExternalIdentifier('externalIdentified0012')
        );

        $this->client->authenticate('user945@example.com', static::VALID_PASSWORD);

        $user = $this->userService->getProfileFromId($userId);
        static::assertSame('user945@example.com', $user->getEmail());
        static::assertSame('externalIdentified0012', $user->getExternalIdentifier());
    }

    public function testRegisterUserWithIsProfessional(): void
    {
        $registerUserCommand = (new RegisterUserCommand())
            ->setEmail('user969@example.com')
            ->setPassword(static::VALID_PASSWORD)
            ->setTitle(UserTitle::MR())
            ->setBirthday(new \DateTimeImmutable("2019-11-18"))
            ->setShipping(new UpdateUserAddressCommand())
            ->setBilling(new UpdateUserAddressCommand())
            ->setIsProfessional(true)
        ;

        $userId = $this->userService->registerWithFullInfos($registerUserCommand);
        $this->client->authenticate("user969@example.com", static::VALID_PASSWORD);
        $user = $this->userService->getProfileFromId($userId);

        static::assertTrue($user->isProfessional());
    }

    public function testUpdateUserIsProfessional(): void
    {
        // create new user
        $registerUserCommand = (new RegisterUserCommand())
            ->setEmail('user989@example.com')
            ->setPassword(static::VALID_PASSWORD)
            ->setTitle(UserTitle::MR())
            ->setBirthday(new \DateTimeImmutable("2019-11-18"))
            ->setShipping(new UpdateUserAddressCommand())
            ->setBilling(new UpdateUserAddressCommand())
            ->setIsProfessional(true)
        ;

        $userId = $this->userService->registerWithFullInfos($registerUserCommand);

        $this->client->authenticate('user989@example.com', static::VALID_PASSWORD);
        $user = $this->userService->getProfileFromId($userId);
        static::assertTrue($user->isProfessional());

        // update user
        $this->userService->updateUser(
            (new UpdateUserCommand())
                ->setUserId($userId)
                ->setEmail('user989@example.com')
                ->setFirstName('Paul')
                ->setLastName('Professional')
                ->setIsProfessional(false)
        );

        $this->client->authenticate('user989@example.com', static::VALID_PASSWORD);
        $user = $this->userService->getProfileFromId($userId);

        static::assertFalse($user->isProfessional());
    }

    public function testPatchUserIsProfessional(): void
    {
        // create new user
        $registerUserCommand = (new RegisterUserCommand())
            ->setEmail('user1024@example.com')
            ->setPassword(static::VALID_PASSWORD)
            ->setTitle(UserTitle::MR())
            ->setBirthday(new \DateTimeImmutable("2019-11-18"))
            ->setShipping(new UpdateUserAddressCommand())
            ->setBilling(new UpdateUserAddressCommand())
            ->setIsProfessional(true)
        ;

        $userId = $this->userService->registerWithFullInfos($registerUserCommand);

        $this->client->authenticate('user1024@example.com', static::VALID_PASSWORD);
        $user = $this->userService->getProfileFromId($userId);
        static::assertTrue($user->isProfessional());

        // update user
        $this->userService->patchUser(
            (new UpdateUserCommand())
                ->setUserId($userId)
                ->setCurrencyCode('EUR')
                ->setPhone('0102030405')
                ->setIsProfessional(false)
        );

        $this->client->authenticate('user1024@example.com', static::VALID_PASSWORD);
        $user = $this->userService->getProfileFromId($userId);
        static::assertFalse($user->isProfessional());
    }

    public function testRegisterUserIntraEuropeanCommunityVAT(): void
    {
        $registerUserCommand = (new RegisterUserCommand())
            ->setEmail('user1059@example.com')
            ->setPassword(static::VALID_PASSWORD)
            ->setTitle(UserTitle::MR())
            ->setBirthday(new \DateTimeImmutable("2019-11-18"))
            ->setShipping(new UpdateUserAddressCommand())
            ->setBilling(new UpdateUserAddressCommand())
            ->setIsProfessional(true)
            ->setIntraEuropeanCommunityVAT("X1234567890")
        ;

        $userId = $this->userService->registerWithFullInfos($registerUserCommand);

        $this->client->authenticate('user1059@example.com', static::VALID_PASSWORD);

        $user = $this->userService->getProfileFromId($userId);

        static::assertSame("X1234567890", $user->getIntraEuropeanCommunityVAT());
    }

    public function testUpdateUserIntraEuropeanCommunityVAT(): void
    {
        // create new user
        $userId = $this->userService->register('user1079@example.com', static::VALID_PASSWORD, 'Jean', 'Paul');

        $this->client->authenticate('user1079@example.com', static::VALID_PASSWORD);
        $user = $this->userService->getProfileFromId($userId);
        static::assertSame('user1079@example.com', $user->getEmail());

        $this->userService->updateUser(
            (new UpdateUserCommand())
                ->setUserId($userId)
                ->setEmail('user1079@example.com')
                ->setFirstName('Paul')
                ->setLastName('Emploi')
                ->setBirthday(null)
                ->setIsProfessional(true)
                ->setIntraEuropeanCommunityVAT('X1234567890')
        );

        $this->client->authenticate('user1079@example.com', static::VALID_PASSWORD);

        $user = $this->userService->getProfileFromId($userId);
        static::assertSame('user1079@example.com', $user->getEmail());
        static::assertSame('X1234567890', $user->getIntraEuropeanCommunityVAT());
    }

    public function testPatchUserIntraEuropeanCommunityVAT(): void
    {
        // create new user
        $userId = $this->userService->register('user1106@test.com', static::VALID_PASSWORD, 'Jean', 'Paul');

        $this->client->authenticate('user1106@test.com', static::VALID_PASSWORD);
        $user = $this->userService->getProfileFromId($userId);
        static::assertSame('user1106@test.com', $user->getEmail());

        $this->userService->patchUser(
            (new UpdateUserCommand())
                ->setUserId($userId)
                ->setIsProfessional(true)
                ->setIntraEuropeanCommunityVAT('X1234567890')
        );

        $this->client->authenticate('user1106@test.com', static::VALID_PASSWORD);

        $user = $this->userService->getProfileFromId($userId);
        static::assertSame('user1106@test.com', $user->getEmail());
        static::assertSame('X1234567890', $user->getIntraEuropeanCommunityVAT());
    }

    public function testRegisterUserCompany(): void
    {
        $registerUserCommand = (new RegisterUserCommand())
            ->setEmail('user1129@test.com')
            ->setPassword(static::VALID_PASSWORD)
            ->setTitle(UserTitle::MR())
            ->setBirthday(new \DateTimeImmutable("2019-11-18"))
            ->setShipping(new UpdateUserAddressCommand())
            ->setBilling(new UpdateUserAddressCommand())
            ->setIsProfessional(true)
            ->setCompany("wizaplace")
        ;

        $userId = $this->userService->registerWithFullInfos($registerUserCommand);

        $this->client->authenticate('user1129@test.com', static::VALID_PASSWORD);

        $user = $this->userService->getProfileFromId($userId);

        static::assertSame("wizaplace", $user->getCompany());
    }

    public function testUpdateUserCompany(): void
    {
        // create new user
        $userId = $this->userService->register('user1151@test.com', static::VALID_PASSWORD, 'Jean', 'Paul');

        $this->client->authenticate('user1151@test.com', static::VALID_PASSWORD);
        $user = $this->userService->getProfileFromId($userId);
        static::assertSame('user1151@test.com', $user->getEmail());

        $this->userService->updateUser(
            (new UpdateUserCommand())
                ->setUserId($userId)
                ->setEmail('user1151@test.com')
                ->setFirstName('Paul')
                ->setLastName('Emploi')
                ->setBirthday(null)
                ->setIsProfessional(true)
                ->setCompany('wizaplace')
        );

        $this->client->authenticate('user1151@test.com', static::VALID_PASSWORD);

        $user = $this->userService->getProfileFromId($userId);
        static::assertSame('user1151@test.com', $user->getEmail());
        static::assertSame('wizaplace', $user->getCompany());
    }

    public function testPatchUserCompany(): void
    {
        // create new user
        $userId = $this->userService->register('user1178@test.com', static::VALID_PASSWORD, 'Jean', 'Paul');

        $this->client->authenticate('user1178@test.com', static::VALID_PASSWORD);
        $user = $this->userService->getProfileFromId($userId);
        static::assertSame('user1178@test.com', $user->getEmail());

        $this->userService->patchUser(
            (new UpdateUserCommand())
                ->setUserId($userId)
                ->setIsProfessional(true)
                ->setCompany('wizaplace')
        );

        $this->client->authenticate('user1178@test.com', static::VALID_PASSWORD);

        $user = $this->userService->getProfileFromId($userId);
        static::assertSame('user1178@test.com', $user->getEmail());
        static::assertSame('wizaplace', $user->getCompany());
    }

    public function testRegisterUserJobTitle(): void
    {
        $registerUserCommand = (new RegisterUserCommand())
            ->setEmail('user1201@test.com')
            ->setPassword(static::VALID_PASSWORD)
            ->setTitle(UserTitle::MR())
            ->setBirthday(new \DateTimeImmutable("2019-11-18"))
            ->setShipping(new UpdateUserAddressCommand())
            ->setBilling(new UpdateUserAddressCommand())
            ->setIsProfessional(true)
            ->setJobTitle("wizaplace")
        ;

        $userId = $this->userService->registerWithFullInfos($registerUserCommand);

        $this->client->authenticate("user1201@test.com", static::VALID_PASSWORD);

        $user = $this->userService->getProfileFromId($userId);

        static::assertSame("wizaplace", $user->getJobTitle());
    }

    public function testUpdateUserJobTitle(): void
    {
        // create new user
        $userId = $this->userService->register('user1223@test.com', static::VALID_PASSWORD, 'Jean', 'Paul');

        $this->client->authenticate('user1223@test.com', static::VALID_PASSWORD);
        $user = $this->userService->getProfileFromId($userId);
        static::assertSame('user1223@test.com', $user->getEmail());

        $this->userService->updateUser(
            (new UpdateUserCommand())
                ->setUserId($userId)
                ->setEmail('user1223@test.com')
                ->setFirstName('Paul')
                ->setLastName('Emploi')
                ->setBirthday(null)
                ->setIsProfessional(true)
                ->setJobTitle('wizaplace')
        );

        $this->client->authenticate('user1223@test.com', static::VALID_PASSWORD);

        $user = $this->userService->getProfileFromId($userId);
        static::assertSame('user1223@test.com', $user->getEmail());
        static::assertSame('wizaplace', $user->getJobTitle());
    }

    public function testPatchUserJobTitle(): void
    {
        // create new user
        $userId = $this->userService->register('user1250@test.com', static::VALID_PASSWORD, 'Jean', 'Paul');

        $this->client->authenticate('user1250@test.com', static::VALID_PASSWORD);
        $user = $this->userService->getProfileFromId($userId);
        static::assertSame('user1250@test.com', $user->getEmail());

        $this->userService->patchUser(
            (new UpdateUserCommand())
                ->setUserId($userId)
                ->setIsProfessional(true)
                ->setJobTitle('wizaplace')
        );

        $this->client->authenticate('user1250@test.com', static::VALID_PASSWORD);

        $user = $this->userService->getProfileFromId($userId);
        static::assertSame('user1250@test.com', $user->getEmail());
        static::assertSame('wizaplace', $user->getJobTitle());
    }

    public function testRegisterUserComment(): void
    {
        $registerUserCommand = (new RegisterUserCommand())
            ->setEmail('user1273@test.com')
            ->setPassword(static::VALID_PASSWORD)
            ->setTitle(UserTitle::MR())
            ->setBirthday(new \DateTimeImmutable("2019-11-18"))
            ->setShipping(new UpdateUserAddressCommand())
            ->setBilling(new UpdateUserAddressCommand())
            ->setIsProfessional(true)
            ->setComment("confirmed client")
        ;

        $userId = $this->userService->registerWithFullInfos($registerUserCommand);

        $this->client->authenticate("user1273@test.com", static::VALID_PASSWORD);

        $user = $this->userService->getProfileFromId($userId);

        static::assertSame("confirmed client", $user->getComment());
    }

    public function testUpdateUserComment(): void
    {
        // create new user
        $userId = $this->userService->register('user1295@test.com', static::VALID_PASSWORD, 'Jean', 'Paul');

        $this->client->authenticate('user1295@test.com', static::VALID_PASSWORD);
        $user = $this->userService->getProfileFromId($userId);
        static::assertSame('user1295@test.com', $user->getEmail());

        $this->userService->updateUser(
            (new UpdateUserCommand())
                ->setUserId($userId)
                ->setEmail('user1295@test.com')
                ->setFirstName('Paul')
                ->setLastName('Emploi')
                ->setBirthday(null)
                ->setIsProfessional(true)
                ->setComment('confirmed client')
        );

        $this->client->authenticate('user1295@test.com', static::VALID_PASSWORD);

        $user = $this->userService->getProfileFromId($userId);
        static::assertSame('user1295@test.com', $user->getEmail());
        static::assertSame('confirmed client', $user->getComment());
    }

    public function testPatchUserComment(): void
    {
        // create new user
        $userId = $this->userService->register('user1322@test.com', static::VALID_PASSWORD, 'Jean', 'Paul');

        $this->client->authenticate('user1322@test.com', static::VALID_PASSWORD);
        $user = $this->userService->getProfileFromId($userId);
        static::assertSame('user1322@test.com', $user->getEmail());

        $this->userService->patchUser(
            (new UpdateUserCommand())
                ->setUserId($userId)
                ->setIsProfessional(true)
                ->setComment('confirmed client')
        );

        $this->client->authenticate('user1322@test.com', static::VALID_PASSWORD);

        $user = $this->userService->getProfileFromId($userId);
        static::assertSame('user1322@test.com', $user->getEmail());
        static::assertSame('confirmed client', $user->getComment());
    }

    public function testRegisterUserLegalIdentifier(): void
    {
        $registerUserCommand = (new RegisterUserCommand())
            ->setEmail('user1345@test.com')
            ->setPassword(static::VALID_PASSWORD)
            ->setTitle(UserTitle::MR())
            ->setBirthday(new \DateTimeImmutable("2019-11-18"))
            ->setShipping(new UpdateUserAddressCommand())
            ->setBilling(new UpdateUserAddressCommand())
            ->setIsProfessional(true)
            ->setLegalIdentifier("wizaplace")
        ;

        $userId = $this->userService->registerWithFullInfos($registerUserCommand);

        $this->client->authenticate("user1345@test.com", static::VALID_PASSWORD);

        $user = $this->userService->getProfileFromId($userId);

        static::assertSame("wizaplace", $user->getLegalIdentifier());
    }

    public function testUpdateUserLegalIdentifier(): void
    {
        // create new user
        $userId = $this->userService->register('user1367@test.com', static::VALID_PASSWORD, 'Jean', 'Paul');

        $this->client->authenticate('user1367@test.com', static::VALID_PASSWORD);
        $user = $this->userService->getProfileFromId($userId);
        static::assertSame('user1367@test.com', $user->getEmail());

        $this->userService->updateUser(
            (new UpdateUserCommand())
                ->setUserId($userId)
                ->setEmail('user1367@test.com')
                ->setFirstName('Paul')
                ->setLastName('Emploi')
                ->setBirthday(null)
                ->setIsProfessional(true)
                ->setLegalIdentifier('wizaplace')
        );

        $this->client->authenticate('user1367@test.com', static::VALID_PASSWORD);

        $user = $this->userService->getProfileFromId($userId);
        static::assertSame('user1367@test.com', $user->getEmail());
        static::assertSame('wizaplace', $user->getLegalIdentifier());
    }

    public function testPatchUserLegalIdentifier(): void
    {
        // create new user
        $userId = $this->userService->register('user1394@test.com', static::VALID_PASSWORD, 'Jean', 'Paul');

        $this->client->authenticate('user1394@test.com', static::VALID_PASSWORD);
        $user = $this->userService->getProfileFromId($userId);
        static::assertSame('user1394@test.com', $user->getEmail());

        $this->userService->patchUser(
            (new UpdateUserCommand())
                ->setUserId($userId)
                ->setIsProfessional(true)
                ->setLegalIdentifier('wizaplace')
        );

        $this->client->authenticate('user1394@test.com', static::VALID_PASSWORD);

        $user = $this->userService->getProfileFromId($userId);
        static::assertSame('user1394@test.com', $user->getEmail());
        static::assertSame('wizaplace', $user->getLegalIdentifier());
    }

    public function testRegisterUserMinimal(): void
    {
        $registerUserCommand = (new RegisterUserCommand())
            ->setEmail($email = 'user1417@test.com')
            ->setPassword(static::VALID_PASSWORD)
            ->setShipping(new UpdateUserAddressCommand())
            ->setBilling(new UpdateUserAddressCommand())
        ;

        $userId = $this->userService->registerWithFullInfos($registerUserCommand);

        $this->client->authenticate($email, static::VALID_PASSWORD);

        $user = $this->userService->getProfileFromId($userId);

        static::assertSame("user1417@test.com", $user->getEmail());
    }

    public function testRegisterUserLoyaltyIdentifier(): void
    {
        $registerUserCommand = (new RegisterUserCommand())
            ->setEmail('user1435@test.com')
            ->setPassword(static::VALID_PASSWORD)
            ->setTitle(UserTitle::MR())
            ->setBirthday(new \DateTimeImmutable("2019-11-18"))
            ->setShipping(new UpdateUserAddressCommand())
            ->setBilling(new UpdateUserAddressCommand())
            ->setIsProfessional(true)
            ->setLoyaltyIdentifier("wizaplace")
        ;

        $userId = $this->userService->registerWithFullInfos($registerUserCommand);

        $this->client->authenticate("user1435@test.com", static::VALID_PASSWORD);

        $user = $this->userService->getProfileFromId($userId);

        static::assertSame("wizaplace", $user->getLoyaltyIdentifier());
    }

    public function testUpdateUserLoyaltyIdentifier(): void
    {
        // create new user
        $userId = $this->userService->register('user1457@test.com', static::VALID_PASSWORD, 'Jean', 'Paul');

        $this->client->authenticate('user1457@test.com', static::VALID_PASSWORD);
        $user = $this->userService->getProfileFromId($userId);
        static::assertSame('user1457@test.com', $user->getEmail());

        $this->userService->updateUser(
            (new UpdateUserCommand())
                ->setUserId($userId)
                ->setEmail('user1457@test.com')
                ->setFirstName('Paul')
                ->setLastName('Emploi')
                ->setBirthday(null)
                ->setIsProfessional(true)
                ->setLoyaltyIdentifier('wizaplace')
        );

        $this->client->authenticate('user1457@test.com', static::VALID_PASSWORD);

        $user = $this->userService->getProfileFromId($userId);
        static::assertSame('user1457@test.com', $user->getEmail());
        static::assertSame('wizaplace', $user->getLoyaltyIdentifier());
    }

    public function testPatchUserLoyaltyIdentifier(): void
    {
        // create new user
        $userId = $this->userService->register('user1484@test.com', static::VALID_PASSWORD, 'Jean', 'Paul');

        $this->client->authenticate('user1484@test.com', static::VALID_PASSWORD);
        $user = $this->userService->getProfileFromId($userId);
        static::assertSame('user1484@test.com', $user->getEmail());

        $this->userService->patchUser(
            (new UpdateUserCommand())
                ->setUserId($userId)
                ->setIsProfessional(true)
                ->setLoyaltyIdentifier('wizaplace')
        );

        $this->client->authenticate('user1484@test.com', static::VALID_PASSWORD);

        $user = $this->userService->getProfileFromId($userId);
        static::assertSame('user1484@test.com', $user->getEmail());
        static::assertSame('wizaplace', $user->getLoyaltyIdentifier());
    }

    public function testListSubscriptionsBy(): void
    {
        $userId = ($this->client->authenticate('user1507@wizaplace.com', static::VALID_PASSWORD))->getId();

        $subscriptions = $this->userService->listSubscriptionsBy($userId);

        static::assertInstanceOf(PaginatedData::class, $subscriptions);
        static::assertEquals(10, $subscriptions->getLimit());
        static::assertEquals(0, $subscriptions->getOffset());
        static::assertEquals(1, $subscriptions->getTotal());
        static::assertCount(1, $subscriptions->getItems());

        /** @var SubscriptionSummary $subscription */
        $subscription = $subscriptions->getItems()[0];

        static::assertInstanceOf(SubscriptionSummary::class, $subscription);
        static::assertUuid($subscription->getId());
        static::assertEquals($userId, $subscription->getUserId());
        static::assertEquals(3, $subscription->getCompanyId());
        static::assertUuid($subscription->getCardId());
    }

    /**
     * @dataProvider partialUserProvider
     */
    public function testRegisterUserPartially(RegisterUserCommand $registerUserCommand, array $expectedData): void
    {
        $apiClient = $this->buildApiClient();

        $userId = (new UserService($apiClient))->registerPartially($registerUserCommand);

        $apiClient->authenticate($expectedData['email'], static::VALID_PASSWORD);

        $user = (new UserService($apiClient))->getProfileFromId($userId);

        static::assertSame($expectedData['email'], $user->getEmail());
        static::assertSame($expectedData['firstName'], $user->getFirstname());
        static::assertSame($expectedData['lastName'], $user->getLastname());
        static::assertEquals($expectedData['title'], $user->getTitle());
        static::assertSame($expectedData['phone'], $user->getPhone());
        static::assertSame($expectedData['lang'], $user->getLanguage());
        static::assertEquals($expectedData['birthday'], $user->getBirthday());
        static::assertInstanceOf($expectedData['billing'], $user->getBillingAddress());
        static::assertInstanceOf($expectedData['shipping'], $user->getShippingAddress());
        static::assertSame($expectedData['externalIdentifier'], $user->getExternalIdentifier());
        static::assertSame($expectedData['isProfessional'], $user->isProfessional());
        static::assertSame($expectedData['intraEuropeanCommunityVAT'], $user->getIntraEuropeanCommunityVAT());
        static::assertSame($expectedData['company'], $user->getCompany());
        static::assertSame($expectedData['jobTitle'], $user->getJobTitle());
        static::assertSame($expectedData['comment'], $user->getComment());
        static::assertSame($expectedData['legalIdentifier'], $user->getLegalIdentifier());
        static::assertSame($expectedData['loyaltyIdentifier'], $user->getLoyaltyIdentifier());
    }

    public function partialUserProvider(): array
    {
        $birthday = (new \DateTimeImmutable("2019-11-18"));

        return [
            [
                (new RegisterUserCommand())
                    ->setEmail('partialUser@wizacha.com')
                    ->setPassword(static::VALID_PASSWORD)
                    ->setTitle(UserTitle::MR())
                    ->setBirthday($birthday),
                [
                    'email' => 'partialUser@wizacha.com',
                    'firstName' => '',
                    'lastName' => '',
                    'title' => UserTitle::MR(),
                    'phone' => '',
                    'lang' => 'fr',
                    'birthday' => $birthday,
                    'billing' => UserAddress::class,
                    'shipping' => UserAddress::class,
                    'externalIdentifier' => '',
                    'isProfessional' => false,
                    'intraEuropeanCommunityVAT' => '',
                    'company' => '',
                    'jobTitle' => '',
                    'comment' => '',
                    'legalIdentifier' => '',
                    'loyaltyIdentifier' => null,
                ],
            ],
            [
                (new RegisterUserCommand())
                    ->setEmail('partialUser2@wizacha.com')
                    ->setPassword(static::VALID_PASSWORD)
                    ->setFirstName('Paul')
                    ->setLastName('WIZA')
                    ->setComment('this is a comment'),
                [
                    'email' => 'partialUser2@wizacha.com',
                    'firstName' => 'Paul',
                    'lastName' => 'WIZA',
                    'title' => null,
                    'phone' => '',
                    'lang' => 'fr',
                    'birthday' => null,
                    'billing' => UserAddress::class,
                    'shipping' => UserAddress::class,
                    'externalIdentifier' => '',
                    'isProfessional' => false,
                    'intraEuropeanCommunityVAT' => '',
                    'company' => '',
                    'jobTitle' => '',
                    'comment' => 'this is a comment',
                    'legalIdentifier' => '',
                    'loyaltyIdentifier' => null,
                ],
            ],
        ];
    }

    public function testRegisterWithAddressesHavingLabelAndComment(): void
    {
        $userEmail = 'user@example.com';
        $userPassword = 'password';
        $userFirstname = 'John';
        $userLastname = 'Doe';
        $userBilling = new UserAddress(
            [
                'label'     => 'Label_b',
                'title'     => UserTitle::MR()->getValue(),
                'firstname' => $userFirstname,
                'lastname'  => $userLastname,
                'company'   => "Company_b",
                'phone'     => "Phone_b",
                'address'   => "Address_b",
                'address_2' => "Address 2_b",
                'zipcode'   => "Zipcode_b",
                'city'      => "City_b",
                'country'   => "FR",
                'comment'   => "Comment_b",
            ]
        );
        $userShipping = new UserAddress(
            [
                'label'     => 'Label_s',
                'title'     => UserTitle::MR()->getValue(),
                'firstname' => $userFirstname,
                'lastname'  => $userLastname,
                'company'   => "Company_s",
                'phone'     => "Phone_s",
                'address'   => "Address_s",
                'address_2' => "Address 2_s",
                'zipcode'   => "Zipcode_s",
                'city'      => "City_s",
                'country'   => "FR",
                'comment'   => "Comment_s",
            ]
        );

        $client = $this->buildApiClient();
        $userService = new UserService($client);

        // create new user
        $userId = $userService->register($userEmail, $userPassword, $userFirstname, $userLastname, $userBilling, $userShipping);

        // authenticate with newly created user
        $client->authenticate($userEmail, $userPassword);

        // fetch user
        $user = $userService->getProfileFromId($userId);

        static::assertSame($userBilling->getLabel(), $user->getBillingAddress()->getLabel());
        static::assertSame($userBilling->getComment(), $user->getBillingAddress()->getComment());
        static::assertSame($userShipping->getLabel(), $user->getShippingAddress()->getLabel());
        static::assertSame($userShipping->getComment(), $user->getShippingAddress()->getComment());
    }

    public function testGetProfileWithAddressesHavingLabelAndComment(): void
    {
        $userEmail = 'example1@example.com';
        $userPassword = 'password';
        $userFirstname = 'John';
        $userLastname = 'Doe';
        $userBilling = new UserAddress(
            [
                'label'     => 'Label_b',
                'title'     => UserTitle::MR()->getValue(),
                'firstname' => $userFirstname,
                'lastname'  => $userLastname,
                'company'   => "Company_b",
                'phone'     => "Phone_b",
                'address'   => "Address_b",
                'address_2' => "Address 2_b",
                'zipcode'   => "Zipcode_b",
                'city'      => "City_b",
                'country'   => "FR",
                'comment'   => "Comment_b",
            ]
        );
        $userShipping = new UserAddress(
            [
                'label'     => 'Label_s',
                'title'     => UserTitle::MR()->getValue(),
                'firstname' => $userFirstname,
                'lastname'  => $userLastname,
                'company'   => "Company_s",
                'phone'     => "Phone_s",
                'address'   => "Address_s",
                'address_2' => "Address 2_s",
                'zipcode'   => "Zipcode_s",
                'city'      => "City_s",
                'country'   => "FR",
                'comment'   => "Comment_s",
            ]
        );

        $client = $this->buildApiClient();
        $userService = new UserService($client);

        // create new user
        $userId = $userService->register($userEmail, $userPassword, $userFirstname, $userLastname, $userBilling, $userShipping);

        $client->authenticate($userEmail, $userPassword);

        // get user
        $user = $userService->getProfileFromId($userId);

        static::assertSame($userBilling->getLabel(), $user->getBillingAddress()->getLabel());
        static::assertSame($userBilling->getComment(), $user->getBillingAddress()->getComment());
        static::assertSame($userShipping->getLabel(), $user->getShippingAddress()->getLabel());
        static::assertSame($userShipping->getComment(), $user->getShippingAddress()->getComment());
    }

    public function testUpdateProfileDisplayingAddressesHavingAllFields(): void
    {
        $userEmail = 'user103@example.com';
        $userPassword = 'password';
        $userFirstName = 'John';
        $userLastName = 'Doe';
        $userPhone = '0100000000';

        $client = $this->buildApiClient();
        $userService = new UserService($client);

        $userId = $userService->register($userEmail, $userPassword, $userFirstName, $userLastName);

        $client->authenticate($userEmail, $userPassword);

        $userService->patchUser(
            (new UpdateUserCommand())
                ->setUserId($userId)
                ->setPhone($userPhone)
        );

        $user = $userService->getProfileFromId($userId);

        static::assertArrayHasKey('id', $user->getBillingAddress()->toArray());
        static::assertArrayHasKey('label', $user->getBillingAddress()->toArray());
        static::assertArrayHasKey('comment', $user->getBillingAddress()->toArray());
        static::assertArrayHasKey('id', $user->getBillingAddress()->toArray());
        static::assertArrayHasKey('label', $user->getBillingAddress()->toArray());
        static::assertArrayHasKey('comment', $user->getBillingAddress()->toArray());
    }

    public function testCreateUserWithNationalities(): void
    {
        $userEmail = 'usertest1129@example.com';
        $userPassword = 'password';
        $userFistname = 'John';
        $userLastname = 'Doe';
        $userBilling = new UserAddress(
            [
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
            ]
        );

        $nationality = new Nationality("FRA");
        $nationalities = [$nationality];

        $client = $this->buildApiClient();
        $client->authenticate('admin@wizaplace.com', 'Windows.98');
        $userService = new UserService($client);

        // create new user
        $userId = $userService->register($userEmail, $userPassword, $userFistname, $userLastname, $userBilling, $userBilling, $nationalities);

        // authenticate with newly created user
        $client->authenticate($userEmail, $userPassword);

        // fetch user
        $user = $userService->getProfileFromId($userId);
        static::assertSame(["FRA"], $user->getCodesA3FromNationalities());
    }

    public function testUpdateUserNationalities(): void
    {
        $client = $this->buildApiClient();
        $client->authenticate('admin@wizaplace.com', 'Windows.98');

        $userService = new UserService($client);

        // create new user
        $userId = $userService->register('user552@example.com', 'password', 'Jean', 'Paul');

        $user = $userService->getProfileFromId($userId);
        static::assertSame([], $user->getNationalities());

        $nationality = new Nationality("FRA");

        $userService->updateUser(
            (new UpdateUserCommand())
                ->setUserId($userId)
                ->setEmail('user552@example.com')
                ->setFirstName('Jacques')
                ->setLastName('Jules')
                ->setTitle(UserTitle::MR())
                ->setPhone('0102030405')
                ->setBirthday(\DateTime::createFromFormat('Y-m-d', '1963-02-17'))
                ->setLanguage('en')
                ->setCurrencyCode('EUR')
                ->addNationality($nationality)
        );

        $user = $userService->getProfileFromId($userId);
        static::assertSame(['FRA'], $user->getCodesA3FromNationalities());
    }

    public function testPatchUserNationalities(): void
    {
        $client = $this->buildApiClient();
        $client->authenticate('admin@wizaplace.com', 'Windows.98');

        $userService = new UserService($client);

        // create new user
        $userId = $userService->register('user562@example.com', 'password', 'Jean', 'Paul');

        $user = $userService->getProfileFromId($userId);
        static::assertSame([], $user->getNationalities());

        $nationality = new Nationality("FRA");

        $userService->patchUser(
            (new UpdateUserCommand())
                ->setUserId($userId)
                ->addNationality($nationality)
        );

        $user = $userService->getProfileFromId($userId);
        static::assertSame(["FRA"], $user->getCodesA3FromNationalities());
    }

    public function testAffiliateUserToACompany(): void
    {
        // create a user
        $userEmail = 'affiliatedUser@example.com';

        $userId = $this->userService->register($userEmail, 'password', 'John', 'Doe');

        // create a company
        $companyRegistration = new CompanyRegistration('company', 'companyToAffiliated@example.com');
        $companyService = $this->buildUserCompanyService('user@wizaplace.com', 'Windows.98');
        $company = $companyService->register($companyRegistration)->getCompany();

        // authenticate as admin
        $this->client->authenticate('admin@wizaplace.com', 'Windows.98');

        $this->userService->affiliateUser($userEmail, $company->getId());

        //get User
        $user = $this->userService->getProfileFromId($userId);

        static::assertSame($company->getId(), $user->getCompanyId());
        static::assertSame(UserType::VENDOR()->getValue(), $user->getType()->getValue());
    }

    public function testDisaffiliateUserFromCompany(): void
    {
        // create a users
        $userEmail1 = 'DisafUser1@example.com';
        $userEmail2 = 'DisafUser2@example.com';

        $userId1 = $this->userService->register($userEmail1, 'password', 'John', 'Doe');
        $userId2 = $this->userService->register($userEmail2, 'password', 'Sara', 'Apl');

        // create a company
        $companyRegistration = new CompanyRegistration('ACME10', 'companyToDisaff1@example.com');
        $companyService = $this->buildUserCompanyService('user@wizaplace.com', 'Windows.98');
        $company = $companyService->register($companyRegistration)->getCompany();

        // authenticate as admin
        $this->client->authenticate('admin@wizaplace.com', 'Windows.98');

        // affiliate users to company
        $this->userService->affiliateUser($userEmail1, $company->getId());
        $this->userService->affiliateUser($userEmail2, $company->getId());

        //disaffiliate user
        $this->userService->disaffiliateUser($userEmail1);

        //get User
        $user = $this->userService->getProfileFromId($userId1);

        static::assertNull($user->getCompanyId());
        static::assertSame(UserType::CLIENT()->getValue(), $user->getType()->getValue());
    }

    public function testGetApiKeyUpdatedAt(): void
    {
        $client = $this->buildApiClient();
        $apiKey = $client->authenticate('admin@wizaplace.com', 'Windows.98');
        $client->revokeUser();
        $this->userService = new UserService($client);

        $apiKey = $client->authenticate('admin@wizaplace.com', 'Windows.98');
        //get User
        $user = $this->userService->getProfileFromId($apiKey->getId());
        static::assertNotNull($user->getApiKeyUpdatedAt());
        static::assertInstanceOf(\DateTimeImmutable::class, $user->getApiKeyUpdatedAt());
    }

    private function buildUserCompanyService(
        string $email = 'customer-3@world-company.com',
        string $password = 'password-customer-3'
    ): CompanyService {
        $apiClient = $this->buildApiClient();
        $apiClient->authenticate($email, $password);

        return new CompanyService($apiClient);
    }

    public function testCreateUserWithExtraFields(): void
    {
        $addressCommand = $this->getAddressCommand();

        $userCommand = (new RegisterUserCommand())
            ->setEmail('user332@example.com')
            ->setPassword(static::VALID_PASSWORD)
            ->setFirstName('Jane')
            ->setLastName('Doe')
            ->setPhone('0102030405')
            ->setBirthday(\DateTime::createFromFormat('Y-m-d', '1998-07-12'))
            ->setTitle(UserTitle::MRS())
            ->setBilling($addressCommand)
            ->setShipping($addressCommand)
            ->setExtra([
                "fields" => "value",
                "fields2" => "value2",
                "fields3" => "value3",
            ]);

        // create new user
        $userId = $this->userService->registerWithFullInfos($userCommand);

        // authenticate with newly created user
        $this->client->authenticate($userCommand->getEmail(), $userCommand->getPassword());

        // fetch user
        $user = $this->userService->getProfileFromId($userId);

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
        static::assertSame($userCommand->getExtra(), $user->getExtra());
    }

    public function testUpdateProfilWithExtraFields(): void
    {
        $userEmail = 'user1010@example.com';
        $userPassword = 'password';
        $userFirstName = 'John';
        $userLastName = 'Doe';
        $userPhone = '0100000000';

        $client = $this->buildApiClient();
        $userService = new UserService($client);

        $userId = $userService->register($userEmail, $userPassword, $userFirstName, $userLastName);

        $client->authenticate($userEmail, $userPassword);

        $extra = [
            "fields" => "value",
            "fields2" => "value2",
        ];

        $userService->updateUser(
            (new UpdateUserCommand())
                ->setUserId($userId)
                ->setEmail($userEmail)
                ->setFirstName($userFirstName)
                ->setLastName($userLastName)
                ->setExtra($extra)
        );

        $user = $userService->getProfileFromId($userId);

        static::assertSame($extra, $user->getExtra());
    }

    public function testPatchProfilWithExtraFields(): void
    {
        $userEmail = 'user104@example.com';
        $userPassword = 'password';
        $userFirstName = 'John';
        $userLastName = 'Doe';
        $userPhone = '0100000000';

        $client = $this->buildApiClient();
        $userService = new UserService($client);

        $userId = $userService->register($userEmail, $userPassword, $userFirstName, $userLastName);

        $client->authenticate($userEmail, $userPassword);

        $extra = [
            "fields" => "value",
            "fields2" => "value2",
        ];

        $userService->patchUser(
            (new UpdateUserCommand())
                ->setUserId($userId)
                ->setExtra($extra)
        );

        $user = $userService->getProfileFromId($userId);

        static::assertSame($extra, $user->getExtra());
    }

    public function testRemoveExtraFields(): void
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
            ->setEmail('userExtraFields22@example.com')
            ->setPassword(static::VALID_PASSWORD)
            ->setFirstName('Jane')
            ->setLastName('Doe')
            ->setPhone('0102030405')
            ->setBirthday(\DateTime::createFromFormat('Y-m-d', '1998-07-12'))
            ->setTitle(UserTitle::MRS())
            ->setBilling($addressCommand)
            ->setShipping($addressCommand)
            ->setExtra([
                "fields" => "value",
                "fields2" => "value2",
                "fields3" => "value3",
            ]);

        // create new user
        $userId = $this->userService->registerWithFullInfos($userCommand);

        // authenticate with newly created user
        $this->client->authenticate($userCommand->getEmail(), $userCommand->getPassword());

        $user = $this->userService->getProfileFromId($userId);
        static::assertSame($userCommand->getExtra(), $user->getExtra());

        //removeExtraFields
        $this->userService->deleteExtraFields(
            $userId,
            ["fields2", "fields"]
        );

        $user = $this->userService->getProfileFromId($userId);
        static::assertSame(["fields3" => "value3"], $user->getExtra());
    }

    public function testUserRegisterWithLangEn(): void
    {
        $userEmail = 'user740@example.com';
        $userPassword = static::VALID_PASSWORD;
        $userLang = 'en';

        $userId = $this->userService->register(
            $userEmail,
            $userPassword,
            '',
            '',
            null,
            null,
            null,
            $userLang
        );

        $this->client->authenticate($userEmail, $userPassword);
        $user = $this->userService->getProfileFromId($userId);

        static::assertSame($userEmail, $user->getEmail());
        static::assertSame($userLang, $user->getLanguage());
    }

    public function testGetUsersPaginatedByFilters(): void
    {
        $this->client->authenticate('admin@wizaplace.com', 'Windows.98');

        $userFilters = new UserFilters(
            [
                'name' => 'a',
                'type' => [UserType::CLIENT()->getValue(), UserType::VENDOR()->getValue()],
                'elements' => 5,
            ]
        );

        $usersPaginatedResult = $this->userService->getUsersByFilters($userFilters);

        static::assertCount(5, $usersPaginatedResult->getUsers());
        static::assertSame(0, $usersPaginatedResult->getPagination()->getPage());
        static::assertSame(9, $usersPaginatedResult->getPagination()->getNbResults());
        static::assertSame(2, $usersPaginatedResult->getPagination()->getNbPages());
        static::assertSame(5, $usersPaginatedResult->getPagination()->getResultsPerPage());

        // Create new users with extra key/value
        $addressCommand = $this->getAddressCommand();

        $userCommand = (new RegisterUserCommand())
            ->setEmail('ABC@extra.com')
            ->setPassword(static::VALID_PASSWORD)
            ->setFirstName('Jane')
            ->setLastName('Doe')
            ->setPhone('0102030405')
            ->setBirthday(\DateTime::createFromFormat('Y-m-d', '1998-07-12'))
            ->setTitle(UserTitle::MRS())
            ->setBilling($addressCommand)
            ->setShipping($addressCommand)
            ->setExtra([
                "key 1" => "value 1",
                "key 2" => "foo",
            ]);

        $this->userService->registerWithFullInfos($userCommand);

        $userCommand = (new RegisterUserCommand())
            ->setEmail('DEF@extra.com')
            ->setPassword(static::VALID_PASSWORD)
            ->setFirstName('Jack')
            ->setLastName('Harris')
            ->setPhone('0102030405')
            ->setBirthday(\DateTime::createFromFormat('Y-m-d', '1998-07-12'))
            ->setTitle(UserTitle::MR())
            ->setBilling($addressCommand)
            ->setShipping($addressCommand)
            ->setExtra([
                "key 1" => "value 2",
                "key 2" => "bar",
            ]);

        $this->userService->registerWithFullInfos($userCommand);

        $userCommand = (new RegisterUserCommand())
            ->setEmail('GHI@extra.com')
            ->setPassword(static::VALID_PASSWORD)
            ->setFirstName('John')
            ->setLastName('Cooper')
            ->setPhone('0102030405')
            ->setBirthday(\DateTime::createFromFormat('Y-m-d', '1998-07-12'))
            ->setTitle(UserTitle::MR())
            ->setBilling($addressCommand)
            ->setShipping($addressCommand)
            ->setExtra([
                "key 1" => "foo",
                "key 2" => "bar",
            ]);

        $this->userService->registerWithFullInfos($userCommand);

        $userFilters = new UserFilters([
            'extra' => ['key 1' => ['value 1','foo']],
        ]);

        $usersPaginatedResult = $this->userService->getUsersByFilters($userFilters);

        static::assertCount(2, $usersPaginatedResult->getUsers());

        $userFilters = new UserFilters([
            'extraStartWith' => ['key 1' => 'value'],
        ]);

        $usersPaginatedResult = $this->userService->getUsersByFilters($userFilters);

        static::assertCount(2, $usersPaginatedResult->getUsers());

        $userFilters = new UserFilters([
            'extraStartWith' => ['key 1' => ['value','foo']],
        ]);

        $usersPaginatedResult = $this->userService->getUsersByFilters($userFilters);

        static::assertCount(3, $usersPaginatedResult->getUsers());
    }

    public function testGetUserProfileDisplayingPasswordExpiryTimeLeft(): void
    {
        $userId = ($this->client->authenticate('sabrine.naceur-ext+1322@wizacha.com', static::VALID_PASSWORD))->getId();

        $user = $this->userService->getProfileFromId($userId);

        static::assertSame(1, $user->getPasswordExpiryTimeLeft());
    }

    public function getAddressCommand(): UpdateUserAddressCommand
    {
        return (new UpdateUserAddressCommand())
            ->setTitle(UserTitle::MRS())
            ->setFirstName('Jane')
            ->setLastName('Doe')
            ->setPhone('0123456789')
            ->setAddress('24 rue de la gare')
            ->setCompany('Wizaplace')
            ->setZipCode('69009')
            ->setCity('Lyon')
            ->setCountry('France');
    }
}
