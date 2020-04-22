<?php

/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */

namespace Wizaplace\SDK\Shipping;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\RequestOptions;
use Wizaplace\SDK\AbstractService;
use Wizaplace\SDK\Exception\NotFound;

use function theodorejb\polycast\to_int;

/**
 * Class ShippingService
 * @package Wizaplace\SDK\Shipping
 */
class ShippingService extends AbstractService
{
    private const ENDPOINT = "shippings";

    /**
     * Get the list of shippings
     *
     * @return Shipping[]
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Wizaplace\SDK\Authentication\AuthenticationRequired
     * @throws \Wizaplace\SDK\Exception\JsonDecodingError
     */
    public function getAll(): array
    {
        $this->client->mustBeAuthenticated();

        try {
            $response = $this->client->get(self::ENDPOINT);

            return array_map(
                function ($shipping) {
                    return new Shipping($shipping);
                },
                $response
            );
        } catch (ClientException $e) {
            throw $e;
        }
    }

    /**
     * Get a shipping
     *
     * @param int $id
     *
     * @return Shipping
     * @throws NotFound
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Wizaplace\SDK\Authentication\AuthenticationRequired
     * @throws \Wizaplace\SDK\Exception\JsonDecodingError
     */
    public function getById(int $id): Shipping
    {
        $this->client->mustBeAuthenticated();

        try {
            $response = $this->client->get(self::ENDPOINT . "/" . $id);

            return new Shipping($response);
        } catch (ClientException $e) {
            if ($e->getResponse()->getStatusCode() === 404) {
                throw new NotFound("The shipping #{$id} doesn't exist", $e);
            }

            throw $e;
        }
    }

    /**
     * @param int            $id
     * @param ShippingStatus $status
     * @param ShippingRate[] $rates
     *
     * @return int Shipping's id
     * @throws NotFound
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Wizaplace\SDK\Authentication\AuthenticationRequired
     * @throws \Wizaplace\SDK\Exception\JsonDecodingError
     */
    public function put(int $id, ShippingStatus $status, array $rates, float $carriagePaidThreshold = null): int
    {
        $this->client->mustBeAuthenticated();

        if (empty($rates)) {
            throw new \BadMethodCallException("You need to set a least one rate");
        }

        if (\count($rates) > 2) {
            throw new \BadMethodCallException("You can have only 2 rates max");
        }

        try {
            $response = $this->client->put(
                self::ENDPOINT . "/" . $id,
                [
                    RequestOptions::JSON => [
                        "status" => $status->getValue(),
                        "rates" => [
                            [
                                "amount" => $rates[0]->getAmount(),
                                "value" => $rates[0]->getValue(),
                            ],
                            [
                                "amount" => isset($rates[1]) ? $rates[1]->getAmount() : $rates[0]->getAmount(),
                                "value" => isset($rates[1]) ? $rates[1]->getValue() : $rates[0]->getValue(),
                            ],
                        ],
                        "carriage_paid_threshold" => $carriagePaidThreshold
                    ],
                ]
            );

            return to_int($response['shipping_id']);
        } catch (ClientException $e) {
            if ($e->getResponse()->getStatusCode() === 404) {
                throw new NotFound("The shipping #{$id} doesn't exist", $e);
            }

            throw $e;
        }
    }
}
