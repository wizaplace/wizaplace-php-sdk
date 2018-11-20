<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\SDK\Company;

use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\RequestOptions;
use Psr\Http\Message\ResponseInterface;
use Wizaplace\SDK\AbstractService;
use Wizaplace\SDK\Authentication\AuthenticationRequired;
use Wizaplace\SDK\Exception\CompanyNotFound;

final class CompanyService extends AbstractService
{
    /**
     * @throws AuthenticationRequired
     */
    public function register(CompanyRegistration $companyRegistration): CompanyRegistrationResult
    {
        $this->client->mustBeAuthenticated();

        $responseData = $this->client->post('companies', [
            RequestOptions::JSON => [
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
                'extra' => $companyRegistration->getExtra(),
            ],
        ]);

        $company = new Company($responseData);

        $fileUploadResults = $this->uploadRegistrationFiles($company->getId(), $companyRegistration->getFiles());


        return new CompanyRegistrationResult($company, $fileUploadResults);
    }

    /**
     * Register a new C2C company (Customer-To-Customer, aka private individual).
     *
     * @throws AuthenticationRequired
     */
    public function registerC2CCompany($companyName = ''): CompanyRegistrationResult
    {
        $this->client->mustBeAuthenticated();

        $responseData = $this->client->post('companies/c2c', [
            RequestOptions::JSON => [
                'name' => $companyName,
            ],
        ]);

        $company = new Company($responseData);

        return new CompanyRegistrationResult($company, []);
    }

    /**
     * @throws AuthenticationRequired
     * @throws CompanyNotFound
     * @throws BadResponseException
     */
    public function update(CompanyUpdateCommand $command): Company
    {
        $this->client->mustBeAuthenticated();

        $responseData = $this->client->put(
            'companies/'.$command->getCompanyId(),
            [
                RequestOptions::JSON => array_filter(
                    [
                        'name' => $command->getName(),
                        'email' => $command->getEmail(),
                        'description' => $command->getDescription(),
                        'slug' => $command->getSlug(),
                        'address' => $command->getAddress(),
                        'country' => $command->getCountry(),
                        'zipcode' => $command->getZipcode(),
                        'city' => $command->getCity(),
                        'phoneNumber' => $command->getPhoneNumber(),
                        'url' => $command->getUrl(),
                        'fax' => $command->getFax(),
                        'vatNumber' => $command->getVatNumber(),
                        'siretNumber' => $command->getSiretNumber(),
                        'rcs' => $command->getRcs(),
                        'legalStatus' => $command->getLegalStatus(),
                        'capital' => $command->getCapital(),
                        'extra' => $command->getExtra(),
                    ],
                    static function ($value): bool {
                        return $value !== null;
                    }
                ),
            ]
        );

        return new Company($responseData);
    }

    public function unauthenticatedRegister(UnauthenticatedCompanyRegistration $companyRegistration): CompanyRegistrationResult
    {
        $responseData = $this->client->post('companies', [
            RequestOptions::JSON => [
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
                'legalRepresentativeFirstName' => $companyRegistration->getLegalRepresentativeFirstName(),
                'legalRepresentativeLastName' => $companyRegistration->getLegalRepresentativeLastName(),
                'extra' => $companyRegistration->getExtra(),
            ],
        ]);

        $company = new Company($responseData);

        $fileUploadResults = $this->uploadRegistrationFiles($company->getId(), $companyRegistration->getFiles());


        return new CompanyRegistrationResult($company, $fileUploadResults);
    }

    /**
     * Return the company matching the given ID
     * @param int $companyId
     * @return Company
     * @throws AuthenticationRequired
     */
    public function getCompany(int $companyId) :Company
    {
        $this->client->mustBeAuthenticated();

        $companyData = $this->client->get('companies/'.$companyId);

        return new Company($companyData);
    }

    /**
     * @return CompanyFile[]
     */
    public function getCompanyFiles(int $companyId) :array
    {
        $this->client->mustBeAuthenticated();

        $files = $this->client->get('companies/'.$companyId.'/files');
        $return = [];
        foreach ($files as $file) {
            $return[] = new CompanyFile($companyId, $file);
        }

        return $return;
    }

    public function fetchFile(CompanyFile $file) :ResponseInterface
    {
        $this->client->mustBeAuthenticated();

        return $this->client->rawRequest('GET', "companies/{$file->getCompanyId()}/files/{$file->getFilename()}");
    }

    /**
     * Allow to update an existing company registration file
     *
     * @param int    $companyId
     * @param string $filename
     * @param array  $file
     *
     * @return mixed|null
     * @throws AuthenticationRequired
     * @throws \Exception
     */
    public function updateFile(int $companyId, string $filename, array $file)
    {
        $this->client->mustBeAuthenticated();

        return $this->client->post("companies/{$companyId}/files/{$filename}", [
            RequestOptions::MULTIPART => [
                $filename => $file,
            ],
        ]);
    }

    /**
     * Allow to delete an existing company registration file
     *
     * @param int    $companyId
     * @param string $filename
     *
     * @return mixed|null
     * @throws AuthenticationRequired
     */
    public function deleteFile(int $companyId, string $filename)
    {
        $this->client->mustBeAuthenticated();

        return $this->client->delete("companies/{$companyId}/files/{$filename}");
    }

    /**
     * Allow to get a list of countries codes of enabled divisions for the company
     *
     * @param int $companyId
     *
     * @return array
     * @throws AuthenticationRequired
     */
    public function getDivisionsCountriesCodes(int $companyId): array
    {
        $this->client->mustBeAuthenticated();

        return $this->client->get("companies/{$companyId}/divisions");
    }

    /**
     * @param array $files {@see \Wizaplace\SDK\Company\CompanyRegistration::addFile}
     * @return FileUploadResult[] a map of result by uploaded file.
     */
    private function uploadRegistrationFiles(int $companyId, array $files): array
    {
        if (empty($files)) {
            return [];
        }

        $responseData = $this->client->post("companies/$companyId/files", [
            RequestOptions::MULTIPART => $files,
        ]);

        return array_map(static function (array $data) {
            return new FileUploadResult($data['error'] ?? null);
        }, $responseData);
    }
}
