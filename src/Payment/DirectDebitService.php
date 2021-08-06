<?php

/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @license     Proprietary
 * @copyright   Copyright (c) Wizacha
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Payment;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\RequestOptions;
use Wizaplace\SDK\AbstractService;
use Wizaplace\SDK\Exception\SomeParametersAreInvalid;

/**
 *  Service for get and create direct debit mandates for user
 * @package Wizaplace\SDK\Payment
 */
class DirectDebitService extends AbstractService
{
    /**
     * @param string[] $data Data send to the PSP
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Wizaplace\SDK\Authentication\AuthenticationRequired
     * @throws \Wizaplace\SDK\Exception\JsonDecodingError
     * @throws SomeParametersAreInvalid
     */
    public function createMandate(array $data): array
    {
        $this->client->mustBeAuthenticated();

        try {
            return $this->client->post('user/mandates', [RequestOptions::JSON => $data]);
        } catch (ClientException $e) {
            if (400 === $e->getResponse()->getStatusCode() || 404 === $e->getResponse()->getStatusCode()) {
                throw new SomeParametersAreInvalid($e->getMessage(), $e->getCode(), $e);
            }
            throw $e;
        }
    }

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Wizaplace\SDK\Authentication\AuthenticationRequired
     * @throws \Wizaplace\SDK\Exception\JsonDecodingError
     *
     * @return array[]
     */
    public function getMandates(): array
    {
        $this->client->mustBeAuthenticated();

        return $this->client->get('user/mandates');
    }
}
