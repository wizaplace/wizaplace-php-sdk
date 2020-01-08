<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @license     Proprietary
 * @copyright   Copyright (c) Wizacha
 */
declare(strict_types=1);

namespace Wizaplace\SDK\Subscription;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\RequestOptions;
use Wizaplace\SDK\AbstractService;
use Wizaplace\SDK\Exception\AccessDenied;
use Wizaplace\SDK\Exception\SomeParametersAreInvalid;
use Wizaplace\SDK\PaginatedData;
use Wizaplace\SDK\Traits\AssertRessourceForbiddenOrNotFoundTrait;
use Wizaplace\SDK\Vendor\Order\Order as VendorOrder;

final class SubscriptionService extends AbstractService
{
    use AssertRessourceForbiddenOrNotFoundTrait;

    /**
     * @param SubscriptionFilter|null $subscriptionFilter
     *
     * @throws AccessDenied
     * @throws SomeParametersAreInvalid
     * @throws \GuzzleHttp\Exception\ClientException
     *
     * @return PaginatedData
     */
    public function listBy(SubscriptionFilter $subscriptionFilter = null): PaginatedData
    {
        $this->client->mustBeAuthenticated();

        if (false === $subscriptionFilter instanceof SubscriptionFilter) {
            $subscriptionFilter = (new SubscriptionFilter())
                ->setLimit(10)
                ->setOffset(0);
        }

        try {
            $response = $this->client->get(
                "subscriptions",
                [RequestOptions::QUERY => $subscriptionFilter->getFilters()]
            );
        } catch (ClientException $exception) {
            if (400 === $exception->getResponse()->getStatusCode()) {
                throw new SomeParametersAreInvalid("Some parameters are invalid.", 400, $exception);
            }

            if (403 === $exception->getResponse()->getStatusCode()) {
                throw new AccessDenied("You're not allowed to access to this subscription.", 403, $exception);
            }

            throw $exception;
        }

        return new PaginatedData(
            $response['limit'],
            $response['offset'],
            $response['total'],
            array_map(function (array $subscription): SubscriptionSummary {
                return new SubscriptionSummary($subscription);
            }, $response['items'])
        );
    }

    /**
     * @param string $subscriptionId
     *
     * @return Subscription
     */
    public function getSubscription(string $subscriptionId): Subscription
    {
        $this->client->mustBeAuthenticated();

        return $this->assertRessourceForbiddenOrNotFound(
            function () use ($subscriptionId): Subscription {
                return new Subscription($this->client->get("subscriptions/{$subscriptionId}"));
            },
            "You're not allowed to access to this subscription.",
            "Subscription '{$subscriptionId}' not found."
        );
    }

    /**
     * @param string $subscriptionId
     * @param int    $limit
     * @param int    $offset
     *
     * @return PaginatedData
     */
    public function getItems(string $subscriptionId, int $limit = 10, int $offset = 0): PaginatedData
    {
        $this->client->mustBeAuthenticated();

        return $this->assertRessourceForbiddenOrNotFound(
            function () use ($subscriptionId, $limit, $offset): PaginatedData {
                $response = $this->client->get(
                    "subscriptions/{$subscriptionId}/items",
                    [
                        RequestOptions::QUERY => [
                            'limit' => $limit,
                            'offset' => $offset,
                        ],
                    ]
                );

                return new PaginatedData(
                    $response['limit'],
                    $response['offset'],
                    $response['total'],
                    array_map(function (array $item): SubscriptionItem {
                        return new SubscriptionItem($item);
                    }, $response['items'])
                );
            },
            "You're not allowed to access to this subscription.",
            "Subscription '{$subscriptionId}' not found."
        );
    }

    /**
     * @param string $subscriptionId
     * @param int    $limit
     * @param int    $offset
     *
     * @return PaginatedData
     */
    public function getTaxes(string $subscriptionId, int $limit = 10, int $offset = 0): PaginatedData
    {
        $this->client->mustBeAuthenticated();

        return $this->assertRessourceForbiddenOrNotFound(
            function () use ($subscriptionId, $limit, $offset): PaginatedData {
                $response = $this->client->get(
                    "subscriptions/{$subscriptionId}/taxes",
                    [
                        RequestOptions::QUERY => [
                            'limit' => $limit,
                            'offset' => $offset,
                        ],
                    ]
                );

                return new PaginatedData(
                    $response['limit'],
                    $response['offset'],
                    $response['total'],
                    array_map(function (array $item): SubscriptionTax {
                        return new SubscriptionTax($item);
                    }, $response['items'])
                );
            },
            "You're not allowed to access to this subscription.",
            "Subscription '{$subscriptionId}' not found."
        );
    }

    /**
     * @param string $subscriptionId
     * @param int    $limit
     * @param int    $offset
     *
     * @return PaginatedData
     */
    public function getOrders(string $subscriptionId, int $limit = 10, int $offset = 0): PaginatedData
    {
        $this->client->mustBeAuthenticated();

        return $this->assertRessourceForbiddenOrNotFound(
            function () use ($subscriptionId, $limit, $offset): PaginatedData {
                $response = $this->client->get(
                    "subscriptions/{$subscriptionId}/orders",
                    [
                        RequestOptions::QUERY => [
                            'limit' => $limit,
                            'offset' => $offset,
                        ],
                    ]
                );

                return new PaginatedData(
                    $response['limit'],
                    $response['offset'],
                    $response['total'],
                    array_map(function (array $item): VendorOrder {
                        return new VendorOrder($item);
                    }, $response['items'])
                );
            },
            "You're not allowed to access to this subscription.",
            "Subscription '{$subscriptionId}' not found."
        );
    }

    /**
     * @param UpdateSubscriptionCommand $command
     *
     * @return Subscription
     */
    public function patchSubscription(UpdateSubscriptionCommand $command): Subscription
    {
        $this->client->mustBeAuthenticated();
        $command->validate();

        return $this->assertRessourceForbiddenOrNotFound(
            function () use ($command): Subscription {
                return new Subscription($this->client->patch(
                    "subscriptions/{$command->getSubscriptionId()}",
                    [
                        RequestOptions::FORM_PARAMS => $command->toArray(),
                    ]
                ));
            },
            "You're not allowed to access to this subscription.",
            "Subscription '{$command->getSubscriptionId()}' not found."
        );
    }
}
