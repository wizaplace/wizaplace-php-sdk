<?php

/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @license     Proprietary
 * @copyright   Copyright (c) Wizacha
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Tests\Payment;

use Wizaplace\SDK\Exception\SomeParametersAreInvalid;
use Wizaplace\SDK\Payment\DirectDebitService;
use Wizaplace\SDK\Tests\ApiTestCase;

final class DirectDebitServiceTest extends ApiTestCase
{
    public function testCreateMandate(): void
    {
        $directDebitService = $this->buildDirectDebitPaymentServiceTest();

        $response = $directDebitService->createMandate(
            5,
            [
                'iban' => 'FR1420041010050500013M02606',
                'bic' => 'CCBPFRPPVER',
                'bank-name' => 'World bank',
                'gender' => 'M',
                'firstname' => 'Robert',
                'lastname' => 'Jean'
            ]
        );

        static::assertEquals([''], $response);
    }

    public function testCreateMandateInvalidProcessor(): void
    {
        static::expectException(SomeParametersAreInvalid::class);
        static::expectExceptionMessage('Invalid payment id');

        $directDebitService = $this->buildDirectDebitPaymentServiceTest();

        $directDebitService->createMandate(
            40000,
            [
                'iban' => 'DE23100000001234567890',
                'bic' => 'MARKDEF1100',
                'bank-name' => 'World bank',
                'gender' => 'M',
                'firstname' => 'Robert',
                'lastname' => 'Jean'
            ]
        );
    }

    public function testCreateMandateInvalidData(): void
    {
        static::expectException(SomeParametersAreInvalid::class);
        static::expectExceptionMessage('This is not a valid Business Identifier Code (BIC).');

        $directDebitService = $this->buildDirectDebitPaymentServiceTest();

        $directDebitService->createMandate(
            5,
            [
                'iban' => 'DE23100000001234567890',
                'bic' => 'Wrong bic',
                'bank-name' => 'World bank',
                'gender' => 'M',
                'firstname' => 'Robert',
                'lastname' => 'Jean'
            ]
        );
    }

    public function testProcessPayment(): void
    {
        $directDebitService = $this->buildDirectDebitPaymentServiceTest();
        $response = $directDebitService->processPayment(5, 1);

        static::assertEquals('success', $response['message']);
    }

    private function buildDirectDebitPaymentServiceTest(
        string $email = 'user@wizaplace.com',
        string $password = 'password'
    ): DirectDebitService {
        $apiClient = $this->buildApiClient();
        $apiClient->authenticate($email, $password);

        return new DirectDebitService($apiClient);
    }
}
