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
use Wizaplace\SDK\Exception\NotFound;
use Wizaplace\SDK\Exception\SomeParametersAreInvalid;
use Wizaplace\SDK\PaginatedData;

class QuoteRequestSelectionService extends AbstractService
{
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

    /**
     * @param QuoteRequestSelectionDeclination[] $declinationsQuantity
     * @return mixed[]
     */
    public function addDeclinationToSelection(array $declinationsQuantity, int $quoteRequestSelectionId = null): array
    {
        $this->client->mustBeAuthenticated();
        $payload = \array_map(function ($declination) {
            return [
                'declinationId' => $declination->getDeclinationId(),
                'quantity' => $declination->getQuantity()
            ];
        }, $declinationsQuantity);

        $url = \is_int($quoteRequestSelectionId) === true
            ? "quote-request-selections/{$quoteRequestSelectionId}/add"
            : "quote-request-selections/add";

        try {
            $response = $this->client->put(
                $url,
                [RequestOptions::JSON => ['declinations' => $payload]]
            );
        } catch (ClientException $exception) {
            if (400 === $exception->getResponse()->getStatusCode()) {
                throw new SomeParametersAreInvalid("Declination not available for quotes.", 400, $exception);
            }

            if (404 === $exception->getResponse()->getStatusCode()) {
                throw new NotFound("Selection or declination not found.", $exception);
            }

            if (403 === $exception->getResponse()->getStatusCode()) {
                throw new AccessDenied("Quote request feature not enabled.", 403, $exception);
            }

            throw $exception;
        }

        return $response;
    }

    /**
     * @param QuoteRequestSelectionDeclination[] $declinationsQuantity
     * @return mixed[]
     */
    public function updateSelectionDeclinations(array $declinationsQuantity, int $quoteRequestSelectionId = null): array
    {
        $this->client->mustBeAuthenticated();
        $payload = \array_map(function ($declination) {
            return [
                'declinationId' => $declination->getDeclinationId(),
                'quantity' => $declination->getQuantity()
            ];
        }, $declinationsQuantity);

        $url = \is_int($quoteRequestSelectionId) === true
            ? "quote-request-selections/{$quoteRequestSelectionId}/update"
            : "quote-request-selections/update";

        try {
            $response = $this->client->put(
                $url,
                [RequestOptions::JSON => ['declinations' => $payload]]
            );
        } catch (ClientException $exception) {
            if (400 === $exception->getResponse()->getStatusCode()) {
                throw new SomeParametersAreInvalid("Declination not available for quotes.", 400, $exception);
            }

            if (404 === $exception->getResponse()->getStatusCode()) {
                throw new NotFound("Selection or declination not found.", $exception);
            }

            if (403 === $exception->getResponse()->getStatusCode()) {
                throw new AccessDenied("Quote request feature not enabled.", 403, $exception);
            }

            throw $exception;
        }

        return $response;
    }

    /**
     * @param string[] $declinationsIds Array of declinations ids (['1_0', '2_0', '3_0']) to remove
     * @return mixed[]
     */
    public function removeDeclinationFromSelection(array $declinationsIds, int $quoteRequestSelectionId = null): array
    {
        $this->client->mustBeAuthenticated();
        $payload = \array_map(function ($declination) {
            return [
                'declinationId' => $declination
            ];
        }, $declinationsIds);

        $url = \is_int($quoteRequestSelectionId) === true
            ? "quote-request-selections/{$quoteRequestSelectionId}/remove"
            : "quote-request-selections/remove";

        try {
            $response = $this->client->put(
                $url,
                [RequestOptions::JSON => ['declinations' => $payload]]
            );
        } catch (ClientException $exception) {
            if (404 === $exception->getResponse()->getStatusCode()) {
                throw new NotFound("Selection or declination not found.", $exception);
            }

            if (403 === $exception->getResponse()->getStatusCode()) {
                throw new AccessDenied("Quote request feature not enabled.", 403, $exception);
            }

            throw $exception;
        }

        return $response;
    }
}
