<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\SDK\Tests\Company;

use GuzzleHttp\Exception\ClientException;
use Symfony\Component\HttpFoundation\Response;
use Wizaplace\SDK\Authentication\AuthenticationRequired;
use Wizaplace\SDK\Company\Company;
use Wizaplace\SDK\Company\CompanyC2CRegistration;
use Wizaplace\SDK\Company\CompanyRegistration;
use Wizaplace\SDK\Company\CompanyRegistrationResult;
use Wizaplace\SDK\Company\CompanyService;
use Wizaplace\SDK\Company\CompanyUpdateCommand;
use Wizaplace\SDK\Company\UnauthenticatedCompanyRegistration;
use Wizaplace\SDK\Exception\CompanyNotFound;
use Wizaplace\SDK\PaginatedData;
use Wizaplace\SDK\Subscription\SubscriptionSummary;
use Wizaplace\SDK\Tests\ApiTestCase;
use Wizaplace\SDK\Tests\File\Mock;
use Wizaplace\SDK\User\UserType;

/**
 * @see CompanyService
 */
final class CompanyServiceTest extends ApiTestCase
{
    public function testRegisteringACompanyWithAllInformation(): void
    {
        $companyRegistration = new CompanyRegistration('ACME2 Test Inc', 'acme2@example.com');
        $companyRegistration->setAddress('24 rue de la gare');
        $companyRegistration->setCapital('1 000 000 000 $');
        $companyRegistration->setCity('Lyon');
        $companyRegistration->setCountry('FR');
        $companyRegistration->setDescription('Super ACME company');
        $companyRegistration->setFax('01 02 03 04 05');
        $companyRegistration->setLegalStatus('SARL');
        $companyRegistration->setPhoneNumber('01 02 03 04 05 06');
        $companyRegistration->setRcs('RCS VANNES B 514 919 844');
        $companyRegistration->setVatNumber('12345678901');
        $companyRegistration->setZipcode('69009');
        $companyRegistration->setSiretNumber('732 829 320 00074');
        $companyRegistration->setSlug('acme-inc');
        $companyRegistration->setUrl('https://acme.example.com/');
        $companyRegistration->setExtra(['driving_license_number' => '654987321']);
        $companyRegistration->setNafCode('ABCDEF');

        $companyRegistration->addUploadedFile('rib', $this->mockUploadedFile('minimal.pdf'));
        $companyRegistration->addUploadedFile('idCard', $this->mockUploadedFile('minimal.pdf'));

        $companyService = $this->buildUserCompanyService('customer-3@world-company.com', 'password-customer-3');

        $result = $companyService->register($companyRegistration);

        $company = $result->getCompany();
        $this->assertGreaterThan(0, $company->getId());
        $this->assertSame('acme-inc', $company->getSlug());
        $this->assertSame('acme2@example.com', $company->getEmail());
        $this->assertSame('24 rue de la gare', $company->getAddress());
        $this->assertSame('1 000 000 000 $', $company->getCapital());
        $this->assertSame('Lyon', $company->getCity());
        $this->assertSame('FR', $company->getCountry());
        $this->assertSame('Super ACME company', $company->getDescription());
        $this->assertSame('01 02 03 04 05', $company->getFax());
        $this->assertSame('SARL', $company->getLegalStatus());
        $this->assertSame('01 02 03 04 05 06', $company->getPhoneNumber());
        $this->assertSame('RCS VANNES B 514 919 844', $company->getRcs());
        $this->assertSame('12345678901', $company->getVatNumber());
        $this->assertSame('69009', $company->getZipcode());
        $this->assertSame('732 829 320 00074', $company->getSiretNumber());
        $this->assertEquals('https://acme.example.com/', $company->getUrl());
        $this->assertSame(['driving_license_number' => '654987321'], $company->getExtra());
        $this->assertSame('ABCDEF', $company->getNafCode());

        $this->assertTrue($result->getFileUploadResult('rib')->isSuccess());
        $this->assertNull($result->getFileUploadResult('rib')->getErrorMessage());
        $this->assertTrue($result->getFileUploadResult('idCard')->isSuccess());
        $this->assertNull($result->getFileUploadResult('idCard')->getErrorMessage());

        $files = $companyService->getCompanyFiles($company->getId());
        $this->assertCount(2, $files);
        foreach ($files as $file) {
            $response = $companyService->fetchFile($file);

            $this->assertSame('application/pdf', $response->getHeaderLine('Content-Type'));
            $this->assertStringStartsWith('attachment; filename="', $response->getHeaderLine('Content-Disposition'));
        }


        // Update file
        $file = $this->mockUploadedFile('minimal.pdf');

        $update = $companyService->updateFile($company->getId(), 'idCard', [
            'name'     => "idCard",
            'contents' => $file->getStream(),
            'filename' => $file->getClientFilename(),
        ]);
        $this->assertSame([
            'success' => true,
        ], $update);

        $files = $companyService->getCompanyFiles($company->getId());
        $this->assertCount(2, $files);


        // Delete file
        $delete = $companyService->deleteFile($company->getId(), 'idCard');
        $this->assertNull($delete);

        $files = $companyService->getCompanyFiles($company->getId());
        $this->assertCount(1, $files);
    }

