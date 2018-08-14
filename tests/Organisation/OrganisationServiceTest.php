<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */

declare(strict_types = 1);

namespace Wizaplace\SDK\Tests\Organisation;

use Psr\Http\Message\UploadedFileInterface;
use Wizaplace\SDK\Organisation\Organisation;
use Wizaplace\SDK\Organisation\OrganisationAddress;
use Wizaplace\SDK\Organisation\OrganisationService;
use Wizaplace\SDK\Tests\ApiTestCase;
use function GuzzleHttp\Psr7\stream_for;

final class OrganisationServiceTest extends ApiTestCase
{
    public function testRegister()
    {
        $organisationService = $this->buildOrganisationService('', '', false);

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
//        $this->assertSame("Galactic", $organisation->getShippingAddress());
//        $this->assertSame("Galactic", $organisation->getAdministrator());
        $this->assertSame("pending", $organisation->getStatus());

    }

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

        $organisationService->organisationAddressesUpdate($organisationId, $address, $shippingAddress);

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

    private function buildOrganisationService(string $email = 'customer-3@world-company.com', string $password = 'password-customer-3', bool $authenticate = true): OrganisationService
    {
        $apiClient = $this->buildApiClient();
        if ($authenticate) {
            $apiClient->authenticate($email, $password);
        }

        return new OrganisationService($apiClient);
    }

    private function mockUploadedFile(string $filename): UploadedFileInterface
    {
        $path = __DIR__.'/../fixtures/files/'.$filename;

        /** @var UploadedFileInterface|\PHPUnit_Framework_MockObject_MockObject $file */
        $file = $this->createMock(UploadedFileInterface::class);
        $file->expects($this->once())->method('getStream')->willReturn(stream_for(fopen($path, 'r')));
        $file->expects($this->once())->method('getClientFilename')->willReturn($filename);

        return $file;
    }
}
