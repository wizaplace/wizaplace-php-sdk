<?php

/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Tests\Organisation;

use GuzzleHttp\Exception\ClientException;
use Symfony\Component\HttpFoundation\File\File;
use Wizaplace\SDK\Authentication\BadCredentials;
use Wizaplace\SDK\Basket\BasketService;
use Wizaplace\SDK\Catalog\DeclinationId;
use Wizaplace\SDK\Exception\AccessDenied;
use Wizaplace\SDK\Exception\NotFound;
use Wizaplace\SDK\Exception\UserDoesntBelongToOrganisation;
use Wizaplace\SDK\Order\Order;
use Wizaplace\SDK\Order\OrderAttachmentType;
use Wizaplace\SDK\Order\OrderService;
use Wizaplace\SDK\Organisation\Organisation;
use Wizaplace\SDK\Organisation\OrganisationAddress;
use Wizaplace\SDK\Organisation\OrganisationBasket;
use Wizaplace\SDK\Organisation\OrganisationFile;
use Wizaplace\SDK\Organisation\OrganisationGroup;
use Wizaplace\SDK\Organisation\OrganisationOrderAttachmentService;
use Wizaplace\SDK\Organisation\OrganisationService;
use Wizaplace\SDK\Tests\ApiTestCase;
use Wizaplace\SDK\Tests\File\Mock;
use Wizaplace\SDK\User\User;
use Wizaplace\SDK\User\UserService;
use Wizaplace\SDK\User\UserTitle;
use Wizaplace\SDK\Vendor\Order\OrderSummary;

final class OrganisationServiceTest extends ApiTestCase
{
    public function testRegisterWithUnauthenticatedUser(): void
    {
        $organisationService = $this->buildOrganisationService('', '', false);

        $data = $this->getDataForRegistration();

        $organisation = new Organisation($data);

        $organisation->addUploadedFile('identityCard', $this->mockUploadedFile('minimal.pdf'));
        $organisation->addUploadedFile('proofOfAppointment', $this->mockUploadedFile('minimal.pdf'));

        $responseData = $organisationService->register($organisation);

        $organisationId = $responseData['id'];

        $organisationService = $this->buildOrganisationService('admin@wizaplace.com', 'password');
        $organisation = $organisationService->get($organisationId);

        $this->assertSame("Galactic Empire", $organisation->getName());
        $this->assertSame("Death Star", $organisation->getBusinessUnitName());
        $this->assertSame("DS", $organisation->getBusinessUnitCode());
        $this->assertSame("DS1122334455", $organisation->getLegalInformationVatNumber());
        $this->assertSame("DS9988776655", $organisation->getLegalInformationSiret());
        $this->assertSame("L'Empire", $organisation->getLegalInformationBusinessName());
        $this->assertSame("La Cantina", $organisation->getAddress()->getAddress());
        $this->assertSame("pending", $organisation->getStatus());
    }

    public function testRegisterWithAdminUser(): void
    {
        $organisationService = $this->buildOrganisationService('admin@wizaplace.com', 'password');

        $data = $this->getDataForRegistration();

        $organisation = new Organisation($data);

        $organisation->addUploadedFile('identityCard', $this->mockUploadedFile('minimal.pdf'));
        $organisation->addUploadedFile('proofOfAppointment', $this->mockUploadedFile('minimal.pdf'));

        $responseData = $organisationService->register($organisation);

        $organisationId = $responseData['id'];

        $organisationService = $this->buildOrganisationService('admin@wizaplace.com', 'password');
        $organisation = $organisationService->get($organisationId);

        $this->assertSame("Galactic Empire", $organisation->getName());
        $this->assertSame("Death Star", $organisation->getBusinessUnitName());
        $this->assertSame("DS", $organisation->getBusinessUnitCode());
        $this->assertSame("DS1122334455", $organisation->getLegalInformationVatNumber());
        $this->assertSame("DS9988776655", $organisation->getLegalInformationSiret());
        $this->assertSame("L'Empire", $organisation->getLegalInformationBusinessName());
        $this->assertSame("La Cantina", $organisation->getAddress()->getAddress());
        $this->assertSame("pending", $organisation->getStatus());
    }

    public function testCannotRegisterIfAlreadyLogged(): void
    {
        $organisationService = $this->buildOrganisationService('user+orga@usc.com', 'password');

        $data = $this->getDataForRegistration();

        $organisation = new Organisation($data);

        $organisation->addUploadedFile('identityCard', $this->mockUploadedFile('minimal.pdf'));
        $organisation->addUploadedFile('proofOfAppointment', $this->mockUploadedFile('minimal.pdf'));

        $this->expectExceptionCode(403);
        $organisationService->register($organisation);
    }

