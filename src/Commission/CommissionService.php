<?php

/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Commission;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\RequestOptions;
use Wizaplace\SDK\AbstractService;
use Wizaplace\SDK\Exception\AccessDenied;
use Wizaplace\SDK\Exception\Conflict;
use Wizaplace\SDK\Exception\NotFound;
use Wizaplace\SDK\Exception\SomeParametersAreInvalid;

class CommissionService extends AbstractService
{
    public function addMarketplaceCommission(Commission $commission): string
    {
        $this->client->mustBeAuthenticated();

        try {
            return $this->client->post(
                'commissions',
                [
                    RequestOptions::JSON => [
                        'percent' => $commission->getPercentAmount(),
                        'fixed' => $commission->getFixedAmount(),
                        'maximum' => $commission->getMaximumAmount(),
                    ],
                ]
            )['id'];
        } catch (ClientException $exception) {
            switch ($exception->getResponse()->getStatusCode()) {
                case 400:
                    throw new SomeParametersAreInvalid();
                case 403:
                    throw new AccessDenied('Access denied.');
                case 409:
                    throw new Conflict('The marketplace commission already exists.');
                default:
                    throw $exception;
            }
        }
    }

    public function addCategoryCommission(Commission $commission): string
    {
        $this->client->mustBeAuthenticated();

        try {
            return $this->client->post(
                'categories/' . $commission->getCategoryId() . '/commissions',
                [
                    RequestOptions::JSON => [
                        'company' => $commission->getCompanyId(),
                        'percent' => $commission->getPercentAmount(),
                        'fixed' => $commission->getFixedAmount(),
                        'maximum' => $commission->getMaximumAmount(),
                    ],
                ]
            )['id'];
        } catch (ClientException $exception) {
            switch ($exception->getResponse()->getStatusCode()) {
                case 400:
                    throw new SomeParametersAreInvalid();
                case 403:
                    throw new AccessDenied('Access denied.');
                case 404:
                    throw new NotFound('The category or company was not found');
                case 409:
                    throw new Conflict('The category commission already exists.');
                default:
                    throw $exception;
            }
        }
    }

    public function addCompanyCommission(Commission $commission): string
    {
        $this->client->mustBeAuthenticated();

        try {
            return $this->client->post(
                'companies/' . $commission->getCompanyId() . '/commissions',
                [
                    RequestOptions::JSON => [
                        'category' => $commission->getCategoryId(),
                        'percent' => $commission->getPercentAmount(),
                        'fixed' => $commission->getFixedAmount(),
                        'maximum' => $commission->getMaximumAmount(),
                    ],
                ]
            )['id'];
        } catch (ClientException $exception) {
            switch ($exception->getResponse()->getStatusCode()) {
                case 400:
                    throw new SomeParametersAreInvalid();
                case 403:
                    throw new AccessDenied('Access denied.');
                case 404:
                    throw new NotFound('The company or category was not found');
                case 409:
                    throw new Conflict('The company commission already exists.');
                default:
                    throw $exception;
            }
        }
    }

    /** @return mixed[] */
    public function updateMarketplaceCommission(Commission $commission): array
    {
        $this->client->mustBeAuthenticated();

        try {
            return $this->client->patch(
                'commissions/' . $commission->getId(),
                [
                    RequestOptions::JSON => [
                        'percent' => $commission->getPercentAmount(),
                        'fixed' => $commission->getFixedAmount(),
                        'maximum' => $commission->getMaximumAmount(),
                    ],
                ]
            );
        } catch (ClientException $exception) {
            switch ($exception->getResponse()->getStatusCode()) {
                case 400:
                    throw new SomeParametersAreInvalid();
                case 403:
                    throw new AccessDenied('Access denied.');
                case 404:
                    throw new NotFound('The marketplace commission was not found.');
                default:
                    throw $exception;
            }
        }
    }

