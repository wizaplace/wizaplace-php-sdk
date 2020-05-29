<?php

/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */

namespace Wizaplace\SDK\Division;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\RequestOptions;
use Symfony\Component\HttpFoundation\Response;
use Wizaplace\SDK\AbstractService;
use Wizaplace\SDK\Exception\AccessDenied;
use Wizaplace\SDK\Exception\FeatureNotEnabled;
use Wizaplace\SDK\Exception\NotFound;

/**
 * Class DivisionService
 * @package Wizaplace\SDK\Division
 */
class DivisionService extends AbstractService
{
    use DivisionsTreeTrait;

    /** @var string Root Division Code */
    public const ROOT_DIVISION = 'ALL';

    /** @var string Exception messages */
    private const MSG_YOU_MUST_BE_AUTHENTICATED = "You must be authenticated as an admin.";

    public function getDivisionsSettings(): DivisionSettings
    {
        $this->client->mustBeAuthenticated();

        try {
            return new DivisionSettings($this->client->get("divisions"));
        } catch (ClientException $previousException) {
            switch ($previousException->getResponse()->getStatusCode()) {
                case Response::HTTP_FORBIDDEN:
                    $exception = new AccessDenied(static::MSG_YOU_MUST_BE_AUTHENTICATED);
                    break;
                default:
                    $exception = $previousException;
            }

            throw $exception;
        }
    }

    public function patchDivisionsSettings(DivisionSettings $divisionSettings): void
    {
        $this->client->mustBeAuthenticated();

        try {
            $this->client->patch(
                "divisions",
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
                default:
                    $exception = $previousException;
            }

            throw $exception;
        }
    }

    /** @return Division[] a Division tree, see item's `parent` and `children` properties to navigate in the tree */
    public function getDivisionsTree(DivisionsTreeFilters $divisionsTreeFilters = null): array
    {
        try {
            return $this->getDivisionsTreeByUrl(
                'divisions-tree',
                $divisionsTreeFilters
            );
        } catch (ClientException $previousException) {
            switch ($previousException->getResponse()->getStatusCode()) {
                case Response::HTTP_FORBIDDEN:
                    $exception = new AccessDenied(static::MSG_YOU_MUST_BE_AUTHENTICATED);
                    break;
                default:
                    $exception = $previousException;
            }

            throw $exception;
        }
    }
}