    public function testRegisteringACompanyWithMinimalInformation(): void
    {
        $companyRegistration = new CompanyRegistration('ACME Test Inc', 'acme@example.com');

        $result = $this->buildUserCompanyService('customer-3@world-company.com', 'password-customer-3')->register($companyRegistration);

        $company = $result->getCompany();
        $this->assertGreaterThan(0, $company->getId());
        $this->assertStringStartsWith('acme-test-inc', $company->getSlug());
        $this->assertSame('acme@example.com', $company->getEmail());
        $this->assertEmpty($company->getFax());
    }

    public function testRegisteringACompanyWithInvalidEmail(): void
    {
        $companyRegistration = new CompanyRegistration('ACME Test Inc', 'acme@@example.com');

        $this->expectException(ClientException::class); // @TODO: decorate?
        $this->buildUserCompanyService('customer-3@world-company.com', 'password-customer-3')->register($companyRegistration);
    }

    public function testRegisteringACompanyWithEmptyRequiredFields(): void
    {
        $this->expectException(ClientException::class);
        $this->expectExceptionCode(400);
        $this->buildUserCompanyService('customer-3@world-company.com', 'password-customer-3')->register(new CompanyRegistration('', ''));
    }

    public function testRegisteringACompanyAnonymously(): void
    {
        $this->expectException(AuthenticationRequired::class);
        (new CompanyService($this->buildApiClient()))->register(new CompanyRegistration('doesntmatter', 'really'));
    }

    public function testUploadingBadExtensionRegistrationFiles(): void
    {
        $companyRegistration = new CompanyRegistration('4CME Test Inc', 'acme4@example.com');
        $companyRegistration->addUploadedFile('rib', $this->mockUploadedFile('dummy.txt'));
        $companyService = $this->buildUserCompanyService('customer-3@world-company.com', 'password-customer-3');

        $result = $companyService->register($companyRegistration);
        $this->assertGreaterThan(0, $result->getCompany()->getId());

        $this->assertFalse($result->getFileUploadResult('rib')->isSuccess());
        $this->assertSame('Invalid file', $result->getFileUploadResult('rib')->getErrorMessage());
    }

    public function testRegisteringACompanyUnauthenticated(): void
    {
        $companyRegistration = new UnauthenticatedCompanyRegistration('ACME Test Inc', 'acme5@example.com', 'John', 'Doe');
        $result = (new CompanyService($this->buildApiClient()))->unauthenticatedRegister($companyRegistration);

        $company = $result->getCompany();
        $this->assertGreaterThan(0, $company->getId());
        $this->assertStringStartsWith('acme-test-inc', $company->getSlug());
        $this->assertSame('acme5@example.com', $company->getEmail());
        $this->assertSame('John', $company->getLegalRepresentativeFirstName());
        $this->assertSame('Doe', $company->getLegalRepresentativeLastName());
    }