    public function testGetOrganisation(): void
    {
        $organisationService = $this->buildOrganisationService('admin@wizaplace.com', 'password');

        $organisationId = $this->getOrganisationId();

        if (\is_string($organisationId)) {
            $organisation = $organisationService->get($organisationId);

            $this->assertSame($organisationId, $organisation->getId());
            $this->assertSame('University of New York', $organisation->getName());
            $this->assertSame('University of New York', $organisation->getLegalInformationBusinessName());
            $this->assertSame('44229377500031', $organisation->getLegalInformationSiret());
            $this->assertSame('FR99999999999', $organisation->getLegalInformationVatNumber());
            $this->assertSame('NTW', $organisation->getBusinessUnitCode());
            $this->assertSame('Network Infrastructure', $organisation->getBusinessUnitName());
            $this->assertSame("194 Lindale Avenue", $organisation->getAddress()->getAddress());
            $this->assertSame("", $organisation->getAddress()->getAdditionalAddress());
            $this->assertSame("94801", $organisation->getAddress()->getZipCode());
            $this->assertSame("Richmond", $organisation->getAddress()->getCity());
            $this->assertSame("", $organisation->getAddress()->getState());
            $this->assertSame("US", $organisation->getAddress()->getCountry());
            $this->assertSame("4917 Snyder Avenue", $organisation->getShippingAddress()->getAddress());
            $this->assertSame("", $organisation->getShippingAddress()->getAdditionalAddress());
            $this->assertSame("28209", $organisation->getShippingAddress()->getZipCode());
            $this->assertSame("North Carolina", $organisation->getShippingAddress()->getCity());
            $this->assertSame("", $organisation->getShippingAddress()->getState());
            $this->assertSame("US", $organisation->getShippingAddress()->getCountry());
            $this->assertSame('pending', $organisation->getStatus());
        }
    }

    public function testCannotGetOrganisationIfNotOwned(): void
    {
        $organisationId = $this->getOrganisationId();

        if (\is_string($organisationId)) {
            $organisationService = $this->buildOrganisationService();

            $this->expectExceptionCode(404);
            $organisationService->get($organisationId);
        }
    }

    public function testGetList(): void
    {
        $organisationService = $this->buildOrganisationService('admin@wizaplace.com', 'password');

        //get the list of all organisation, to have their ID
        $listOrga = $organisationService->getList();

        $this->assertNotEmpty($listOrga);
        $this->assertCount(3, $listOrga);
        $this->assertArrayHasKey('total', $listOrga);
        $this->assertArrayHasKey('count', $listOrga);
        $this->assertArrayHasKey('_embedded', $listOrga);
        $this->assertArrayHasKey('organisations', $listOrga['_embedded']);
        $this->assertSame(2, $listOrga['count']);
        $this->assertSame(2, $listOrga['total']);
        $this->assertSame('University of New York', $listOrga['_embedded']['organisations'][0]['name']);
        $this->assertSame('University of Southern California', $listOrga['_embedded']['organisations'][1]['name']);
    }

    public function testCannotGetListIfNotOwned(): void
    {
        $organisationService = $this->buildOrganisationService();

        $this->expectException(BadCredentials::class);
        $organisationService->getList();
    }

    public function testGetListUsers(): void
    {
        $organisationService = $this->buildOrganisationService('admin@wizaplace.com', 'password');

        $organisationId = $this->getOrganisationId();

        if (\is_string($organisationId)) {
            $listUsers = $organisationService->getListUsers($organisationId);

            $this->assertNotEmpty($listUsers);
            $this->assertCount(3, $listUsers);
            $this->assertArrayHasKey('total', $listUsers);
            $this->assertArrayHasKey('count', $listUsers);
            $this->assertArrayHasKey('_embedded', $listUsers);
            $this->assertSame(1, $listUsers['count']);
            $this->assertSame(1, $listUsers['total']);
        }
    }

    public function testCannotGetListUsersIfNotOwned(): void
    {
        $organisationService = $this->buildOrganisationService();

        $organisationId = $this->getOrganisationId();

        if (\is_string($organisationId)) {
            $this->expectException(UserDoesntBelongToOrganisation::class);
            $organisationService->getListUsers($organisationId);
        }
    }

    public function testOrganisationAddressesUpdate(): void
    {
        $organisationService = $this->buildOrganisationService('admin@wizaplace.com', 'password');

        $organisationId = $this->getOrganisationId();

        if (\is_string($organisationId)) {
            $address = [
                'address' => '10 rue de la gare',
                'additionalAddress' => 'Lieu dit: au dessus de LDLC',
                'zipCode' => '69009',
                'city' => 'Lyon',
                'state' => 'Rhone',
                'country' => 'France',
            ];

            $address = new OrganisationAddress($address);

            $shippingAddress = [
                'address' => '3 rue de la République',
                'additionalAddress' => '',
                'zipCode' => '75002',
                'city' => 'Paris',
                'state' => 'Paris',
                'country' => 'France',
            ];

            $shippingAddress = new OrganisationAddress($shippingAddress);

            $organisationService->updateOrganisationAddresses($organisationId, $address, $shippingAddress);

            $organisation = $organisationService->get($organisationId);

            $this->assertSame('10 rue de la gare', $organisation->getAddress()->getAddress());
            $this->assertSame('Lieu dit: au dessus de LDLC', $organisation->getAddress()->getAdditionalAddress());
            $this->assertSame('69009', $organisation->getAddress()->getZipCode());
            $this->assertSame('Lyon', $organisation->getAddress()->getCity());
            $this->assertSame('Rhone', $organisation->getAddress()->getState());
            $this->assertSame('France', $organisation->getAddress()->getCountry());

            $this->assertSame('3 rue de la République', $organisation->getShippingAddress()->getAddress());
            $this->assertSame('', $organisation->getShippingAddress()->getAdditionalAddress());
            $this->assertSame('75002', $organisation->getShippingAddress()->getZipCode());
            $this->assertSame('Paris', $organisation->getShippingAddress()->getCity());
            $this->assertSame('Paris', $organisation->getShippingAddress()->getState());
            $this->assertSame('France', $organisation->getShippingAddress()->getCountry());
        }
    }

