<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @license     Proprietary
 * @copyright   Copyright (c) Wizacha
 */
declare(strict_types=1);

namespace Wizaplace\SDK\CreditCard;

use GuzzleHttp\RequestOptions;
use Wizaplace\SDK\AbstractService;
use Wizaplace\SDK\PaginatedData;
use Wizaplace\SDK\Traits\AssertRessourceNotFoundTrait;

final class CreditCardService extends AbstractService
{
    use AssertRessourceNotFoundTrait;

    /**
     * @param int $userId
     * @param int $limit
     * @param int $offset
     *
     * @return PaginatedData
     */
    public function getCreditCards(int $userId, int $limit = 10, int $offset = 0): PaginatedData
    {
        $this->client->mustBeAuthenticated();

        return $this->assertRessourceNotFound(
            function () use ($userId, $limit, $offset): PaginatedData {
                $response = $this->client->get(
                    "users/{$userId}/cards",
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
                    array_map(function (array $creditCard): CreditCard {
                        return new CreditCard($creditCard);
                    }, $response['items'])
                );
            },
            "User '{$userId}' not found."
        );
    }

    /**
     * @param int    $userId
     * @param string $creditCardId
     *
     * @return CreditCard
     */
    public function getCreditCard(int $userId, string $creditCardId): CreditCard
    {
        $this->client->mustBeAuthenticated();

        return $this->assertRessourceNotFound(
            function () use ($userId, $creditCardId): CreditCard {
                return new CreditCard($this->client->get("users/{$userId}/cards/{$creditCardId}"));
            },
            "User '{$userId}' or credit card '{$creditCardId}' not found."
        );
    }

    /**
     * @param int $userId
     *
     * @return string
     */
    public function getRegistrationUrl(int $userId, string $redirectUrl, int $paymentId, string $cssUrl = null): string
    {
        $this->client->mustBeAuthenticated();

        $params = [
            'redirectUrl' => $redirectUrl,
            'paymentId' => $paymentId,
        ];

        if (\is_string($cssUrl)) {
            $params['cssUrl'] = $cssUrl;
        }

        return $this->assertRessourceNotFound(
            function () use ($userId, $params): string {
                return $this->client->get(
                    "users/{$userId}/credit-card-registration",
                    [RequestOptions::QUERY => $params]
                )['url'];
            },
            "User '{$userId}' not found."
        );
    }
}