    public function testRegisteringAC2CCompany(): void
    {
        $service = $this->buildUserCompanyService('customer-3@world-company.com', 'password');

        $result = $service->registerC2CCompany('Super C2C Company');
        $this->assertInstanceOf(CompanyRegistrationResult::class, $result);

        $company = $result->getCompany();
        $this->assertInstanceOf(Company::class, $company);

        $this->assertSame('Super C2C Company', $company->getName());
        $this->assertSame('customer-3@world-company.com', $company->getEmail());
    }

    public function testUpdatingACompany(): void
    {
        $service = $this->buildUserCompanyService('vendor@wizaplace.com', 'password');

        $company = $service->update((new CompanyUpdateCommand(2))->setPhoneNumber('0987654321'));

        $this->assertSame('0987654321', $company->getPhoneNumber());
    }

    public function testUpdatingACompanyWhichDoesNotExistYieldsAnError(): void
    {
        $this->expectException(CompanyNotFound::class);
        $this->buildUserCompanyService()->update((new CompanyUpdateCommand(404))->setPhoneNumber('0123456789'));
    }

    public function testGettingCompanyInfoWithAdminAccount(): void
    {
        $service = $this->buildUserCompanyService('admin@wizaplace.com', 'password');
        $company = $service->getCompany(3);

        $this->assertEquals(3, $company->getId());
        $this->assertSame('The World Company Inc.', $company->getName());
        $this->assertSame('coucou@world-company.com', $company->getEmail());
        $this->assertSame('The World Company Inc.', $company->getDescription());
        $this->assertSame('75001', $company->getZipcode());
        $this->assertSame('40 rue Laure Diebold', $company->getAddress());
        $this->assertSame('Paris', $company->getCity());
        $this->assertSame('FR', $company->getCountry());
        $this->assertSame('0987654321', $company->getPhoneNumber());
        $this->assertSame('the-world-company-inc', $company->getSlug());
    }

    public function testGettingVendorCompanyInfoWithVendorAccount(): void
    {
        $service = $this->buildUserCompanyService('vendor@world-company.com', 'password');
        $company = $service->getCompany(3);

        $this->assertEquals(3, $company->getId());
        $this->assertSame('The World Company Inc.', $company->getName());
        $this->assertSame('coucou@world-company.com', $company->getEmail());
        $this->assertSame('The World Company Inc.', $company->getDescription());
        $this->assertSame('75001', $company->getZipcode());
        $this->assertSame('40 rue Laure Diebold', $company->getAddress());
        $this->assertSame('Paris', $company->getCity());
        $this->assertSame('FR', $company->getCountry());
        $this->assertSame('0987654321', $company->getPhoneNumber());
        $this->assertSame('the-world-company-inc', $company->getSlug());
    }

    public function testRegisteringAC2CCVendorWithUploadFiles(): void
    {
        $service = $this->buildUserCompanyService();
        $file = [
                    [
                        'name'     => 'idCard',
                        'contents' => fopen(__DIR__.'/../fixtures/files/minimal.pdf', 'r'),
                        'filename' => 'minimal.pdf',
                    ],
            ];
        $result = $service->registerC2CCompany('C2C Vendor', '', '', $file);

        $this->assertInstanceOf(CompanyRegistrationResult::class, $result);

        $company = $result->getCompany();
        $this->assertInstanceOf(Company::class, $company);

        $this->assertSame('C2C Vendor', $company->getName());
        $this->assertSame('customer-3@world-company.com', $company->getEmail());
        $this->assertInstanceOf(CompanyRegistrationResult::class, $result);

        $files = $service->getCompanyFiles($company->getId());
        $this->assertCount(3, $files);
    }

    public function testCannotGetOtherCompanyInfoWithVendorAccount(): void
    {
        $this->expectException(ClientException::class);
        $this->buildUserCompanyService('vendor@world-company.com', 'password')->getCompany(1);
    }

