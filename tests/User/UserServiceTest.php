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
        $this->assertNull($user->getCompanyId());
        $this->assertFalse($user->isVendor());
        $this->assertSame(UserType::CLIENT()->getValue(), $user->getType()->getValue());

        // shipping address
        $this->assertNull($user->getShippingAddress()->getTitle());
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
        $this->assertNull($user->getBillingAddress()->getTitle());
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

    public function testCreateUserWithAddresses()
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
        $userId = $userService->register($userEmail, $userPassword, $userFistname, $userLastname, $userBilling, $userShipping);

        // authenticate with newly created user
        $client->authenticate($userEmail, $userPassword);

        // fetch user
        $user = $userService->getProfileFromId($userId);

        $this->assertNotNull($user, 'User exists');
        $this->assertSame($userEmail, $user->getEmail());
        $this->assertSame($userId, $user->getId());
        $this->assertSame(null, $user->getTitle());
        $this->assertSame($userFistname, $user->getFirstname());
        $this->assertSame($userLastname, $user->getLastname());
        $this->assertSame(null, $user->getBirthday());
        $this->assertNull($user->getCompanyId());
        $this->assertFalse($user->isVendor());
        $this->assertSame(UserType::CLIENT()->getValue(), $user->getType()->getValue());

        // shipping address
        $this->assertSame($userShipping->getTitle()->getValue(), $user->getShippingAddress()->getTitle()->getValue());
        $this->assertSame($userShipping->getFirstName(), $user->getShippingAddress()->getFirstName());
        $this->assertSame($userShipping->getLastName(), $user->getShippingAddress()->getLastName());
        $this->assertSame($userShipping->getCompany(), $user->getShippingAddress()->getCompany());
        $this->assertSame($userShipping->getPhone(), $user->getShippingAddress()->getPhone());
        $this->assertSame($userShipping->getAddress(), $user->getShippingAddress()->getAddress());
        $this->assertSame($userShipping->getAddressSecondLine(), $user->getShippingAddress()->getAddressSecondLine());
        $this->assertSame($userShipping->getZipCode(), $user->getShippingAddress()->getZipCode());
        $this->assertSame($userShipping->getCity(), $user->getShippingAddress()->getCity());
        $this->assertSame($userShipping->getCountry(), $user->getShippingAddress()->getCountry());

        // billing address
        $this->assertSame($userBilling->getTitle()->getValue(), $user->getBillingAddress()->getTitle()->getValue());
        $this->assertSame($userBilling->getFirstName(), $user->getBillingAddress()->getFirstName());
        $this->assertSame($userBilling->getLastName(), $user->getBillingAddress()->getLastName());
        $this->assertSame($userBilling->getCompany(), $user->getBillingAddress()->getCompany());
        $this->assertSame($userBilling->getPhone(), $user->getBillingAddress()->getPhone());
        $this->assertSame($userBilling->getAddress(), $user->getBillingAddress()->getAddress());
        $this->assertSame($userBilling->getAddressSecondLine(), $user->getBillingAddress()->getAddressSecondLine());
        $this->assertSame($userBilling->getZipCode(), $user->getBillingAddress()->getZipCode());
        $this->assertSame($userBilling->getCity(), $user->getBillingAddress()->getCity());
        $this->assertSame($userBilling->getCountry(), $user->getBillingAddress()->getCountry());
    }

    public function testCreateUserWithAnAddress()
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

    public function testCreateUserOnlyWithCompanyName()
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

        $this->assertNotNull($user, 'User exists');
        $this->assertSame($userEmail, $user->getEmail());
        $this->assertSame($userId, $user->getId());
        $this->assertSame(null, $user->getTitle());
        $this->assertSame($userFistname, $user->getFirstname());
        $this->assertSame($userLastname, $user->getLastname());
        $this->assertSame(null, $user->getBirthday());
        $this->assertNull($user->getCompanyId());
        $this->assertFalse($user->isVendor());
        $this->assertSame(UserType::CLIENT()->getValue(), $user->getType()->getValue());

        //shipping adress
        $this->assertSame($userShipping->getTitle()->getValue(), $user->getShippingAddress()->getTitle()->getValue());
        $this->assertSame($userShipping->getFirstName(), $user->getShippingAddress()->getFirstName());
        $this->assertSame($userShipping->getLastName(), $user->getShippingAddress()->getLastName());
        $this->assertSame($userShipping->getCompany(), $user->getShippingAddress()->getCompany());
        $this->assertSame($userShipping->getPhone(), $user->getShippingAddress()->getPhone());
        $this->assertSame($userShipping->getAddress(), $user->getShippingAddress()->getAddress());
        $this->assertSame($userShipping->getAddressSecondLine(), $user->getShippingAddress()->getAddressSecondLine());
        $this->assertSame($userShipping->getZipCode(), $user->getShippingAddress()->getZipCode());
        $this->assertSame($userShipping->getCity(), $user->getShippingAddress()->getCity());
        $this->assertSame('FR', $user->getShippingAddress()->getCountry());



        //billing adress
        $this->assertSame($userBilling->getTitle()->getValue(), $user->getBillingAddress()->getTitle()->getValue());
        $this->assertSame($userBilling->getFirstName(), $user->getBillingAddress()->getFirstName());
        $this->assertSame($userBilling->getLastName(), $user->getBillingAddress()->getLastName());
        $this->assertSame($userBilling->getCompany(), $user->getBillingAddress()->getCompany());
        $this->assertSame($userBilling->getPhone(), $user->getBillingAddress()->getPhone());
        $this->assertSame($userBilling->getAddress(), $user->getBillingAddress()->getAddress());
        $this->assertSame($userBilling->getAddressSecondLine(), $user->getBillingAddress()->getAddressSecondLine());
        $this->assertSame($userBilling->getZipCode(), $user->getBillingAddress()->getZipCode());
        $this->assertSame($userBilling->getCity(), $user->getBillingAddress()->getCity());
        $this->assertSame('FR', $user->getBillingAddress()->getCountry());
    }

    public function testCreateUserWithFullInfos()
    {
        $addressCommand = new UpdateUserAddressCommand();
        $addressCommand->setTitle(UserTitle::MRS());
        $addressCommand->setFirstName('Jane');
        $addressCommand->setLastName('Doe');
        $addressCommand->setPhone('0123456789');
        $addressCommand->setAddress('24 rue de la gare');
        $addressCommand->setCompany('Wizaplace');
        $addressCommand->setZipCode('69009');
        $addressCommand->setCity('Lyon');
        $addressCommand->setCountry('France');

        $userCommand = new RegisterUserCommand();
        $userCommand->setEmail('user@example.com');
        $userCommand->setPassword('password');
        $userCommand->setFirstName('Jane');
        $userCommand->setLastName('Doe');
        $userCommand->setBirthday(\DateTime::createFromFormat('Y-m-d', '1998-07-12'));
        $userCommand->setTitle(UserTitle::MRS());
        $userCommand->setBilling($addressCommand);
        $userCommand->setShipping($addressCommand);

        $client = $this->buildApiClient();
        $userService = new UserService($client);

        // create new user
        $userId = $userService->registerWithFullInfos($userCommand);

        // authenticate with newly created user
        $client->authenticate($userCommand->getEmail(), $userCommand->getPassword());

        // fetch user
        $user = $userService->getProfileFromId($userId);

        $this->assertNotNull($user, 'User exists');
        $this->assertSame($userCommand->getEmail(), $user->getEmail());
        $this->assertSame($userId, $user->getId());
        $this->assertTrue($userCommand->getTitle()->equals($user->getTitle()));
        $this->assertSame($userCommand->getFirstName(), $user->getFirstname());
        $this->assertSame($userCommand->getLastName(), $user->getLastname());
        $this->assertSame('1998-07-12', $user->getBirthday()->format('Y-m-d'));
        $this->assertNull($user->getCompanyId());
        $this->assertFalse($user->isVendor());
        $this->assertSame(UserType::CLIENT()->getValue(), $user->getType()->getValue());

        // shipping address
        $this->assertTrue($addressCommand->getTitle()->equals($user->getShippingAddress()->getTitle()));
        $this->assertSame($addressCommand->getFirstName(), $user->getShippingAddress()->getFirstName());
        $this->assertSame($addressCommand->getLastName(), $user->getShippingAddress()->getLastName());
        $this->assertSame($addressCommand->getCompany(), $user->getShippingAddress()->getCompany());
        $this->assertSame($addressCommand->getPhone(), $user->getShippingAddress()->getPhone());
        $this->assertSame($addressCommand->getAddress(), $user->getShippingAddress()->getAddress());
        $this->assertSame('', $user->getShippingAddress()->getAddressSecondLine());
        $this->assertSame($addressCommand->getZipCode(), $user->getShippingAddress()->getZipCode());
        $this->assertSame($addressCommand->getCity(), $user->getShippingAddress()->getCity());
        $this->assertSame('Fr', $user->getShippingAddress()->getCountry());

        // billing address
        $this->assertTrue($addressCommand->getTitle()->equals($user->getBillingAddress()->getTitle()));
        $this->assertSame($addressCommand->getFirstName(), $user->getBillingAddress()->getFirstName());
        $this->assertSame($addressCommand->getLastName(), $user->getBillingAddress()->getLastName());
        $this->assertSame($addressCommand->getCompany(), $user->getBillingAddress()->getCompany());
        $this->assertSame($addressCommand->getPhone(), $user->getBillingAddress()->getPhone());
        $this->assertSame($addressCommand->getAddress(), $user->getBillingAddress()->getAddress());
        $this->assertSame('', $user->getBillingAddress()->getAddressSecondLine());
        $this->assertSame($addressCommand->getZipCode(), $user->getBillingAddress()->getZipCode());
        $this->assertSame($addressCommand->getCity(), $user->getBillingAddress()->getCity());
        $this->assertSame('Fr', $user->getBillingAddress()->getCountry());
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
        $this->assertNull($user->getCompanyId());
        $this->assertFalse($user->isVendor());
        $this->assertSame(UserType::CLIENT()->getValue(), $user->getType()->getValue());

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
        $this->assertNull($user->getCompanyId());
        $this->assertFalse($user->isVendor());
        $this->assertSame(UserType::CLIENT()->getValue(), $user->getType()->getValue());
    }

    public function testUpdateUserAddresses()
    {
        $client = $this->buildApiClient();
        $userService = new UserService($client);

        // create new user
        $userId = $userService->register('user12@example.com', 'password', 'Jean', 'Paul');

        $client->authenticate('user12@example.com', 'password');
        $user = $userService->getProfileFromId($userId);
        $this->assertNull($user->getShippingAddress()->getTitle());
        $this->assertSame('', $user->getShippingAddress()->getFirstName());
        $this->assertSame('', $user->getShippingAddress()->getLastName());

        $this->assertNull($user->getBillingAddress()->getTitle());
        $this->assertSame('', $user->getBillingAddress()->getFirstName());
        $this->assertSame('', $user->getBillingAddress()->getLastName());


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

        $this->assertTrue(UserTitle::MR()->equals($user->getShippingAddress()->getTitle()));
        $this->assertSame('Pierre', $user->getShippingAddress()->getFirstName());
        $this->assertSame('Jacques', $user->getShippingAddress()->getLastName());
        $this->assertSame('FR', $user->getShippingAddress()->getCountry());
        $this->assertSame('Lyon', $user->getShippingAddress()->getCity());
        $this->assertSame('24 rue de la gare', $user->getShippingAddress()->getAddress());
        $this->assertSame('1er étage', $user->getShippingAddress()->getAddressSecondLine());
        $this->assertSame('Wizaplace', $user->getShippingAddress()->getCompany());
        $this->assertSame('0123456798', $user->getShippingAddress()->getPhone());
        $this->assertSame('69009', $user->getShippingAddress()->getZipCode());

        $this->assertTrue(UserTitle::MRS()->equals($user->getBillingAddress()->getTitle()));
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
        $this->assertNull($user->getShippingAddress()->getTitle());
        $this->assertSame('', $user->getShippingAddress()->getFirstName());
        $this->assertSame('', $user->getShippingAddress()->getLastName());

        $this->assertNull($user->getBillingAddress()->getTitle());
        $this->assertSame('', $user->getBillingAddress()->getFirstName());
        $this->assertSame('', $user->getBillingAddress()->getLastName());


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

        $this->assertTrue(UserTitle::MR()->equals($user->getShippingAddress()->getTitle()));
        $this->assertSame('', $user->getShippingAddress()->getFirstName());
        $this->assertSame('', $user->getShippingAddress()->getLastName());
        $this->assertSame('FR', $user->getShippingAddress()->getCountry());
        $this->assertSame('', $user->getShippingAddress()->getCity());
        $this->assertSame('', $user->getShippingAddress()->getAddress());
        $this->assertSame('', $user->getShippingAddress()->getAddressSecondLine());
        $this->assertSame('', $user->getShippingAddress()->getCompany());
        $this->assertSame('', $user->getShippingAddress()->getPhone());
        $this->assertSame('', $user->getShippingAddress()->getZipCode());

        $this->assertNull($user->getBillingAddress()->getTitle());
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

    public function testUpdateUserAddressesWithMissingFieldsWhenFullFieldsBefore()
    {
        $client = $this->buildApiClient();
        $userService = new UserService($client);

        // create new user
        $userId = $userService->register('user123@example.com', 'password', 'Paul', 'Jacques');

        $client->authenticate('user123@example.com', 'password');

        //test juste après création du compte
        $user = $userService->getProfileFromId($userId);
        $this->assertNull($user->getShippingAddress()->getTitle());
        $this->assertSame('Paul', $user->getShippingAddress()->getFirstName());
        $this->assertSame('Jacques', $user->getShippingAddress()->getLastName());

        $this->assertNull($user->getBillingAddress()->getTitle());
        $this->assertSame('Paul', $user->getBillingAddress()->getFirstName());
        $this->assertSame('Jacques', $user->getBillingAddress()->getLastName());

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

        $this->assertTrue(UserTitle::MR()->equals($user->getShippingAddress()->getTitle()));

        $this->assertSame('Paul', $user->getShippingAddress()->getFirstName());
        $this->assertSame('Jacques', $user->getShippingAddress()->getLastName());
        $this->assertSame('Universite de Cambridge', $user->getShippingAddress()->getCompany());
        $this->assertSame('49 rue des chemins', $user->getShippingAddress()->getAddress());
        $this->assertSame('9e étage', $user->getShippingAddress()->getAddressSecondLine());
        $this->assertSame('69009', $user->getShippingAddress()->getZipCode());


        $this->assertNull($user->getBillingAddress()->getTitle());

        $this->assertSame('Paul', $user->getBillingAddress()->getFirstName());
        $this->assertSame('Jacques', $user->getBillingAddress()->getLastName());
        $this->assertSame('Universite de Cambridge', $user->getShippingAddress()->getCompany());
        $this->assertSame('49 rue des chemins', $user->getBillingAddress()->getAddress());
        $this->assertSame('9e étage', $user->getBillingAddress()->getAddressSecondLine());
        $this->assertSame('69009', $user->getBillingAddress()->getZipCode());

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

        $this->assertTrue(UserTitle::MR()->equals($user->getShippingAddress()->getTitle()));
        $this->assertSame('Paul', $user->getShippingAddress()->getFirstName());
        $this->assertSame('Jacques', $user->getShippingAddress()->getLastName());
        $this->assertSame('', $user->getShippingAddress()->getAddress());
        $this->assertSame('', $user->getShippingAddress()->getAddressSecondLine());
        $this->assertSame('', $user->getShippingAddress()->getCompany());
        $this->assertSame('', $user->getShippingAddress()->getZipCode());

        $this->assertNull($user->getBillingAddress()->getTitle());
        $this->assertSame('Paul', $user->getBillingAddress()->getFirstName());
        $this->assertSame('Jacques', $user->getBillingAddress()->getLastName());
        $this->assertSame('', $user->getBillingAddress()->getAddress());
        $this->assertSame('', $user->getBillingAddress()->getAddressSecondLine());
        $this->assertSame('', $user->getBillingAddress()->getCompany());
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
        $this->assertSame(UserType::CLIENT()->getValue(), $user->getType()->getValue());

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
        $this->assertSame(UserType::CLIENT()->getValue(), $user->getType()->getValue());
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

    public function testRecoverPasswordWithCustomUrl()
    {
        $client = $this->buildApiClient();
        $userService = new UserService($client);

        $userEmail = 'user1237@example.com';
        $userPassword = 'password';
        $userService->register($userEmail, $userPassword);

        $this->assertNull($userService->recoverPassword($userEmail, new Uri('https://marketplace.example.com/recover-password=token')));
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

    public function testChangePasswordWithToken()
    {
        $client = $this->buildApiClient();
        $userService = new UserService($client);

        $cannotLogin = false;
        try {
            $client->authenticate('customer-4@world-company.com', 'newPassword');
        } catch (BadCredentials $e) {
            $cannotLogin = true;
        }
        $this->assertTrue($cannotLogin);

        $userService->changePasswordWithRecoveryToken(md5('fake_secret_token'), 'newPassword');

        $apiKey = $client->authenticate('customer-4@world-company.com', 'newPassword');
        $this->assertInstanceOf(ApiKey::class, $apiKey);
    }

    public function testGetUserCompany()
    {
        $apiClient = $this->buildApiClient();

        $userId = ($apiClient->authenticate('vendor@world-company.com', 'password-vendor'))->getId();

        $user = (new UserService($apiClient))->getProfileFromId($userId);
        $companyId = $user->getCompanyId();

        $this->assertInternalType('int', $companyId);
        $this->assertGreaterThan(0, $companyId);
        $this->assertTrue($user->isVendor());
    }
}