    public function testCannotUpdateOrganisationAddresses(): void
    {
        $organisationService = $this->buildOrganisationService('admin@wizaplace.com', 'password');

        $organisationId = $this->getOrganisationId();

        if (\is_string($organisationId)) {
            $address = [
                'address' => '10 rue de la gare',
                'additionalAddress' => 'Lieu dit: au dessus de LDLC',
                'zipCode' => '69009',
                'city' => 'Lyon',
                'state' => 'Rhone',
                'country' => 'France',
            ];

            $address = new OrganisationAddress($address);

            $shippingAddress = [
                'address' => '3 rue de la République',
                'additionalAddress' => '',
                'zipCode' => '75002',
                'city' => 'Paris',
                'state' => 'Paris',
                'country' => 'France',
            ];

            $shippingAddress = new OrganisationAddress($shippingAddress);

            $organisationService = $this->buildOrganisationService();

            $this->expectException(UserDoesntBelongToOrganisation::class);
            $organisationService->updateOrganisationAddresses($organisationId, $address, $shippingAddress);
        }
    }

    public function testOrganisationUpdate(): void
    {
        $organisationService = $this->buildOrganisationService('admin@wizaplace.com', 'password');

        $organisationId = $this->getOrganisationId();

        if (\is_string($organisationId)) {
            $organisation = $organisationService->get($organisationId);

            $organisation->setName("New name");
            $organisation->setBusinessUnitName("New Business Unit Name");
            $organisation->setBusinessUnitCode("NEWCODE");
            $organisation->setLegalInformationBusinessName("New Business Name");
            $organisation->setLegalInformationSiret("NEWSIRET");
            $organisation->setLegalInformationVatNumber("NEWVATNUMBER");

            $organisationService->updateOrganisation($organisationId, $organisation);

            $organisation = $organisationService->get($organisationId);

            $this->assertSame("New name", $organisation->getName());
            $this->assertSame("New Business Unit Name", $organisation->getBusinessUnitName());
            $this->assertSame("NEWCODE", $organisation->getBusinessUnitCode());
            $this->assertSame("New Business Name", $organisation->getLegalInformationBusinessName());
            $this->assertSame("NEWSIRET", $organisation->getLegalInformationSiret());
            $this->assertSame("NEWVATNUMBER", $organisation->getLegalInformationVatNumber());
        }
    }

    public function testCannotUpdateOrganisationIfNotOwned(): void
    {
        $organisationService = $this->buildOrganisationService('admin@wizaplace.com', 'password');

        $organisationId = $this->getOrganisationId();

        if (\is_string($organisationId)) {
            $organisation = $organisationService->get($organisationId);

            $organisation->setName("New name");
            $organisation->setBusinessUnitName("New Business Unit Name");
            $organisation->setBusinessUnitCode("NEWCODE");
            $organisation->setLegalInformationBusinessName("New Business Name");
            $organisation->setLegalInformationSiret("NEWSIRET");
            $organisation->setLegalInformationVatNumber("NEWVATNUMBER");

            $organisationService = $this->buildOrganisationService();

            $this->expectException(UserDoesntBelongToOrganisation::class);
            $organisationService->updateOrganisation($organisationId, $organisation);
        }
    }

    public function testAddBasket(): void
    {
        $organisationService = $this->buildOrganisationService('user+orga@usc.com', 'password');

        $organisationId = $this->getOrganisationId(1);

        if (\is_string($organisationId)) {
            $responseData = $organisationService->addBasket($organisationId, "Mon nouveau panier");

            $this->assertSame("Mon nouveau panier", $responseData['name']);
            $this->assertSame(false, $responseData['locked']);
            $this->assertSame(false, $responseData['accepted']);
        }
    }

    public function testCannotAddBasketIfNotOwned(): void
    {
        $organisationService = $this->buildOrganisationService('admin@wizaplace.com', 'password');

        $organisationId = $this->getOrganisationId(1);

        if (\is_string($organisationId)) {
            $this->expectException(UserDoesntBelongToOrganisation::class);
            $organisationService->addBasket($organisationId, "Mon nouveau panier");
        }
    }

    public function testLockBasket(): void
    {
        $organisationService = $this->buildOrganisationService('user+orga@usc.com', 'password');

        $organisationId = $this->getOrganisationId(1);

        if (\is_string($organisationId)) {
            $responseData = $organisationService->addBasket($organisationId, "Mon nouveau panier");

            $basketId = $responseData['basketId'];

            $responseData = $organisationService->lockBasket($organisationId, $basketId);

            $this->assertSame("Mon nouveau panier", $responseData['name']);
            $this->assertSame(true, $responseData['locked']);
            $this->assertSame(false, $responseData['accepted']);
        }
    }