    public function testAddACompanyImageAndDeleteIt(): void
    {
        $service = $this->buildUserCompanyService('vendor@wizaplace.com', 'password');
        $companyId = $service->getCompany(2)->getId();

        $file = $this->mockUploadedFile('favicon.png');

        $imageId = $service->updateCompanyImage($companyId, [
            'name'     => "file",
            'contents' => $file->getStream(),
            'filename' => $file->getClientFilename(),
        ]);
        $this->assertGreaterThan(0, $imageId);

        $companyImageId = $service->getCompanyImageId($companyId);
        $this->assertEquals(13, $companyImageId);

        $result = $service->deleteCompanyImage($companyId, $companyImageId);
        $this->assertEquals(true, $result["success"]);
        $this->assertEquals("Image ".$imageId." successfully deleted", $result["message"]);
    }

    public function testGettingAListOfDivisionsCountriesCode(): void
    {
        $service = $this->buildUserCompanyService('vendor@world-company.com', 'password-vendor');

        $countriesCodes = $service->getDivisionsCountriesCodes(3);
        $this->assertCount(200, $countriesCodes);
    }

    public function testGettingAListOfDivisionsBlacklists(): void
    {
        $service = $this->buildUserCompanyService('vendor@world-company.com', 'password-vendor');

        $divisions = $service->getDivisions(3, 'FR');
        $this->assertCount(125, $divisions);

        foreach ($divisions as $division) {
            switch ($division->getCode()) {
                case 'FR':
                case 'FR-ARA':
                case 'FR-01':
                case 'FR-03':
                    $this->assertEquals(true, $division->isEnabled());
                    $this->assertNull($division->getDisabledBy());
                    break;
                case 'FR-69':
                    $this->assertEquals(false, $division->isEnabled());
                    $this->assertInstanceOf(UserType::class, $division->getDisabledBy());
                    $this->assertEquals(UserType::VENDOR(), $division->getDisabledBy());
                    break;
            }
        }
    }

    public function testSettingDivisionsBlacklists(): void
    {
        $service = $this->buildUserCompanyService('vendor@world-company.com', 'password-vendor');

        $divisions = $service->putDivisions(3, 'FR', ['FR-03', 'FR-69']);
        $this->assertCount(125, $divisions);

        foreach ($divisions as $division) {
            switch ($division->getCode()) {
                case 'FR':
                case 'FR-ARA':
                case 'FR-03':
                case 'FR-69':
                    $this->assertEquals(true, $division->isEnabled());
                    $this->assertNull($division->getDisabledBy());
                    break;
                case 'FR-01':
                    $this->assertEquals(false, $division->isEnabled());
                    $this->assertInstanceOf(UserType::class, $division->getDisabledBy());
                    $this->assertEquals(UserType::VENDOR(), $division->getDisabledBy());
                    break;
            }
        }
    }

    public function testRegisterACompanyWithReturnNafCodeNull(): void
    {
        $companyRegistration = new CompanyRegistration('ACME-2019 Test Inc', 'acme-2019@example.com');
        $companyRegistration->setAddress('24 rue de la gare');
        $companyRegistration->setCapital('1 000 000 000 $');
        $companyRegistration->setCity('Lyon');
        $companyRegistration->setCountry('FR');
        $companyRegistration->setDescription('Super ACME company');
        $companyRegistration->setFax('01 02 03 04 05');
        $companyRegistration->setLegalStatus('SARL');
        $companyRegistration->setPhoneNumber('01 02 03 04 05 06');
        $companyRegistration->setRcs('RCS VANNES B 514 919 844');
        $companyRegistration->setVatNumber('12345678901');
        $companyRegistration->setZipcode('69009');
        $companyRegistration->setSiretNumber('732 829 320 00074');
        $companyRegistration->setSlug('acme-inc');
        $companyRegistration->setUrl('https://acme.example.com/');
        $companyRegistration->setIban("AD1200012030200359100100");
        $companyRegistration->setBic("AGFBFRCC");
        $companyRegistration->setExtra(['driving_license_number' => '654987321']);
        $companyService = $this->buildUserCompanyService('customer-4@world-company.com', 'password-customer-4');
        $result = $companyService->register($companyRegistration);
        $company = $result->getCompany();
        static::assertNull($company->getNafCode());
    }

