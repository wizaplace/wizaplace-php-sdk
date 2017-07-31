<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\Tests\Company;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Stream;
use Wizaplace\Authentication\AuthenticationRequired;
use Wizaplace\Company\CompanyRegistration;
use Wizaplace\Company\CompanyService;
use Wizaplace\Tests\ApiTestCase;

/**
 * @see CompanyService
 */
class CompanyServiceTest extends ApiTestCase
{
    public function testRegisteringACompanyWithAllInformation()
    {
        $companyRegistration = new CompanyRegistration('ACME2 Test Inc', 'acme2@example.com');
        $companyRegistration->setAddress('24 rue de la gare');
        $companyRegistration->setCapital('1 000 000 000 $');
        $companyRegistration->setCity('Lyon');
        $companyRegistration->setCountry('France');
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

        $company = $this->buildUserCompanyService()->register($companyRegistration);
        $this->assertGreaterThan(0, $company->getId());
        $this->assertEquals('acme-inc', $company->getSlug());
        $this->assertEquals('acme2@example.com', $company->getEmail());
        $this->assertEquals('24 rue de la gare', $companyRegistration->getAddress());
        $this->assertEquals('1 000 000 000 $', $companyRegistration->getCapital());
        $this->assertEquals('Lyon', $companyRegistration->getCity());
        $this->assertEquals('France', $companyRegistration->getCountry());
        $this->assertEquals('Super ACME company', $companyRegistration->getDescription());
        $this->assertEquals('01 02 03 04 05', $companyRegistration->getFax());
        $this->assertEquals('SARL', $companyRegistration->getLegalStatus());
        $this->assertEquals('01 02 03 04 05 06', $companyRegistration->getPhoneNumber());
        $this->assertEquals('RCS VANNES B 514 919 844', $companyRegistration->getRcs());
        $this->assertEquals('12345678901', $companyRegistration->getVatNumber());
        $this->assertEquals('69009', $companyRegistration->getZipcode());
        $this->assertEquals('732 829 320 00074', $companyRegistration->getSiretNumber());
        $this->assertEquals('https://acme.example.com/', $companyRegistration->getUrl());
    }

    public function testRegisteringACompanyWithMinimalInformation()
    {
        $companyRegistration = new CompanyRegistration('ACME Test Inc', 'acme@example.com');

        $company = $this->buildUserCompanyService()->register($companyRegistration);
        $this->assertGreaterThan(0, $company->getId());
        $this->assertStringStartsWith('acme-test-inc', $company->getSlug());
        $this->assertEquals('acme@example.com', $company->getEmail());
        $this->assertEmpty($company->getFax());
    }

    public function testRegisteringACompanyWithInvalidEmail()
    {
        $companyRegistration = new CompanyRegistration('ACME Test Inc', 'acme@@example.com');

        $this->expectException(ClientException::class); // @TODO: decorate?
        $this->buildUserCompanyService()->register($companyRegistration);
    }

    public function testRegisteringACompanyWithEmptyRequiredFields()
    {
        $this->expectException(ClientException::class);
        $this->expectExceptionCode(400);
        $this->buildUserCompanyService()->register(new CompanyRegistration('', ''));
    }

    public function testRegisteringACompanyAnonymously()
    {
        $this->expectException(AuthenticationRequired::class);
        (new CompanyService($this->buildApiClient()))->register(new CompanyRegistration('doesntmatter', 'really'));
    }

    public function testUploadingRegistrationFiles()
    {
        $companyRegistration = new CompanyRegistration('ACM3 Test Inc', 'acme3@example.com');
        $companyService = $this->buildUserCompanyService();

        $company = $companyService->register($companyRegistration);
        $this->assertGreaterThan(0, $company->getId());

        $files = [
            'rib' => __DIR__.'/../fixtures/files/minimal.pdf',
            'idCard' => fopen(__DIR__.'/../fixtures/files/minimal.pdf', 'r'),
            'addressProof' => new Stream(fopen(__DIR__.'/../fixtures/files/minimal.pdf', 'r')),
        ];
        $results = $companyService->uploadRegistrationFiles($company->getId(), $files);

        $this->assertCount(count($files), $results);
        foreach ($files as $name => $file) {
            $this->assertArrayHasKey($name, $results);

            $this->assertTrue($results[$name]->isSuccess());
            $this->assertNull($results[$name]->getErrorMessage());
        }
    }

    public function testUploadingBadExtensionRegistrationFiles()
    {
        $companyRegistration = new CompanyRegistration('4CME Test Inc', 'acme4@example.com');
        $companyService = $this->buildUserCompanyService();

        $company = $companyService->register($companyRegistration);
        $this->assertGreaterThan(0, $company->getId());

        $files = [
            'rib' => __DIR__.'/../fixtures/files/dummy.txt',
        ];
        $results = $companyService->uploadRegistrationFiles($company->getId(), $files);

        $this->assertCount(count($files), $results);
        foreach ($files as $name => $file) {
            $this->assertArrayHasKey($name, $results);

            $this->assertFalse($results[$name]->isSuccess());
            $this->assertEquals('Invalid file', $results[$name]->getErrorMessage());
        }
    }

    private function buildUserCompanyService(): CompanyService
    {
        $apiClient = $this->buildApiClient();
        $apiClient->authenticate('user@wizaplace.com', 'password');

        return new CompanyService($apiClient);
    }
}
