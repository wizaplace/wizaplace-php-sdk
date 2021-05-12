<?php

/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Tests\Company;

use Wizaplace\SDK\Company\Company;
use Wizaplace\SDK\Company\CompanyPerson;
use Wizaplace\SDK\Company\CompanyPersonService;
use Wizaplace\SDK\Company\CompanyPersonType;
use Wizaplace\SDK\Company\CompanyRegistration;
use Wizaplace\SDK\Company\CompanyService;
use Wizaplace\SDK\Tests\ApiTestCase;
use Wizaplace\SDK\User\UserTitle;

/**
 * @see CompanyPersonService
 */
final class CompanyPersonServiceTest extends ApiTestCase
{
    public function testAddCompanyPersonToVendor(): void
    {
        $companyService = $this->buildUserCompanyService('vendor@world-company.com', 'password-vendor');
        $companyPersonService = $this->buildUserCompanyPersonService('vendor@world-company.com', 'password-vendor');
        $company = $companyService->getCompany(3);

        $companyPerson = new CompanyPerson($this->getPostCompanyPersonData());
        $companyPerson = $companyPersonService->addCompanyPerson($company->getId(), $companyPerson);

        $this->assertSameFields($companyPerson);
    }

    public function testUpdateCompanyPersonToVendor(): void
    {
        $companyService = $this->buildUserCompanyService('vendor@world-company.com', 'password-vendor');
        $companyPersonService = $this->buildUserCompanyPersonService('vendor@world-company.com', 'password-vendor');
        $company = $companyService->getCompany(3);

        $data = $this->getPostCompanyPersonData();
        $companyPerson = new CompanyPerson($data);
        $companyPerson = $companyPersonService->addCompanyPerson($company->getId(), $companyPerson);

        $data['firstname'] = 'Sabrine';
        $data['lastname'] = 'WIZA';

        $companyPersonForPut = new CompanyPerson($data);
        $companyPerson = $companyPersonService->updateCompanyPerson(
            $company->getId(),
            $companyPerson->getId(),
            $companyPersonForPut
        );

        static::assertSame('Sabrine', $companyPerson->getFirstName());
        static::assertSame('WIZA', $companyPerson->getLastName());
    }

    public function testGetCompanyPersonList(): void
    {
        $companyService = $this->buildUserCompanyService('vendor@world-company.com', 'password-vendor');
        $companyPersonService = $this->buildUserCompanyPersonService('vendor@world-company.com', 'password-vendor');
        $company = $companyService->getCompany(3);

        $companyPerson = new CompanyPerson($this->getPostCompanyPersonData());
        $companyPersonService->addCompanyPerson($company->getId(), $companyPerson);
        $companyPersonList = $companyPersonService->getCompanyPersonList($company->getId());

        $this->assertSameFields($companyPersonList[0]);
    }

    public function testDeleteCompanyPerson(): void
    {
        $companyService = $this->buildUserCompanyService('vendor@world-company.com', 'password-vendor');
        $companyPersonService = $this->buildUserCompanyPersonService('vendor@world-company.com', 'password-vendor');
        $company = $companyService->getCompany(3);

        $companyPerson = new CompanyPerson($this->getPostCompanyPersonData());
        $companyPerson = $companyPersonService->addCompanyPerson($company->getId(), $companyPerson);

        $companyPersonService->deleteCompanyPerson($company->getId(), $companyPerson->getId());

        $companyPersonList = $companyPersonService->getCompanyPersonList($company->getId());

        static::assertCount(0, $companyPersonList);
    }

    public function testSubmitCompanyUBOs(): void
    {
        $companyService = $this->buildUserCompanyService('vendor@world-company.com', 'password-vendor');
        $companyPersonService = $this->buildUserCompanyPersonService('vendor@world-company.com', 'password-vendor');
        $company = $companyService->getCompany(3);
        $companyPerson = new CompanyPerson($this->getPostCompanyPersonData());
        $companyPersonService->addCompanyPerson($company->getId(), $companyPerson);

        $response = $companyPersonService->submitCompanyUBO($company->getId());
        static::assertSame('UBO submission successfully completed, validation in progress', $response);
    }

    private function buildUserCompanyPersonService(
        string $email = 'customer-3@world-company.com',
        string $password = 'password-customer-3'
    ): CompanyPersonService {
        $apiClient = $this->buildApiClient();
        $apiClient->authenticate($email, $password);

        return new CompanyPersonService($apiClient);
    }

    private function buildUserCompanyService(
        string $email = 'customer-3@world-company.com',
        string $password = 'password-customer-3'
    ): CompanyService {
        $apiClient = $this->buildApiClient();
        $apiClient->authenticate($email, $password);

        return new CompanyService($apiClient);
    }

    private function getPostCompanyPersonData(): array
    {
        return [
            "firstname" => "Joe",
            "lastname" => "David",
            "title" => UserTitle::MR()->getValue(),
            "address" => "Rue de madrid",
            "address2" => "A coté de poste",
            "city" => "lyon",
            "zipcode" => "69001",
            "state" => "FR",
            "country" => "FR",
            "nationalities" => ["FR"],
            "birthdate" => "1988-07-01",
            "birthplaceCity" => "lyon",
            "birthplaceCountry" => "FR",
            "type" => CompanyPersonType::OWNER()->getValue(),
            "ownershipPercentage" => 25
        ];
    }

    private function assertSameFields(CompanyPerson $companyPerson): void
    {
        static::assertSame('Joe', $companyPerson->getFirstName());
        static::assertSame('David', $companyPerson->getLastName());
        static::assertSame(UserTitle::MR()->getValue(), $companyPerson->getTitle());
        static::assertSame('Rue de madrid', $companyPerson->getAddress());
        static::assertSame('A coté de poste', $companyPerson->getAddress2());
        static::assertSame('lyon', $companyPerson->getCity());
        static::assertSame('69001', $companyPerson->getZipCode());
        static::assertSame('FR', $companyPerson->getCountry());
        static::assertSame('FR', $companyPerson->getNationalities()[0]);
        static::assertSame('1988-07-01T00:00:00+02:00', $companyPerson->getBirthdate());
        static::assertSame('lyon', $companyPerson->getBirthplaceCity());
        static::assertSame('FR', $companyPerson->getBirthplaceCountry());
        static::assertSame(CompanyPersonType::OWNER()->getValue(), $companyPerson->getType());
        static::assertSame(25.0, $companyPerson->getOwnershipPercentage());
    }
}
