<?php

/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Pim\Product;

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

    public function getDivisionsSettings(int $productId): DivisionSettings
    {
        $this->client->mustBeAuthenticated();

        try {
            return new DivisionSettings($this->client->get("products/{$productId}/divisions"));
        } catch (ClientException $previousException) {
            switch ($previousException->getResponse()->getStatusCode()) {
                case Response::HTTP_FORBIDDEN:
                    $exception = new AccessDenied("You must be authenticated as an admin or vendor.");
                    break;
                case Response::HTTP_NOT_FOUND:
                    $exception = new NotFound("Product '$productId' not found.");
                    break;
                default:
                    $exception = $previousException;
            }
            throw $exception;
        }
    }

    public function patchDivisionsSettings(int $productId, DivisionSettings $divisionSettings): void
    {
        $this->client->mustBeAuthenticated();

        try {
            $this->client->patch(
                "products/{$productId}/divisions",
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
                    $exception = new AccessDenied("You must be authenticated as an admin or vendor.");
                    break;
                case Response::HTTP_NOT_FOUND:
                    $exception = new NotFound("Product '$productId' not found.");
                    break;
                default:
                    $exception = $previousException;
            }
            throw $exception;
        }
    }

    /** @return Division[] a Division tree, see item's `parent` and `children` properties to navigate in the tree */
    public function getDivisionsTree(int $productId, DivisionsTreeFilters $divisionsTreeFilters = null): array
    {
        $this->client->mustBeAuthenticated();

        try {
            return $this->getDivisionsTreeByUrl(
                "products/{$productId}/divisions-tree",
                $divisionsTreeFilters
            );
        } catch (ClientException $previousException) {
            switch ($previousException->getResponse()->getStatusCode()) {
                case Response::HTTP_FORBIDDEN:
                    $exception = new AccessDenied("You must be authenticated as an admin or vendor.");
                    break;
                case Response::HTTP_NOT_FOUND:
                    $exception = new NotFound("Product '$productId' not found.");
                    break;
                default:
                    $exception = $previousException;
            }
            throw $exception;
        }
    }
}
