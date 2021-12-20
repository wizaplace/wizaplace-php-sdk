<?php

/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @license     Proprietary
 * @copyright   Copyright (c) Wizacha
 */

declare(strict_types=1);

namespace Wizaplace\SDK\QuoteRequest\QuoteRequestSelection;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\RequestOptions;
use Wizaplace\SDK\AbstractService;
use Wizaplace\SDK\Exception\AccessDenied;
use Wizaplace\SDK\Exception\SomeParametersAreInvalid;
use Wizaplace\SDK\PaginatedData;
use Wizaplace\SDK\Traits\AssertRessourceForbiddenOrNotFoundTrait;

class QuoteRequestSelectionService extends AbstractService
{
    use AssertRessourceForbiddenOrNotFoundTrait;

    /**
     * @param QuoteRequestSelectionFilter|null $subscriptionFilter
     *
     * @throws AccessDenied
     * @throws SomeParametersAreInvalid
     * @throws \GuzzleHttp\Exception\ClientException
     *
     * @return PaginatedData
     */
    public function listBy(QuoteRequestSelectionFilter $selectionFilter = null): PaginatedData
    {
        $this->client->mustBeAuthenticated();

        if (false === $selectionFilter instanceof QuoteRequestSelectionFilter) {
            $selectionFilter = (new QuoteRequestSelectionFilter())
                ->setLimit(100)
                ->setOffset(0);
        }

        try {
            $response = $this->client->get(
                "quote-request-selections",
                [RequestOptions::QUERY => $selectionFilter->getFilters()]
            );
        } catch (ClientException $exception) {
            if (400 === $exception->getResponse()->getStatusCode()) {
                throw new SomeParametersAreInvalid("Some parameters are invalid.", 400, $exception);
            }

            if (403 === $exception->getResponse()->getStatusCode()) {
                throw new AccessDenied("Quote request feature not enabled.", 403, $exception);
            }

            throw $exception;
        }

        return new PaginatedData(
            $response['limit'],
            $response['offset'],
            $response['total'],
            array_map(
                function (array $subscription): QuoteRequestSelection {
                    return new QuoteRequestSelection($subscription);
                },
                $response['items']
            )
        );
    }
}