    public function testCannotLockBasketIfNotOwned(): void
    {
        $organisationService = $this->buildOrganisationService('user+orga@usc.com', 'password');

        $organisationId = $this->getOrganisationId(1);

        if (\is_string($organisationId)) {
            $responseData = $organisationService->addBasket($organisationId, "Mon nouveau panier");

            $basketId = $responseData['basketId'];

            $organisationService = $this->buildOrganisationService();

            $this->expectException(UserDoesntBelongToOrganisation::class);
            $organisationService->lockBasket($organisationId, $basketId);
        }
    }

    public function testBasketValidation(): void
    {
        $organisationService = $this->buildOrganisationService('user+orga@usc.com', 'password');

        $organisationId = $this->getOrganisationId(1);

        if (\is_string($organisationId)) {
            $responseData = $organisationService->addBasket($organisationId, "Mon nouveau panier");

            $basketId = $responseData['basketId'];

            $responseData = $organisationService->validateBasket($organisationId, $basketId);

            $this->assertSame("Mon nouveau panier", $responseData['name']);
            $this->assertSame(false, $responseData['locked']);
            $this->assertSame(true, $responseData['accepted']);
        }
    }

    public function testHideBasket(): void
    {
        $organisationService = $this->buildOrganisationService('user+orga@usc.com', 'password');

        $organisationId = $this->getOrganisationId(1);

        if (\is_string($organisationId)) {
            $responseData = $organisationService->addBasket($organisationId, "Mon nouveau panier");

            $basketId = $responseData['basketId'];

            $responseData = $organisationService->hideBasket($organisationId, $basketId);

            $this->assertSame("Mon nouveau panier", $responseData['name']);
            $this->assertSame(true, $responseData['hidden']);
        }
    }

    public function testCannotValidateBasketIfNotOwned(): void
    {
        $organisationService = $this->buildOrganisationService('user+orga@usc.com', 'password');

        $organisationId = $this->getOrganisationId(1);

        if (\is_string($organisationId)) {
            $responseData = $organisationService->addBasket($organisationId, "Mon nouveau panier");

            $basketId = $responseData['basketId'];

            $organisationService = $this->buildOrganisationService();

            $this->expectException(UserDoesntBelongToOrganisation::class);
            $organisationService->validateBasket($organisationId, $basketId);
        }
    }

    public function testBasketCheckout(): void
    {
        $organisationService = $this->buildOrganisationService('user+orga@usc.com', 'password');
        $organisationId = $this->getOrganisationId(1);

        if (\is_string($organisationId)) {
            $responseData = $organisationService->addBasket($organisationId, "Mon nouveau panier");

            $basketId = $responseData['basketId'];

            //Add products to basket
            $basketService = $this->buildBasketService('user+orga@usc.com', 'password');
            $basketService->addProductToBasket($basketId, new DeclinationId('1'), 1);

            $responseData = $organisationService->checkoutBasket($organisationId, $basketId, 1, true, 'http://www.google.fr');

            $this->assertCount(1, $responseData['orders']);
        }
    }

    public function testBasketCheckoutIfNotOwned(): void
    {
        $organisationService = $this->buildOrganisationService('user+orga@usc.com', 'password');
        $organisationId = $this->getOrganisationId(1);

        if (\is_string($organisationId)) {
            $responseData = $organisationService->addBasket($organisationId, "Mon nouveau panier");

            $basketId = $responseData['basketId'];

            //Add products to basket
            $basketService = $this->buildBasketService('user+orga@usc.com', 'password');
            $basketService->addProductToBasket($basketId, new DeclinationId('1'), 1);

            $organisationService = $this->buildOrganisationService();
            $this->expectException(UserDoesntBelongToOrganisation::class);

            $organisationService->checkoutBasket($organisationId, $basketId, 1, true, 'http://www.google.fr');
        }
    }

    public function testGetOrganisationFromUser(): void
    {
        $organisationService = $this->buildOrganisationService('admin@wizaplace.com', 'password');

        $response = $organisationService->getOrganisationFromUserId(11);
        $this->assertInstanceOf(Organisation::class, $response);
    }

    public function testRemoveAndAddUserToGroup(): void
    {
        $organisationService = $this->buildOrganisationService('admin@wizaplace.com', 'password');

        $organisationId = $this->getOrganisationId();

        $organisationGroups = $organisationService->getOrganisationGroups((string) $organisationId);
        foreach ($organisationGroups as $key => $group) {
            if ($key === 0) {
                $organisationService->removeUserFromGroup($group->getId(), 11);
            }
            $this->assertInstanceOf(OrganisationGroup::class, $group);
        }
    }

    public function testGetOrganisationBaskets(): void
    {
        $organisationService = $this->buildOrganisationService('user+orga@usc.com', 'password');

        $organisationId = $this->getOrganisationId(1);

        $organisationService->addBasket((string) $organisationId, "fake_basket");

        $baskets = $organisationService->getOrganisationBaskets((string) $organisationId);
        foreach ($baskets as $basket) {
            $this->assertInstanceOf(OrganisationBasket::class, $basket);
        }
    }