    public function testRegisterACompanyWithIbanAndBic(): void
    {
        $companyRegistration = new CompanyRegistration('ACME-2019 Test Inc', 'acme-2019@example.com');
        $companyRegistration->setAddress('24 rue de la gare');
        $companyRegistration->setCapital('1 000 000 000 $');
        $companyRegistration->setCity('Lyon');
        $companyRegistration->setCountry('FR');
        $companyRegistration->setDescription('Super ACME company');
        $companyRegistration->setFax('01 02 03 04 05');
        $companyRegistration->setLegalStatus('SARL');
        $companyRegistration->setPhoneNumber('01 02 03 04 05 06');
        $companyRegistration->setRcs('RCS VANNES B 514 919 844');
        $companyRegistration->setVatNumber('12345678901');
        $companyRegistration->setZipcode('69009');
        $companyRegistration->setSiretNumber('732 829 320 00074');
        $companyRegistration->setSlug('acme-inc');
        $companyRegistration->setUrl('https://acme.example.com/');
        $companyRegistration->setIban("AD1200012030200359100100");
        $companyRegistration->setBic("AGFBFRCC");
        $companyRegistration->setExtra(['driving_license_number' => '654987321']);
        $companyService = $this->buildUserCompanyService('customer-4@world-company.com', 'password-customer-4');
        $result = $companyService->register($companyRegistration);
        $company = $result->getCompany();
        $this->assertSame("AD1200012030200359100100", $company->getIban());
        $this->assertSame("AGFBFRCC", $company->getBic());
    }

    public function testRegisterACompanyC2CWithIbanAndBic(): void
    {
        $companyService = $this->buildUserCompanyService('user@wizaplace.com', 'password');
        $result = $companyService->registerC2CCompany("Super nom", "AD1200012030200359100100", "AGFBFRCC");
        $company = $result->getCompany();
        $this->assertSame("AD1200012030200359100100", $company->getIban());
        $this->assertSame("AGFBFRCC", $company->getBic());
    }

    public function testRegisterACompanyC2CWithIbanAndBicAndAddress(): void
    {
        $companyService = $this->buildUserCompanyService('user@wizaplace.com', 'password');

        $companyRegistration = new CompanyC2CRegistration();
        $companyRegistration->setName("Super nom")
            ->setIban("AD1200012030200359100100")
            ->setBic("AGFBFRCC")
            ->setAddress("address")
            ->setZipcode("zipCode")
            ->setCity("city")
            ->setCountry("FR");

        $result = $companyService->register($companyRegistration);

        $company = $result->getCompany();
        $this->assertSame("AD1200012030200359100100", $company->getIban());
        $this->assertSame("AGFBFRCC", $company->getBic());
        $this->assertSame("address", $company->getAddress());
        $this->assertSame("zipCode", $company->getZipcode());
        $this->assertSame("city", $company->getCity());
        $this->assertSame("FR", $company->getCountry());
    }

    public function testRegisterACompanyWithIbanAndBicUnauthenticated(): void
    {
        $companyRegistration = new UnauthenticatedCompanyRegistration('ACME Test Inc', 'acme5@example.com', 'John', 'Doe');
        $companyRegistration->setIban('AD1200012030200359100100');
        $companyRegistration->setBic('AGFBFRCC');

        $result = (new CompanyService($this->buildApiClient()))->unauthenticatedRegister($companyRegistration);

        $company = $result->getCompany();
        $this->assertSame("AD1200012030200359100100", $company->getIban());
        $this->assertSame("AGFBFRCC", $company->getBic());
    }

    public function testUpdateCompanyMetadataWithAdminAccount(): void
    {
        $service = $this->buildUserCompanyService('admin@wizaplace.com', 'password');

        $company = $this->setCompanyMetadata(
            3,
            [
                'title' => 'Mon titre',
                'description' => 'Ma description',
                'keywords' => 'Mes mots-clés',
            ]
        );

        static::expectExceptionCode(Response::HTTP_FORBIDDEN);
        $service->update($company);
    }

