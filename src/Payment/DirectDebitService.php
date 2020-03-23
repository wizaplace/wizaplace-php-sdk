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
 *  Service for generating direct debit mandates and processing payments
 * @package Wizaplace\SDK\Payment
 */
class DirectDebitService extends AbstractService
{
    /**
     * @param int $paymentId ID of the payment method to use (see getPayments())
     * @param string[] $data Data send to the PSP
     *
     * @return string[]
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Wizaplace\SDK\Authentication\AuthenticationRequired
     * @throws \Wizaplace\SDK\Exception\JsonDecodingError
     * @throws SomeParametersAreInvalid
     */
    public function createMandate(int $paymentId, array $data): array
    {
        $this->client->mustBeAuthenticated();

        try {
            return $this->client->post(
                'payment/create-direct-debit-mandate/' . $paymentId,
                [RequestOptions::FORM_PARAMS => $data]
            );
        } catch (ClientException $e) {
            if (400 === $e->getResponse()->getStatusCode() || 404 === $e->getResponse()->getStatusCode()) {
                throw new SomeParametersAreInvalid($e->getMessage(), $e->getCode(), $e);
            }
            throw $e;
        }
    }

    /**
     * @param int $paymentId ID of the payment method to use (see getPayments())
     * @param int $orderId
     *
     * @return string[]
     * @throws SomeParametersAreInvalid
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Wizaplace\SDK\Authentication\AuthenticationRequired
     * @throws \Wizaplace\SDK\Exception\JsonDecodingError
     */
    public function processPayment(int $paymentId, int $orderId): array
    {
        $this->client->mustBeAuthenticated();

        try {
            return $this->client->post(
                'payment/' . $orderId . '/process-direct-debit-payment/' . $paymentId
            );
        } catch (ClientException $e) {
            if (400 === $e->getResponse()->getStatusCode()) {
                throw new SomeParametersAreInvalid($e->getMessage(), $e->getCode(), $e);
            }
            throw $e;
        }
    }
}
