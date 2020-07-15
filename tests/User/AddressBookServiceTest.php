<?php

/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Tests\User;

use Wizaplace\SDK\User\AddressBook;
use Wizaplace\SDK\PaginatedData;
use Wizaplace\SDK\User\AddressType;
use Wizaplace\SDK\User\UpdateAddressBookCommand;
use Wizaplace\SDK\User\UserAddress;
use Wizaplace\SDK\User\UserService;
use Wizaplace\SDK\User\AddressBookService;
use Wizaplace\SDK\Tests\ApiTestCase;
use Wizaplace\SDK\User\UserTitle;

/**
 * @see AddressBookService
 */
class AddressBookServiceTest extends ApiTestCase
{
    public function testCreateAddressInAddressBook(): void
    {
        $userEmail = 'AddressBookTest51@wizaplace.com';
        $userPassword = 'password';

        $client = $this->buildApiClient();

        $userService = new UserService($client);

        $userId = $userService->register($userEmail, $userPassword);

        // authenticate with newly created user
        $client->authenticate($userEmail, $userPassword);

        $addressBookService = new AddressBookService($client);

        // create new address
        $adddressdata = [
            'label' => 'Domicile',
            'firstname' => 'firstname',
            'lastname' => 'lastname',
            'title' => 'mr',
            'company' => 'ACME',
            'phone' => '20000',
            'address' => '40 rue Laure Diebold',
            'address_2' => '3ème étage',
            'city' => 'Lyon',
            'zipcode' => '69009',
            'country' => 'FR',
            'division_code' => 'FR-03'
        ];

        $addressBookService->createAddressInAddressBook($userId, $adddressdata);
        $paginatedData = $addressBookService->listAddressBook($userId);

        static::assertInstanceOf(PaginatedData::class, $paginatedData);
        static::assertEquals(20, $paginatedData->getLimit());
        static::assertEquals(0, $paginatedData->getOffset());
        static::assertEquals(1, $paginatedData->getTotal());
        static::assertCount($paginatedData->getTotal(), $paginatedData->getItems());

        $addressBook = $paginatedData->getItems()[0];

        static::assertInstanceOf(AddressBook::class, $addressBook);
        static::assertUuid($addressBook->getId());
        static::assertSame($addressBook->getLabel(), 'Domicile');
        static::assertSame($addressBook->getFirstName(), 'firstname');
        static::assertSame($addressBook->getLastName(), 'lastname');
        static::assertSame($addressBook->getCompany(), 'ACME');
        static::assertSame($addressBook->getPhone(), '20000');
        static::assertSame($addressBook->getAddress(), '40 rue Laure Diebold');
        static::assertSame($addressBook->getAddressSecondLine(), '3ème étage');
        static::assertSame($addressBook->getCity(), 'Lyon');
        static::assertSame($addressBook->getCountry(), 'FR');
        static::assertSame($addressBook->getDivisionCode(), 'FR-03');
    }

    public function testRemoveAddressBook(): void
    {
        $userEmail = 'AddressBookTest7@wizaplace.com';
        $userPassword = 'password';

        $client = $this->buildApiClient();

        $userService = new UserService($client);

        $userId = $userService->register($userEmail, $userPassword);

        // authenticate with newly created user
        $client->authenticate($userEmail, $userPassword);

        $addressBookService = new AddressBookService($client);

        // create address from billing address in profile user
        $addressBookService->createAddressInAddressBook($userId, ["fromUserProfile" => "billing"]);

        // create address from shipping address in profile user
        $addressBookService->createAddressInAddressBook($userId, ["fromUserProfile" => "shipping"]);

        // create new address
        $adddressdata = [
            'label' => 'Domicile',
            'firstname' => 'firstname',
            'lastname' => 'lastname',
            'title' => 'mr',
            'company' => 'ACME',
            'phone' => '20000',
            'address' => '40 rue Laure Diebold',
            'address_2' => '3ème étage',
            'city' => 'Lyon',
            'zipcode' => '69009',
            'country' => 'FR',
            'division_code' => 'FR-03'
        ];

        $addressBookService->createAddressInAddressBook($userId, $adddressdata);

        $paginatedData = $addressBookService->listAddressBook($userId);

        static::assertInstanceOf(PaginatedData::class, $paginatedData);
        static::assertEquals(20, $paginatedData->getLimit());
        static::assertEquals(0, $paginatedData->getOffset());
        static::assertEquals(3, $paginatedData->getTotal());
        static::assertCount($paginatedData->getTotal(), $paginatedData->getItems());


        $addressBook = $paginatedData->getItems()[0];

        static::assertInstanceOf(AddressBook::class, $addressBook);
        static::assertUuid($addressBook->getId());
        static::assertSame($addressBook->getLabel(), 'Domicile');
        static::assertSame($addressBook->getFirstName(), 'firstname');
        static::assertSame($addressBook->getLastName(), 'lastname');
        static::assertTrue(UserTitle::MR()->equals($addressBook->getTitle()));
        static::assertSame($addressBook->getCompany(), 'ACME');
        static::assertSame($addressBook->getPhone(), '20000');
        static::assertSame($addressBook->getAddress(), '40 rue Laure Diebold');
        static::assertSame($addressBook->getAddressSecondLine(), '3ème étage');
        static::assertSame($addressBook->getCity(), 'Lyon');
        static::assertSame($addressBook->getCountry(), 'FR');
        static::assertSame($addressBook->getDivisionCode(), 'FR-03');
    }

