<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\SDK\Company;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\RequestOptions;
use Psr\Http\Message\ResponseInterface;
use Wizaplace\SDK\AbstractService;
use Wizaplace\SDK\Authentication\AuthenticationRequired;
use Wizaplace\SDK\Division\DivisionCompany;
use Wizaplace\SDK\Exception\NotFound;
use Wizaplace\SDK\PaginatedData;
use Wizaplace\SDK\Subscription\SubscriptionFilter;
use Wizaplace\SDK\Subscription\SubscriptionSummary;
use Wizaplace\SDK\Traits\AssertRessourceNotFoundTrait;

/**
 * Class CompanyService
 * @package Wizaplace\SDK\Company
 */
final class CompanyService extends AbstractService
{
    use AssertRessourceNotFoundTrait;

    /**
     * Only for an authenticated registration (normal and C2C) as we don't send legalRepresentativeFirstName and legalRepresentativeLastName properties
     * for unauthenticated registration, use unauthenticatedRegister() method, see API companies documentation.
     *
     * @param AbstractCompanyRegistration $companyRegistration
     *
     * @return CompanyRegistrationResult
     * @throws AuthenticationRequired
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Wizaplace\SDK\Exception\JsonDecodingError
     */
    public function register(AbstractCompanyRegistration $companyRegistration): CompanyRegistrationResult
    {
        $this->client->mustBeAuthenticated();

        $endpoint = $companyRegistration->isC2C() ? 'companies/c2c' : 'companies';

        $responseData = $this->client->post($endpoint, [
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
                'iban' => $companyRegistration->getIban(),
                'bic' => $companyRegistration->getBic(),
                'extra' => $companyRegistration->getExtra(),
                'nafCode' => $companyRegistration->getNafCode(),
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
     *
     * @deprecated
     */
    public function registerC2CCompany($companyName = '', ?string $iban = null, ?string $bic = null, array $files = []): CompanyRegistrationResult
    {
        @trigger_error('The method "registerC2CCompany" is deprecated, use "register" with CompanyC2CRegistration::class instead.', E_USER_DEPRECATED);

        $this->client->mustBeAuthenticated();

        $responseData = $this->client->post('companies/c2c', [
            RequestOptions::JSON => [
                'name' => $companyName,
                'iban' => $iban ?? "",
                'bic'  => $bic ?? "",
            ],
        ]);

        $company = new Company($responseData);

        //If parameter $files is not null,
        //we call uploadRegistrationFiles() method
        $fileUploadResults = [];
        if (!empty($files)) {
            $fileUploadResults = $this->uploadRegistrationFiles($company->getId(), $files);
        }

        return new CompanyRegistrationResult($company, $fileUploadResults);
    }

    /**
     * @param CompanyUpdateCommand $command
     *
     * @return Company
     * @throws AuthenticationRequired
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Wizaplace\SDK\Exception\JsonDecodingError
     */
    public function update(CompanyUpdateCommand $command): Company
    {
        $this->client->mustBeAuthenticated();
        $isNotNull =  function ($value): bool {
            if (is_array($value)) {
                return $value !== [];
            }

            return $value !== null;
        };

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
                        'nafCode' => $command->getNafCode(),
                        'meta' => array_filter(
                            [
                                'title' => $command->getMetaTitle(),
                                'description' => $command->getMetaDescription(),
                                'keywords' => $command->getMetaKeywords(),
                            ],
                            $isNotNull
                        ),
                    ],
                    $isNotNull
                ),
            ]
        );