    public function testGetOrganisationOrders(): void
    {
        $organisationService = $this->buildOrganisationService('admin@wizaplace.com', 'password');
        $organisationId = $this->getOrganisationId(1);

        $response = $organisationService->getOrganisationOrders((string) $organisationId);

        if (!empty($response['orders'])) {
            $this->assertCount(1, $response['orders']);

            foreach ($response['orders'] as $order) {
                $this->assertInstanceOf(OrderSummary::class, $order);
            }
        }

        $response = $organisationService->getOrganisationOrders((string) $organisationId, 500, 10);

        $this->assertSame(0, $response['count']);
        $this->assertSame(1, $response['total']);
    }

    public function dataProviderGetOrganisationPaginatedOrders(): array
    {
        return [
            'with limit lower than default_limit and start' => [4, 2, [1, 4, 2, 3]],
            'with limit greater than default_limit and start' => [200, 1, [2, 200, 1, 3]],
            'with limit equals to 0, no start' => [0, null, [3, 0, 0, 3]],
            'no limit, no start' => [null, null, [3, 100, 0, 3]],
        ];
    }

    /** @dataProvider dataProviderGetOrganisationPaginatedOrders */
    public function testGetOrganisationPaginatedOrders(
        int $limit = null,
        int $start = null,
        array $expected = []
    ): void {
        $organisationService = $this->buildOrganisationService('admin@wizaplace.com', 'password');
        $organisationId = $this->getOrganisationId(1);

        $paginatedData = $organisationService->getOrganisationPaginatedOrders((string) $organisationId, $start, $limit);

        foreach ($paginatedData->getItems()['orders'] as $order) {
            $this->assertInstanceOf(OrderSummary::class, $order);
        }

        static::assertEquals($expected[0], \count($paginatedData->getItems()['orders']));
        static::assertEquals($expected[1], $paginatedData->getLimit());
        static::assertEquals($expected[2], $paginatedData->getOffset());
        static::assertEquals($expected[3], $paginatedData->getTotal());
    }

    public function dataProviderGetOrganisationPaginatedOrdersThrowsException(): array
    {
        return [
            'organisation not found' => [
                'admin@wizaplace.com',
                'password',
                NotFound::class,
                "The organisation doesn't exist.",
                'xxx',
            ],
            'access denied' => [
                'customer-1@world-company.com',
                'password-customer-1',
                AccessDenied::class,
                "You're neither a marketplace administrator nor an organisation's administrator",
                null
            ],
        ];
    }

    /** @dataProvider dataProviderGetOrganisationPaginatedOrdersThrowsException */
    public function testGetOrganisationPaginatedOrdersThrowsException(
        string $user,
        string $password,
        string $exceptionClass,
        string $exceptionMessage,
        string $organisationId = null
    ): void {
        $this->expectException($exceptionClass);
        $this->expectExceptionMessage($exceptionMessage);

        $organisationService = $this->buildOrganisationService($user, $password);
        $organisationId = $organisationId ?? $this->getOrganisationId(1);

        $organisationService->getOrganisationPaginatedOrders($organisationId);
    }

    public function testAddUserAdminToOrganisation(): void
    {
        $organisationService = $this->buildOrganisationService('admin@wizaplace.com', 'password');

        $organisationId = $this->getOrganisationId();

        $groupId = "";
        foreach ($organisationService->getOrganisationGroups((string) $organisationId) as $group) {
            if ($group->getType() === "admin") {
                $groupId = $group->getId();
                break;
            }
        }

        $data = [
            "groupId"    => $groupId,
            "email"      => "lemmy@motohead.com",
            "firstName"  => "Lemmy",
            "lastName"   => "Kilmister",
            "password"   => "born2loose",
            "status"     => "A",
            "title"      => "mr",
            "occupation" => "singer",
        ];


        $idCard = $this->mockUploadedFile('minimal.pdf');
        $proof  = $this->mockUploadedFile('minimal.pdf');

        $files = [
            new OrganisationFile("identityCard", $idCard->getStream(), $idCard->getClientFilename()),
            new OrganisationFile("proofOfAppointment", $proof->getStream(), $proof->getClientFilename()),
        ];

        $user = $organisationService->addNewUser((string) $organisationId, $data, $files);

        $this->assertInstanceOf(User::class, $user);
    }

