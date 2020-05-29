<?php

/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Company;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\RequestOptions;
use Symfony\Component\HttpFoundation\Response;
use Wizaplace\SDK\AbstractService;
use Wizaplace\SDK\Division\Division;
use Wizaplace\SDK\Division\DivisionSettings;
use Wizaplace\SDK\Division\DivisionsTreeFilters;
use Wizaplace\SDK\Division\DivisionsTreeTrait;
use Wizaplace\SDK\Exception\AccessDenied;
use Wizaplace\SDK\Exception\NotFound;

class DivisionService extends AbstractService
{
    use DivisionsTreeTrait;

    /** @var string Exception messages */
    private const MSG_YOU_MUST_BE_AUTHENTICATED = "You must be authenticated as an admin or vendor.";

    /** @var string Exception messages */
    private const MSG_COMPANY_NOT_FOUND = "Company '%s' not found.";

    public function getDivisionsSettings(int $companyId): DivisionSettings
    {
        $this->client->mustBeAuthenticated();

        try {
            return new DivisionSettings($this->client->get("companies/{$companyId}/divisions"));
        } catch (ClientException $previousException) {
            switch ($previousException->getResponse()->getStatusCode()) {
                case Response::HTTP_FORBIDDEN:
                    $exception = new AccessDenied(static::MSG_YOU_MUST_BE_AUTHENTICATED);
                    break;
                case Response::HTTP_NOT_FOUND:
                    $exception = new NotFound(
                        sprintf(static::MSG_COMPANY_NOT_FOUND, $companyId)
                    );
                    break;
                default:
                    $exception = $previousException;
            }
            throw $exception;
        }
    }

    public function patchDivisionsSettings(int $companyId, DivisionSettings $divisionSettings): void
    {
        $this->client->mustBeAuthenticated();

        try {
            $this->client->patch(
                "companies/{$companyId}/divisions",
                [
                    RequestOptions::JSON => [
                        'included' => $divisionSettings->getIncluded(),
                        'excluded' => $divisionSettings->getExcluded(),
                    ],
                ]
            );
        } catch (ClientException $previousException) {
            switch ($previousException->getResponse()->getStatusCode()) {
                case Response::HTTP_FORBIDDEN:
                    $exception = new AccessDenied(static::MSG_YOU_MUST_BE_AUTHENTICATED);
                    break;
                case Response::HTTP_NOT_FOUND:
                    $exception = new NotFound(
                        sprintf(static::MSG_COMPANY_NOT_FOUND, $companyId)
                    );
                    break;
                default:
                    $exception = $previousException;
            }
            throw $exception;
        }
    }

    /** @return Division[] a Division tree, see item's `parent` and `children` properties to navigate in the tree */
    public function getDivisionsTree(int $companyId, DivisionsTreeFilters $divisionsTreeFilters = null): array
    {
        $this->client->mustBeAuthenticated();

        try {
            return $this->getDivisionsTreeByUrl(
                "companies/{$companyId}/divisions-tree",
                $divisionsTreeFilters
            );
        } catch (ClientException $previousException) {
            switch ($previousException->getResponse()->getStatusCode()) {
                case Response::HTTP_FORBIDDEN:
                    $exception = new AccessDenied(static::MSG_YOU_MUST_BE_AUTHENTICATED);
                    break;
                case Response::HTTP_NOT_FOUND:
                    $exception = new NotFound(
                        sprintf(static::MSG_COMPANY_NOT_FOUND, $companyId)
                    );
                    break;
                default:
                    $exception = $previousException;
            }

            throw $exception;
        }
    }
}