        return new Company($responseData);
    }

    /**
     * Same as register() method but with legalRepresentativeFirstName and legalRepresentativeLastName properties
     * allowing registering a company without being authenticated (see API companies documentation)
     *
     * @param UnauthenticatedCompanyRegistration $companyRegistration
     *
     * @return CompanyRegistrationResult
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Wizaplace\SDK\Exception\JsonDecodingError
     */
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
                'iban' => $companyRegistration->getIban(),
                'bic' => $companyRegistration->getBic(),
                'legalRepresentativeFirstName' => $companyRegistration->getLegalRepresentativeFirstName(),
                'legalRepresentativeLastName' => $companyRegistration->getLegalRepresentativeLastName(),
                'extra' => $companyRegistration->getExtra(),
                'nafCode' => $companyRegistration->getNafCode(),
            ],
        ]);

        $company = new Company($responseData);

        $fileUploadResults = $this->uploadRegistrationFiles($company->getId(), $companyRegistration->getFiles());


        return new CompanyRegistrationResult($company, $fileUploadResults);
    }

    /**
     * Return the company matching the given ID
     *
     * @param int $companyId
     *
     * @return Company
     * @throws AuthenticationRequired
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Wizaplace\SDK\Exception\JsonDecodingError
     */
    public function getCompany(int $companyId) :Company
    {
        $this->client->mustBeAuthenticated();

        $companyData = $this->client->get('companies/'.$companyId);

        return new Company($companyData);
    }

    /**
     * @param int $companyId
     *
     * @return CompanyFile[]
     * @throws AuthenticationRequired
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Wizaplace\SDK\Exception\JsonDecodingError
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

    /**
     * @param CompanyFile $file
     *
     * @return ResponseInterface
     * @throws AuthenticationRequired
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
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
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Wizaplace\SDK\Exception\JsonDecodingError
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
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Wizaplace\SDK\Exception\JsonDecodingError
     */
    public function deleteFile(int $companyId, string $filename)
    {
        $this->client->mustBeAuthenticated();

        return $this->client->delete("companies/{$companyId}/files/{$filename}");
    }

    /**
     * @param int   $companyId
     * @param array $imageFile
     *
     * @return int
     * @throws AuthenticationRequired
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function updateCompanyImage(int $companyId, array $imageFile): int
    {
        $this->client->mustBeAuthenticated();

        return (int) $this->client->rawRequest("POST", "companies/{$companyId}/image", [
            RequestOptions::MULTIPART => [
                'file' => $imageFile,
            ],
        ])
            ->getBody()
            ->getContents();
    }

    /**
     * @param int $companyId
     * @param int $imageId
     *
     * @return mixed|null
     * @throws AuthenticationRequired
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Wizaplace\SDK\Exception\JsonDecodingError
     */
    public function deleteCompanyImage(int $companyId, int $imageId)
    {
        $this->client->mustBeAuthenticated();

        return $this->client->delete("companies/{$companyId}/image/{$imageId}");
    }

    /**
     * @param int $companyId
     *
     * @return int
     * @throws AuthenticationRequired
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Wizaplace\SDK\Exception\JsonDecodingError
     */
    public function getCompanyImageId(int $companyId)
    {
        $this->client->mustBeAuthenticated();

        return $this->client->get("companies/{$companyId}/image");
    }

    /**
     * Allow to get a list of countries codes of enabled divisions for the company
     *
     * @param int $companyId
     *
     * @return array
     * @throws AuthenticationRequired
     * @throws NotFound
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Wizaplace\SDK\Exception\JsonDecodingError
     */
    public function getDivisionsCountriesCodes(int $companyId): array
    {
        $this->client->mustBeAuthenticated();

        try {
            return $this->client->get("companies/{$companyId}/divisions");
        } catch (ClientException $e) {
            if ($e->getResponse()->getStatusCode() === 404) {
                throw new NotFound($e);
            }
            throw $e;
        }
    }

    /**
     * Allow to get a list of divsions enabled for the company
     *
     * @param int    $companyId
     * @param string $countryCode
     *
     * @return DivisionCompany[]
     * @throws AuthenticationRequired
     * @throws NotFound
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Wizaplace\SDK\Exception\JsonDecodingError
     */
    public function getDivisions(int $companyId, string $countryCode): array
    {
        $this->client->mustBeAuthenticated();

        try {
            return array_map(function ($datas) {
                return new DivisionCompany($datas);
            }, $this->client->get("companies/{$companyId}/divisions/{$countryCode}"));
        } catch (ClientException $e) {
            if ($e->getResponse()->getStatusCode() === 404) {
                throw new NotFound($e);
            }
            throw $e;
        }
    }

    /**
     * Allow to disable divisions for the company
     *
     * @param int    $companyId
     * @param string $countryCode
     * @param array  $codes
     *
     * @return DivisionCompany[]
     * @throws AuthenticationRequired
     * @throws NotFound
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Wizaplace\SDK\Exception\JsonDecodingError
     */
    public function putDivisions(int $companyId, string $countryCode, array $codes): array
    {
        $this->client->mustBeAuthenticated();

        try {
            $divisions = $this->client->put("companies/{$companyId}/divisions/{$countryCode}", [
                RequestOptions::FORM_PARAMS => [
                    'code' => $codes,
                ],
            ]);

            return array_map(function ($datas) {
                return new DivisionCompany($datas);
            }, $divisions);
        } catch (ClientException $e) {
            if ($e->getResponse()->getStatusCode() === 404) {
                throw new NotFound($e);
            }
            throw $e;
        }
    }

    /**
     * @param int                     $companyId
     * @param null|SubscriptionFilter $subscriptionFilter
     *
     * @return PaginatedData
     */
    public function listSubscriptionsBy(int $companyId, SubscriptionFilter $subscriptionFilter = null): PaginatedData
    {
        $this->client->mustBeAuthenticated();

        if (false === $subscriptionFilter instanceof SubscriptionFilter) {
            $subscriptionFilter = (new SubscriptionFilter())
                ->setLimit(10)
                ->setOffset(0);
        }

        return $this->assertRessourceNotFound(
            function () use ($companyId, $subscriptionFilter): PaginatedData {
                $response = $this->client->get(
                    "companies/{$companyId}/subscriptions",
                    [RequestOptions::QUERY => $subscriptionFilter->getFilters()]
                );

                return new PaginatedData(
                    $response['limit'],
                    $response['offset'],
                    $response['total'],
                    array_map(function (array $subscription): SubscriptionSummary {
                        return new SubscriptionSummary($subscription);
                    }, $response['items'])
                );
            },
            "Company '{$companyId}' not found."
        );
    }

    /**
     * @param int   $companyId
     * @param array $files {@see \Wizaplace\SDK\Company\CompanyRegistration::addFile}
     *
     * @return FileUploadResult[] a map of result by uploaded file.
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Wizaplace\SDK\Exception\JsonDecodingError
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