    public function testAddNewUserToOrganisationWithAddressesHavingLabelAndComment(): void
    {
        $apiClient = $this->buildApiClient();
        $apiClient->authenticate('admin@wizaplace.com', 'password');
        $userService = new UserService($apiClient);
        $organisationService = new OrganisationService($apiClient);

        $organisationId = $this->getOrganisationId();

        $groupId = "";
        foreach ($organisationService->getOrganisationGroups((string) $organisationId) as $group) {
            if ($group->getType() === "admin") {
                $groupId = $group->getId();
                break;
            }
        }

        $shippingAddress = [
            'title' => UserTitle::MR(),
            'firstname' => 'Jean',
            'lastname' => 'Joe',
            'address' => 'Rue de madrid',
            'address_2' => '3ème étage',
            'city' => 'Lyon',
            'country' => 'FR',
            'label' => 'Domicile',
            'comment' => 'Près de la poste',
        ];

        $data = [
            "groupId"    => $groupId,
            "email"      => "user@motohead.com",
            "firstName"  => "Lemmy",
            "lastName"   => "Kilmister",
            "password"   => "born2loose",
            "status"     => "A",
            "title"      => "mr",
            "occupation" => "singer",
            "shippingAddress" => $shippingAddress,
        ];

        $idCard = $this->mockUploadedFile('minimal.pdf');
        $proof  = $this->mockUploadedFile('minimal.pdf');

        $files = [
            new OrganisationFile("identityCard", $idCard->getStream(), $idCard->getClientFilename()),
            new OrganisationFile("proofOfAppointment", $proof->getStream(), $proof->getClientFilename()),
        ];

        $createdUser = $organisationService->addNewUser((string) $organisationId, $data, $files);

        $user = $userService->getProfileFromId($createdUser->getId());

        static::assertSame('Domicile', $user->getShippingAddress()->getLabel());
        static::assertSame('Près de la poste', $user->getShippingAddress()->getComment());
    }

    public function testCanCreateAnOrganisationGroup(): void
    {
        $organisationService = $this->buildOrganisationService('admin@wizaplace.com', 'password');

        $organisationId = $this->getOrganisationId();

        if (\is_string($organisationId)) {
            $responseData = $organisationService->createGroup($organisationId, "name", "type");

            $this->assertRegExp(
                '~^[a-zA-Z0-9]{8}(-[a-zA-Z0-9]{4}){4}[a-zA-Z0-9]{8}$~',
                $responseData['id']
            );
        }
    }

    public function testGetListGroupUser(): void
    {
        $organisationService = $this->buildOrganisationService('admin@wizaplace.com', 'password');

        $organisationId = $this->getOrganisationId();

        if (\is_string($organisationId)) {
            $groups = $organisationService->getOrganisationGroups($organisationId);

            foreach ($groups as $group) {
                $usersList = $organisationService->getGroupUsers($group->getId());

                foreach ($usersList as $user) {
                    $this->assertInstanceOf(User::class, $user);
                }
            }
        }
    }

    /**
     * We try to get all the orga's order and foreach one, get the order détails.
     *
     * Note: Would be a better check if we could be sur than the order user is différent of the admin user authenticated
     *       but for now we don't have the user in the order Entity
     *
     * @throws BadCredentials
     * @throws \Wizaplace\SDK\Authentication\AuthenticationRequired
     * @throws \Wizaplace\SDK\Exception\NotFound
     */
    public function testGetOrder(): void
    {
        // Organisation admin user
        $organisationService = $this->buildOrganisationService('user+orga@usc.com', 'password');
        // "University of Southern California" organisation with few orders
        $organisationId = $this->getOrganisationId(1);
        $this->assertInternalType('string', $organisationId);

        if (\is_string($organisationId)) {
            // Get all the organisation orders
            $organisationOrders = $organisationService->getOrganisationPaginatedOrders($organisationId);

            if (!empty($organisationOrders->getItems()['orders'])) {
                foreach ($organisationOrders->getItems()['orders'] as $order) {
                    // Get the order details
                    $orderId = $order->getOrderId();
                    $orderDetails = $organisationService->getOrder($orderId);
                    $this->assertInstanceOf(Order::class, $orderDetails);
                }
            }
        }
    }

    public function testGetOrderWithAddressesHavingLabelAndComment(): void
    {
        // Organisation admin user
        $organisationService = $this->buildOrganisationService('user+orga@usc.com', 'password');
        $organisationId = $this->getOrganisationId(1);

        $organisationOrders = $organisationService->getOrganisationPaginatedOrders($organisationId);

        foreach ($organisationOrders->getItems()['orders'] as $order) {
            // Get the order details
            $orderId = $order->getOrderId();
            $orderDetails = $organisationService->getOrder($orderId);
            static::assertSame('', $orderDetails->getShippingAddress()->getLabel());
            static::assertSame('', $orderDetails->getShippingAddress()->getComment());
            static::assertSame('', $orderDetails->getBillingAddress()->getLabel());
            static::assertSame('', $orderDetails->getBillingAddress()->getComment());
        }
    }

    public function testGetOrganisationWithCompanyName(): void
    {
        $organisationService = $this->buildOrganisationService('admin@wizaplace.com', 'password');
        $organisationId = $this->getOrganisationId();

        if (\is_string($organisationId) === true) {
            $organisationService->get($organisationId);
            $organisationGetOrder = $organisationService->getOrganisationPaginatedOrders($organisationId);
            $orderService = $this->buildOrderService('customer-1@world-company.com', 'password');
            $orders = $orderService->getOrder($organisationGetOrder->getItems()['orders'][0]->getOrderId());
            $this->assertSame('The World Company Inc.', $orders->getCompanyName());
        }
    }

