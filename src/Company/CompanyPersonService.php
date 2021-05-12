<?php

/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Company;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\RequestOptions;
use Wizaplace\SDK\AbstractService;
use Wizaplace\SDK\Exception\AccessDenied;
use Wizaplace\SDK\Exception\NotFound;
use Wizaplace\SDK\Exception\SomeParametersAreInvalid;

/**
 * Class CompanyPersonService
 * @package Wizaplace\SDK\Company
 */
final class CompanyPersonService extends AbstractService
{
    public function addCompanyPerson(int $companyId, CompanyPerson $companyPerson): CompanyPerson
    {
        try {
            $response = $this->client->post(
                "companies/{$companyId}/persons",
                [
                    RequestOptions::JSON => $companyPerson->jsonSerialize(),
                ]
            );

            return new CompanyPerson($response);
        } catch (ClientException $e) {
            switch ($e->getResponse()->getStatusCode()) {
                case 403:
                    throw new AccessDenied($e->getMessage());
                case 404:
                    throw new NotFound($e->getMessage());
                case 400:
                    throw new SomeParametersAreInvalid($e->getMessage());
                default:
                    throw $e;
            }
        }
    }

    public function updateCompanyPerson(int $companyId, int $personId, CompanyPerson $companyPerson): CompanyPerson
    {
        try {
            $response = $this->client->put(
                "companies/{$companyId}/persons/{$personId}",
                [
                    RequestOptions::JSON => $companyPerson->jsonSerialize(),
                ]
            );

            return new CompanyPerson($response);
        } catch (ClientException $e) {
            switch ($e->getResponse()->getStatusCode()) {
                case 403:
                    throw new AccessDenied($e->getMessage());
                case 404:
                    throw new NotFound($e->getMessage());
                case 400:
                    throw new SomeParametersAreInvalid($e->getMessage());
                default:
                    throw $e;
            }
        }
    }

    public function getCompanyPersonList(int $companyId): array
    {
        try {
            $companyPersonList = $this->client->get("companies/{$companyId}/persons");

            return array_map(function (array $companyPerson) {
                return new CompanyPerson($companyPerson);
            }, $companyPersonList);
        } catch (ClientException $e) {
            switch ($e->getResponse()->getStatusCode()) {
                case 403:
                    throw new AccessDenied($e->getMessage());
                case 404:
                    throw new NotFound($e->getMessage());
                default:
                    throw $e;
            }
        }
    }

    public function deleteCompanyPerson(int $companyId, int $personId): void
    {
        try {
            $this->client->delete("companies/{$companyId}/persons/{$personId}");
        } catch (ClientException $e) {
            switch ($e->getResponse()->getStatusCode()) {
                case 403:
                    throw new AccessDenied($e->getMessage());
                case 404:
                    throw new NotFound($e->getMessage());
                default:
                    throw $e;
            }
        }
    }

    public function submitCompanyUBO(int $companyId): string
    {
        try {
            return $this->client->post("companies/{$companyId}/validate-ubo");
        } catch (ClientException $e) {
            switch ($e->getResponse()->getStatusCode()) {
                case 403:
                    throw new AccessDenied($e->getMessage());
                case 404:
                    throw new NotFound($e->getMessage());
                case 400:
                    throw new SomeParametersAreInvalid($e->getMessage());
                default:
                    throw $e;
            }
        }
    }
}