    public function testReplaceAddressBook(): void
    {
        $userEmail = 'replaceAddress82@wizaplace.com';
        $userPassword = 'password';

        $client = $this->buildApiClient();

        $userService = new UserService($client);

        $userId = $userService->register($userEmail, $userPassword);

        $client->authenticate($userEmail, $userPassword);

        $addressBookService = new AddressBookService($client);

        // create new address
        $adddressdata = [
            'label' => 'Domicile',
            'firstname' => 'firstname',
            'lastname' => 'lastname',
            'title' => 'mr',
            'company' => 'ACME',
            'phone' => '20000',
            'address' => '40 rue Laure Diebold',
            'address_2' => '3ème étage',
            'city' => 'Lyon',
            'zipcode' => '69009',
            'country' => 'FR',
            'division_code' => 'FR-03'
        ];

        $addressId = $addressBookService->createAddressInAddressBook($userId, $adddressdata);

        $addressBookService->replaceAddressInAddressBook(
            $userId,
            $addressId,
            (new UpdateAddressBookCommand())
                ->setLabel('My address')
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
                ->setComment('My comment')
        );

        $paginatedData = $addressBookService->listAddressBook($userId);
        static::assertInstanceOf(PaginatedData::class, $paginatedData);
        static::assertEquals(1, $paginatedData->getTotal());
        static::assertCount(1, $paginatedData->getItems());

        $addressBook = $paginatedData->getItems()[0];
        static::assertTrue(UserTitle::MR()->equals($addressBook->getTitle()));
        static::assertSame('Pierre', $addressBook->getFirstname());
        static::assertSame('Jacques', $addressBook->getLastname());
        static::assertSame('0123456798', $addressBook->getPhone());
        static::assertSame('FR', $addressBook->getCountry());
        static::assertSame('Lyon', $addressBook->getCity());
        static::assertSame('24 rue de la gare', $addressBook->getAddress());
        static::assertSame('1er étage', $addressBook->getAddressSecondLine());
        static::assertSame('Wizaplace', $addressBook->getCompany());
        static::assertSame('69009', $addressBook->getZipCode());
        static::assertSame('My comment', $addressBook->getComment());
    }