    public function testGetOrganisationOrdersByOrganisationIdWithRefundedData(): void
    {
        $organisationService = $this->buildOrganisationService('admin@wizaplace.com', 'password');
        $organisationId = $this->getOrganisationId(1);
        $organisationOrders = $organisationService->getOrganisationPaginatedOrders($organisationId);

        $orders = $organisationOrders->getItems()['orders'];

        static::assertGreaterThan(0, \count($orders));
        foreach ($orders as $order) {
            static::assertInternalType('boolean', $order->isRefunded());
        }
    }

    public function testGetOrganisationsOrderWithRefundedData(): void
    {
        $organisationService = $this->buildOrganisationService('user+orga@usc.com', 'password');

        $organisationOrder = $organisationService->getOrder(8);

        static::assertFalse($organisationOrder->isRefunded());
    }

    public function testGetOrganisationOrdersByOrganisationIdWithBalance(): void
    {
        $organisationService = $this->buildOrganisationService('admin@wizaplace.com', 'password');
        $organisationId = $this->getOrganisationId(1);
        $organisationOrders = $organisationService->getOrganisationPaginatedOrders($organisationId);

        $orders = $organisationOrders->getItems()['orders'];

        static::assertGreaterThan(0, \count($orders));
        foreach ($orders as $order) {
            static::assertSame(0.0, $order->getBalance());
        }
    }

    public function testGetOrganisationsOrderWithBalance(): void
    {
        $organisationService = $this->buildOrganisationService('user+orga@usc.com', 'password');

        $organisationOrder = $organisationService->getOrder(8);

        static::assertSame(0.0, $organisationOrder->getBalance());
    }

    public function testGetOrganisationsOrderAttachments(): void
    {
        $organisationService = $this->buildOrganisationService('user+orga@usc.com', 'password');
        $organisationOrder = $organisationService->getOrder(9);
        $orderAttachments = $organisationOrder->getOrderAttachments();

        static::assertCount(2, $orderAttachments);

        $firstAttachments = $orderAttachments[0];

        static::assertSame('9ef4895b-a9a4-41ef-a909-b57ed306e68b', $firstAttachments->getId());
        static::assertSame('P1', $firstAttachments->getName());
        static::assertSame('pp.png', $firstAttachments->getFilename());
        static::assertSame(OrderAttachmentType::CUSTOMER_INVOICE()->getValue(), $firstAttachments->getType()->getValue());
    }

    public function testGetOrganisationsOrderAttachmentById(): void
    {
        $organisationService = $this->buildOrganisationService('user+orga@usc.com', 'Windows.98');
        $orderId = 9;
        $organisationOrder = $organisationService->getOrder($orderId);
        $orderAttachments = $organisationOrder->getOrderAttachments();

        static::assertCount(1, $orderAttachments);

        $firstAttachments = $orderAttachments[0];
        $orderAttachmentId = $firstAttachments->getId();
        $organisationAttachmentService = $this->buildOrganisationAttachmentService('user+orga@usc.com', 'Windows.98');
        $response = $organisationAttachmentService->getOrganisationOrderAttachment($orderId, $orderAttachmentId);

        static::assertSame($response->getId(), $orderAttachmentId);
    }

    public function testGetOrganisationsHasNotAttachments(): void
    {
        $organisationService = $this->buildOrganisationService('user+orga@usc.com', 'password');
        $organisationOrder = $organisationService->getOrder(15);
        $orderAttachments = $organisationOrder->getOrderAttachments();
        static::assertCount(0, $orderAttachments);
    }

    public function testUserCanDownloadOrganisationsOrderAttachments(): void
    {
        $organisationService = $this->buildOrganisationService('user+orga@usc.com', 'Windows.98');
        $organisationOrder = $organisationService->getOrder(9);
        $orderAttachments = $organisationOrder->getOrderAttachments();

        static::assertCount(1, $orderAttachments);

        $firstAttachments = $orderAttachments[0];
        $orderAttachmentId = $firstAttachments->getId();
        $organisationAttachmentService = $this->buildOrganisationAttachmentService('user+orga@usc.com', 'Windows.98');
        $file = $organisationAttachmentService->downloadOrganisationOrderAttachment(9, $orderAttachmentId);

        $fileHeader = '%PDF-1.4';
        $fileContents = $file->getContents();
        $this->assertStringStartsWith($fileHeader, $fileContents);
        $this->assertGreaterThan(\strlen($fileHeader), \strlen($fileContents));
    }

    public function testGetOrganisationOrdersByOrganisationIdWithBankWireTransactionReference(): void
    {
        $organisationService = $this->buildOrganisationService('user+orga@usc.com', 'password');
        $organisationId = $this->getOrganisationId(1);
        $organisationOrders = $organisationService->getOrganisationPaginatedOrders($organisationId);
        $organisationOrders = $organisationOrders->getItems()['orders'];

        static::assertGreaterThan(0, \count($organisationOrders));
        foreach ($organisationOrders as $order) {
            $orderDetails = $organisationService->getOrder($order->getOrderId());

            if (($orderDetails->getPayment()->getType() === 'bank-transfer')
                && ($orderDetails->getPayment()->getProcessorName() === 'mangopay' || ($orderDetails->getPayment()->getProcessorName() === 'lemonway'))
            ) {
                static::assertNotNull($order->getBankWireTransactionReference());
            } else {
                static::assertNull($order->getBankWireTransactionReference());
            }
        }
    }