    public function testGetCompanyMetadataWithAdminAccount(): void
    {
        $service = $this->buildUserCompanyService('admin@wizaplace.com', 'password');

        $company = $service->getCompany(3);

        static::assertEquals(3, $company->getId());
        static::assertSame('Title', $company->getMetadata()->getTitle());
        static::assertSame('Description', $company->getMetadata()->getDescription());
        static::assertSame('Keyword, Keyword2', $company->getMetadata()->getKeywords());
    }

    public function testUpdateAndGetCompanyMetadataWithGoodVendorAccount(): void
    {
        $service = $this->buildUserCompanyService('vendor@wizaplace.com', 'password');

        $company = $this->setCompanyMetadata(
            2,
            [
                'title' => 'Mon titre',
                'description' => 'Ma description',
                'keywords' => 'Mes mots-clés',
            ]
        );

        $newCompany = $service->update($company);

        static::assertEquals(2, $newCompany->getId());
        static::assertSame('Mon titre', $newCompany->getMetadata()->getTitle());
        static::assertSame('Ma description', $newCompany->getMetadata()->getDescription());
        static::assertSame('Mes mots-clés', $newCompany->getMetadata()->getKeywords());
    }

    public function testUpdateCompanyMetadataWithBadVendorAccount(): void
    {
        $service = $this->buildUserCompanyService('vendor@wizaplace.com', 'password');

        $company = $this->setCompanyMetadata(
            3,
            [
                'title' => 'Mon titre',
                'description' => 'Ma description',
                'keywords' => 'Mes mots-clés',
            ]
        );

        static::expectExceptionCode(Response::HTTP_FORBIDDEN);
        $service->update($company);
    }

    public function testGetCompanyMetadataWithBadVendorAccount(): void
    {
        $service = $this->buildUserCompanyService('vendor@wizaplace.com', 'password');

        static::expectExceptionCode(Response::HTTP_NOT_FOUND);
        $service->getCompany(3);
    }

    public function testListSubscriptionsBy(): void
    {
        $companyService = $this->buildUserCompanyService('vendor@wizaplace.com', 'password');
        $subscriptions = $companyService->listSubscriptionsBy(1);

        static::assertInstanceOf(PaginatedData::class, $subscriptions);
        static::assertEquals(10, $subscriptions->getLimit());
        static::assertEquals(0, $subscriptions->getOffset());
        static::assertEquals(1, $subscriptions->getTotal());
        static::assertCount(1, $subscriptions->getItems());

        /** @var SubscriptionSummary $subscription */
        $subscription = $subscriptions->getItems()[0];

        static::assertInstanceOf(SubscriptionSummary::class, $subscription);
        static::assertUuid($subscription->getId());
        static::assertEquals(1, $subscription->getUserId());
        static::assertEquals(1, $subscription->getCompanyId());
        static::assertUuid($subscription->getCardId());
    }

    public function testRegisteringACompanyWithNullNafCode(): void
    {
        $companyRegistration = new CompanyRegistration('ACME Test Inc', 'acme@example.com');
        $companyRegistration->setNafCode(null);

        $result = $this->buildUserCompanyService('customer-3@world-company.com', 'password-customer-3')
            ->register($companyRegistration)
        ;

        $company = $result->getCompany();
        static::assertGreaterThan(0, $company->getId());
        static::assertStringStartsWith('acme-test-inc', $company->getSlug());
        static::assertSame('acme@example.com', $company->getEmail());
        static::assertEmpty($company->getFax());
        static::assertNull($company->getNafCode());
    }

    private function buildUserCompanyService(
        string $email = 'customer-3@world-company.com',
        string $password = 'password-customer-3'
    ): CompanyService {
        $apiClient = $this->buildApiClient();
        $apiClient->authenticate($email, $password);

        return new CompanyService($apiClient);
    }

    private function setCompanyMetadata(int $companyId, array $metadata = []): CompanyUpdateCommand
    {
        return (new CompanyUpdateCommand($companyId))
            ->setMetaTitle($metadata['title'] ?? null)
            ->setMetaDescription($metadata['description'] ?? null)
            ->setMetaKeywords($metadata['keywords'] ?? null);
    }
}
