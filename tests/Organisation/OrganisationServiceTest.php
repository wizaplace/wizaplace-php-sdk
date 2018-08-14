<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */

declare(strict_types = 1);

namespace Wizaplace\SDK\Tests\Organisation;


use GuzzleHttp\Exception\ClientException;
use Wizaplace\SDK\Organisation\OrganisationAddress;
use Wizaplace\SDK\Organisation\OrganisationService;
use Wizaplace\SDK\Tests\ApiTestCase;

final class OrganisationServiceTest extends ApiTestCase
{
    public function testGetOrganisation()
    {
        $organisationService = $this->buildOrganisationService('admin@wizaplace.com', 'password');

        $organisationId = $this->getFirstOrganisationId();

        $organisation = $organisationService->get($organisationId);

        $this->assertSame($organisationId, $organisation->getId());
        $this->assertSame('University of New York', $organisation->getName());
        $this->assertSame('University of New York', $organisation->getLegalInformationBusinessName());
        $this->assertSame('44229377500031', $organisation->getLegalInformationSiret());
        $this->assertSame('FR99999999999', $organisation->getLegalInformationVatNumber());
        $this->assertSame('NTW', $organisation->getBusinessUnitCode());
        $this->assertSame('Network Infrastructure', $organisation->getBusinessUnitName());
        $this->assertArrayHasKey('address', $organisation->getAddress());
        $this->assertSame("194 Lindale Avenue", $organisation->getAddress()['address']);
        $this->assertArrayHasKey('additionalAddress', $organisation->getAddress());
        $this->assertSame("", $organisation->getAddress()['additionalAddress']);
        $this->assertArrayHasKey('zipCode', $organisation->getAddress());
        $this->assertSame("94801", $organisation->getAddress()['zipCode']);
        $this->assertArrayHasKey('city', $organisation->getAddress());
        $this->assertSame("Richmond", $organisation->getAddress()['city']);
        $this->assertArrayHasKey('state', $organisation->getAddress());
        $this->assertSame("", $organisation->getAddress()['state']);
        $this->assertArrayHasKey('country', $organisation->getAddress());
        $this->assertSame("US", $organisation->getAddress()['country']);
        $this->assertArrayHasKey('address', $organisation->getShippingAddress());
        $this->assertSame("4917 Snyder Avenue", $organisation->getShippingAddress()['address']);
        $this->assertArrayHasKey('additionalAddress', $organisation->getShippingAddress());
        $this->assertSame("", $organisation->getShippingAddress()['additionalAddress']);
        $this->assertArrayHasKey('zipCode', $organisation->getShippingAddress());
        $this->assertSame("28209", $organisation->getShippingAddress()['zipCode']);
        $this->assertArrayHasKey('city', $organisation->getShippingAddress());
        $this->assertSame("North Carolina", $organisation->getShippingAddress()['city']);
        $this->assertArrayHasKey('state', $organisation->getShippingAddress());
        $this->assertSame("", $organisation->getShippingAddress()['state']);
        $this->assertArrayHasKey('country', $organisation->getShippingAddress());
        $this->assertSame("US", $organisation->getShippingAddress()['country']);
        $this->assertSame('pending', $organisation->getStatus());
    }

    public function testGetList()
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

    public function testGetListUsers()
    {
        $organisationService = $this->buildOrganisationService('admin@wizaplace.com', 'password');

        $organisationId = $this->getFirstOrganisationId();
        $listUsers = $organisationService->getListUsers($organisationId);

        $this->assertNotEmpty($listUsers);
        $this->assertCount(3, $listUsers);
        $this->assertArrayHasKey('total', $listUsers);
        $this->assertArrayHasKey('count', $listUsers);
        $this->assertArrayHasKey('_embedded', $listUsers);
        $this->assertSame(1, $listUsers['count']);
        $this->assertSame(1, $listUsers['total']);
    }

    public function testOrganisationAddressesUpdate()
    {
        $organisationService = $this->buildOrganisationService('admin@wizaplace.com', 'password');

        $organisationId = $this->getFirstOrganisationId();

        $address = [
            'address' => '10 rue de la gare',
            'additional_address' => 'Lieu dit: au dessus de LDLC',
            'zip_code' => '69009',
            'city' => 'Lyon',
            'state' => 'Rhone',
            'country' => 'France',
        ];

        $address = new OrganisationAddress($address);

        $shippingAddress = [
            'address' => '3 rue de la République',
            'additional_address' => '',
            'zip_code' => '75002',
            'city' => 'Paris',
            'state' => 'Paris',
            'country' => 'France',
        ];

        $shippingAddress = new OrganisationAddress($shippingAddress);

        $responseData = $organisationService->organisationAddressesUpdate($organisationId, $address, $shippingAddress);

        $organisation = $organisationService->get($organisationId);

        $this->assertSame('10 rue de la gare', $organisation->getAddress()->getAddress());
    }

    /**
     * Return the if of the first organisation, if found, else false
     * @return string|bool
     * @throws \Wizaplace\SDK\Authentication\AuthenticationRequired
     * @throws \Wizaplace\SDK\Authentication\BadCredentials
     */
    private function getFirstOrganisationId()
    {
        $organisationService = $this->buildOrganisationService('admin@wizaplace.com', 'password');

        //get the list of all organisation, to have their ID
        $listOrga = $organisationService->getList();

        if (is_array($listOrga)
            && count($listOrga) > 0
            && isset($listOrga['_embedded'], $listOrga['_embedded']['organisations'], $listOrga['_embedded']['organisations'][0])) {
            $tempOrga = $listOrga['_embedded']['organisations'][0];
            $organisationId = $tempOrga['id'];
            return $organisationId;
        }
        return false;
    }

    private function buildOrganisationService(string $email = 'customer-3@world-company.com', string $password = 'password-customer-3'): OrganisationService
    {
        $apiClient = $this->buildApiClient();
        $apiClient->authenticate($email, $password);

        return new OrganisationService($apiClient);
    }
}