    public function testCopyBillingAddressInAddressBook(): void
    {
        $userEmail = 'userCopyBillingAddress3@example.com';
        $userPassword = 'password';
        $userFistname = 'John';
        $userLastname = 'Doe';
        $userBilling = new UserAddress(
            [
                'label'         => 'Domicile_b',
                'title'         => UserTitle::MR()->getValue(),
                'firstname'     => $userFistname,
                'lastname'      => $userLastname,
                'company'       => "Company_b",
                'phone'         => "Phone_b",
                'address'       => "Address_b",
                'address_2'     => "Address 2_b",
                'zipcode'       => "Zipcode_b",
                'city'          => "City_b",
                'country'       => "FR",
                'division_code' => "FR-69",
                'comment'       => "comment_b",
            ]
        );

        $userShipping = new UserAddress(
            [
                'label'         => 'Domicile_s',
                'title'         => UserTitle::MR()->getValue(),
                'firstname'     => $userFistname,
                'lastname'      => $userLastname,
                'company'       => "Company_s",
                'phone'         => "Phone_s",
                'address'       => "Address_s",
                'address_2'     => "Address 2_s",
                'zipcode'       => "Zipcode_s",
                'city'          => "City_s",
                'country'       => "FR",
                'division_code' => "FR-69",
                'comment'       => "comment_s",
            ]
        );

        $client = $this->buildApiClient();

        $userService = new UserService($client);

        $userId = $userService->register($userEmail, $userPassword, $userFistname, $userLastname, $userBilling, $userShipping);

        $client->authenticate($userEmail, $userPassword);

        $addressBookService = new AddressBookService($client);

        $addressBookService->copyAddressInAddressBook($userId, AddressType::BILLING());
        $paginatedData = $addressBookService->listAddressBook($userId);

        $addressBook = $paginatedData->getItems()[0];

        static::assertSame('Domicile_b', $addressBook->getLabel());
        static::assertSame(UserTitle::MR()->getValue(), $addressBook->getTitle()->getValue());
        static::assertSame($userFistname, $addressBook->getFirstName());
        static::assertSame($userLastname, $addressBook->getLastName());
        static::assertSame('Company_b', $addressBook->getCompany());
        static::assertSame('Phone_b', $addressBook->getPhone());
        static::assertSame('Address_b', $addressBook->getAddress());
        static::assertSame('Address 2_b', $addressBook->getAddressSecondLine());
        static::assertSame('Zipcode_b', $addressBook->getZipCode());
        static::assertSame('City_b', $addressBook->getCity());
        static::assertSame('FR', $addressBook->getCountry());
        static::assertSame('comment_b', $addressBook->getComment());
    }

    public function testCopyShippingAddressInAddressBook(): void
    {
        $userEmail = 'user23@example.com';
        $userPassword = 'password';
        $userFirstname = 'John';
        $userLastname = 'Doe';
        $userBilling = new UserAddress(
            [
                'label'         => 'Domicile_b',
                'title'         => UserTitle::MR()->getValue(),
                'firstname'     => $userFirstname,
                'lastname'      => $userLastname,
                'company'       => "Company_b",
                'phone'         => "Phone_b",
                'address'       => "Address_b",
                'address_2'     => "Address 2_b",
                'zipcode'       => "Zipcode_b",
                'city'          => "City_b",
                'country'       => "FR",
                'division_code' => "FR-69",
                'comment'       => "comment_b",
            ]
        );

        $userShipping = new UserAddress(
            [
                'label'         => 'Domicile_s',
                'title'         => UserTitle::MR()->getValue(),
                'firstname'     => $userFirstname,
                'lastname'      => $userLastname,
                'company'       => "Company_s",
                'phone'         => "Phone_s",
                'address'       => "Address_s",
                'address_2'     => "Address 2_s",
                'zipcode'       => "Zipcode_s",
                'city'          => "City_s",
                'country'       => "FR",
                'division_code' => "FR-69",
                'comment'       => "comment_s",
            ]
        );

        $client = $this->buildApiClient();

        $userService = new UserService($client);

        $userId = $userService->register($userEmail, $userPassword, $userFirstname, $userLastname, $userBilling, $userShipping);

        $client->authenticate($userEmail, $userPassword);

        $addressBookService = new AddressBookService($client);

        $addressBookService->copyAddressInAddressBook($userId, AddressType::SHIPPING());
        $paginatedData = $addressBookService->listAddressBook($userId);

        $addressBook = $paginatedData->getItems()[0];

        static::assertSame('Domicile_s', $addressBook->getLabel());
        static::assertSame(UserTitle::MR()->getValue(), $addressBook->getTitle()->getValue());
        static::assertSame($userFirstname, $addressBook->getFirstName());
        static::assertSame($userLastname, $addressBook->getLastName());
        static::assertSame('Company_s', $addressBook->getCompany());
        static::assertSame('Phone_s', $addressBook->getPhone());
        static::assertSame('Address_s', $addressBook->getAddress());
        static::assertSame('Address 2_s', $addressBook->getAddressSecondLine());
        static::assertSame('Zipcode_s', $addressBook->getZipCode());
        static::assertSame('City_s', $addressBook->getCity());
        static::assertSame('FR', $addressBook->getCountry());
        static::assertSame('comment_s', $addressBook->getComment());
    }
}