    public function updateCategoryCommission(Commission $commission): array
    {
        $this->client->mustBeAuthenticated();

        try {
            return $this->client->patch(
                'categories/' . $commission->getCategoryId() . '/commissions/' . $commission->getId(),
                [
                    RequestOptions::JSON => [
                        'company' => $commission->getCompanyId(),
                        'percent' => $commission->getPercentAmount(),
                        'fixed' => $commission->getFixedAmount(),
                        'maximum' => $commission->getMaximumAmount(),
                    ],
                ]
            );
        } catch (ClientException $exception) {
            switch ($exception->getResponse()->getStatusCode()) {
                case 400:
                    throw new SomeParametersAreInvalid();
                case 403:
                    throw new AccessDenied('Access denied.');
                case 404:
                    throw new NotFound('The category commission was not found.');
                default:
                    throw $exception;
            }
        }
    }

    /** @return mixed[] */
    public function updateCompanyCommission(Commission $commission): array
    {
        $this->client->mustBeAuthenticated();

        try {
            return $this->client->patch(
                'companies/' . $commission->getCompanyId() . '/commissions/' . $commission->getId(),
                [
                    RequestOptions::JSON => [
                        'category' => $commission->getCategoryId(),
                        'percent' => $commission->getPercentAmount(),
                        'fixed' => $commission->getFixedAmount(),
                        'maximum' => $commission->getMaximumAmount(),
                    ],
                ]
            );
        } catch (ClientException $exception) {
            switch ($exception->getResponse()->getStatusCode()) {
                case 400:
                    throw new SomeParametersAreInvalid();
                case 403:
                    throw new AccessDenied('Access denied.');
                case 404:
                    throw new NotFound('The company commission was not found.');
                default:
                    throw $exception;
            }
        }
    }

    /** @return Commission[] */
    public function getCommissions(): array
    {
        $this->client->mustBeAuthenticated();

        try {
            $commissions = $this->client->get('commissions');

            return \array_map(
                function ($commission): Commission {
                    return new Commission($commission);
                },
                $commissions
            );
        } catch (ClientException $exception) {
            switch ($exception->getResponse()->getStatusCode()) {
                case 403:
                    throw new AccessDenied('Access denied.');
                default:
                    throw $exception;
            }
        }
    }

    public function getMarketplaceCommission(): Commission
    {
        $this->client->mustBeAuthenticated();

        try {
            return new Commission($this->client->get('commissions/default'));
        } catch (ClientException $exception) {
            switch ($exception->getResponse()->getStatusCode()) {
                case 403:
                    throw new AccessDenied('Access denied.');
                case 404:
                    throw new NotFound('The marketplace commission was not found.');
                default:
                    throw $exception;
            }
        }
    }

    public function getCommission(string $commissionId): Commission
    {
        $this->client->mustBeAuthenticated();

        try {
            return new Commission($this->client->get("commissions/$commissionId"));
        } catch (ClientException $exception) {
            switch ($exception->getResponse()->getStatusCode()) {
                case 403:
                    throw new AccessDenied('Access denied.');
                case 404:
                    throw new NotFound("The commission '$commissionId' was not found.");
                default:
                    throw $exception;
            }
        }
    }

    /** @return Commission[] */
    public function getCategoryCommissions(int $categoryId): array
    {
        $this->client->mustBeAuthenticated();

        try {
            $commissions = $this->client->get("categories/$categoryId/commissions");

            return \array_map(
                function ($commission) {
                    return new Commission($commission);
                },
                $commissions
            );
        } catch (ClientException $exception) {
            switch ($exception->getResponse()->getStatusCode()) {
                case 403:
                    throw new AccessDenied('Access denied.');
                case 404:
                    throw new NotFound("The category '$categoryId' was not found.");
                default:
                    throw $exception;
            }
        }
    }

    /** @return Commission[] */
    public function getCompanyCommissions(int $companyId): array
    {
        $this->client->mustBeAuthenticated();

        try {
            $commissions = $this->client->get("companies/$companyId/commissions");

            return \array_map(
                function ($commission): Commission {
                    return new Commission($commission);
                },
                $commissions
            );
        } catch (ClientException $exception) {
            switch ($exception->getResponse()->getStatusCode()) {
                case 403:
                    throw new AccessDenied('Access denied.');
                case 404:
                    throw new NotFound("The company '$companyId' was not found.");
                default:
                    throw $exception;
            }
        }
    }

    /** @return mixed[] */
    public function deleteCommission(string $commissionId): array
    {
        $this->client->mustBeAuthenticated();

        return $this->client->delete("commissions/$commissionId");
    }
}
