<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\Company;

use Wizaplace\AbstractService;
use Wizaplace\Authentication\AuthenticationRequired;

class CompanyService extends AbstractService
{
    /**
     * @throws AuthenticationRequired
     */
    public function register(CompanyRegistration $companyRegistration): CompanyRegistrationResult
    {
        $this->client->mustBeAuthenticated();

        $responseData = $this->client->post('companies', [
            'json' => [
                'name' => $companyRegistration->getName(),
                'email' => $companyRegistration->getEmail(),
                'description' => $companyRegistration->getDescription(),
                'slug' => $companyRegistration->getSlug(),
                'address' => $companyRegistration->getAddress(),
                'country' => $companyRegistration->getCountry(),
                'zipcode' => $companyRegistration->getZipcode(),
                'city' => $companyRegistration->getCity(),
                'phoneNumber' => $companyRegistration->getPhoneNumber(),
                'url' => $companyRegistration->getUrl(),
                'fax' => $companyRegistration->getFax(),
                'vatNumber' => $companyRegistration->getVatNumber(),
                'siretNumber' => $companyRegistration->getSiretNumber(),
                'rcs' => $companyRegistration->getRcs(),
                'legalStatus' => $companyRegistration->getLegalStatus(),
                'capital' => $companyRegistration->getCapital(),
            ],
        ]);

        $company = new Company($responseData);

        $fileUploadResults = $this->uploadRegistrationFiles($company->getId(), $companyRegistration->getFiles());


        return new CompanyRegistrationResult($company, $fileUploadResults);
    }

    /**
     * @param array $files {@see \Wizaplace\Company\CompanyRegistration::addFile}
     * @return FileUploadResult[] a map of result by uploaded file.
     */
    private function uploadRegistrationFiles(int $companyId, array $files): array
    {
        if (empty($files)) {
            return [];
        }

        $this->client->mustBeAuthenticated();

        $responseData = $this->client->post("companies/$companyId/files", [
            'multipart' => $files,
        ]);

        return array_map(function (array $data) {
            return new FileUploadResult($data['error'] ?? null);
        }, $responseData);
    }
}