    public function testGetOrganisationsOrderWithoutBankWireTransactionReference(): void
    {
        $organisationService = $this->buildOrganisationService('user+orga@usc.com', 'password');
        $organisationOrder = $organisationService->getOrder(8);

        static::assertSame($organisationOrder->getPayment()->getType(), 'manual');
        static::assertNull($organisationOrder->getBankWireTransactionReference());
    }

    public function testGetOrganisationsOrderWithBankWireTransactionReference(): void
    {
        $organisationService = $this->buildOrganisationService('user+orga@usc.com', 'password');
        $organisationOrder = $organisationService->getOrder(17);

        static::assertSame($organisationOrder->getPayment()->getProcessorName(), 'mangopay');
        static::assertSame($organisationOrder->getPayment()->getType(), 'bank-transfer');
        static::assertNotNull($organisationOrder->getBankWireTransactionReference());
    }

    /**
     * Return and Order service, depending of a logged user, or not
     * @param string $email
     * @param string $password
     * @param bool $authenticate
     * @return OrderService
     * @throws BadCredentials
     */
    public function buildOrderService(string $email = 'admin@wizaplace.com', string $password = 'password', bool $authenticate = true): OrderService
    {
        $apiClient = $this->buildApiClient();
        if ($authenticate === true) {
            $apiClient->authenticate($email, $password);
        }

        return new OrderService($apiClient);
    }

    /**
     * Return the id of an organisation, if found, else false
     * @param int $index
     * @return string|bool
     * @throws \Wizaplace\SDK\Authentication\AuthenticationRequired
     * @throws \Wizaplace\SDK\Authentication\BadCredentials
     */
    private function getOrganisationId(int $index = 0)
    {
        $organisationService = $this->buildOrganisationService('admin@wizaplace.com', 'password');

        //get the list of all organisation, to have their ID
        $listOrga = $organisationService->getList();

        if (\is_array($listOrga)
            && \count($listOrga) > 0
            && isset($listOrga['_embedded'], $listOrga['_embedded']['organisations'], $listOrga['_embedded']['organisations'][$index])
        ) {
            $tempOrga = $listOrga['_embedded']['organisations'][$index];
            $organisationId = $tempOrga['id'];

            return $organisationId;
        }

        return false;
    }

    /**
     * Return and Organisation service, depending of a logged user, or not
     * @param string $email
     * @param string $password
     * @param bool $authenticate
     * @return OrganisationService
     * @throws BadCredentials
     */
    private function buildOrganisationService(string $email = 'customer-3@world-company.com', string $password = 'password-customer-3', bool $authenticate = true): OrganisationService
    {
        $apiClient = $this->buildApiClient();
        if ($authenticate) {
            $apiClient->authenticate($email, $password);
        }

        return new OrganisationService($apiClient);
    }

    /**
     * Return an Organisation attachment service, depending of a logged user, or not
     * @param string $email
     * @param string $password
     * @param bool $authenticate
     *
     * @return OrganisationOrderAttachmentService
     * @throws BadCredentials
     * @throws \Wizaplace\SDK\Exception\JsonDecodingError
     */
    private function buildOrganisationAttachmentService(string $email = 'customer-3@world-company.com', string $password = 'password-customer-3', bool $authenticate = true): OrganisationOrderAttachmentService
    {
        $apiClient = $this->buildApiClient();
        if ($authenticate) {
            $apiClient->authenticate($email, $password);
        }

        return new OrganisationOrderAttachmentService($apiClient);
    }

    /**
     * The datas for testing the registration of an organisation
     * @return array
     */
    private function getDataForRegistration()
    {
        $data = [
            'name' => "Galactic Empire",
            'businessName' => "L'Empire",
            'businessUnitName' => "Death Star",
            'businessUnitCode' => "DS",
            'siret' => "DS9988776655",
            'vatNumber' => "DS1122334455",
            'address' => [
                'address' => "La Cantina",
                'additionalAddress' => "Han Shot First",
                'zipCode' => "69521",
                'city' => "Mos Esley",
                'state' => "Bordure Exterieure",
                'country' => "France",
            ],
            'shippingAddress' => [
                'address' => "La Cantina",
                'additionalAddress' => "Han Shot First",
                'zipCode' => "69521",
                'city' => "Mos Esley",
                'state' => "Bordure Exterieure",
                'country' => "France",
            ],
            'administrator' => [
                'email' => 'darth@empire.com',
                'firstName' => 'Anakin',
                'lastName' => 'Skywalker',
                'password' => 'jedi',
                'title' => 'mr',
                'occupation' => 'kill the emperor',
            ],
        ];

        return $data;
    }

    /**
     * Return an Basket service
     *
     * @param string $email
     * @param string $password
     * @param bool $authenticate
     * @return BasketService
     * @throws BadCredentials
     */
    private function buildBasketService(string $email = 'customer-3@world-company.com', string $password = 'password-customer-3', bool $authenticate = true): BasketService
    {
        $apiClient = $this->buildApiClient();
        if ($authenticate) {
            $apiClient->authenticate($email, $password);
        }

        return new BasketService($apiClient);
    }
}
