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
            1013,
            10,
            [
                'iban' => 'SE35 5000 0000 0549 1000 0003',
                'bic' => 'BCRTFRPP',
                'bank-name' => 'Mock Bank',
                'gender' => 'M',
                'firstname' => 'Robert',
                'lastname' => 'Jean'
            ]
        );

        static::assertEquals('success', $response['message']);
    }

    public function testCreateMandateInvalidProcessor(): void
    {
        static::expectException(SomeParametersAreInvalid::class);
        static::expectExceptionMessage('Invalid payment processor id');

        $directDebitService = $this->buildDirectDebitPaymentServiceTest();

        $directDebitService->createMandate(
            -1,
            10,
            [
                'iban' => 'SE35 5000 0000 0549 1000 0003',
                'bic' => 'BCRTFRPP',
                'bank-name' => 'Mock Bank',
                'gender' => 'M',
                'firstname' => 'Robert',
                'lastname' => 'Jean'
            ]
        );
    }

    public function testCreateMandateInvalidData(): void
    {
        static::expectException(SomeParametersAreInvalid::class);

        $directDebitService = $this->buildDirectDebitPaymentServiceTest();

        $directDebitService->createMandate(
            1013,
            10,
            [
                'iban' => 'SE35 5000 0000 0549 1000 0003',
                'bic' => 'Wrong bic',
                'bank-name' => 'Mock Bank',
                'gender' => 'M',
                'firstname' => 'Robert',
                'lastname' => 'Jean'
            ]
        );
    }

    public function testProcessPayment(): void
    {
        $directDebitService = $this->buildDirectDebitPaymentServiceTest();
        $response = $directDebitService->processPayment(1013, 10);

        static::assertEquals('success', $response['message']);
    }

    private function buildDirectDebitPaymentServiceTest(
        string $email = 'admin@wizaplace.com',
        string $password = 'password'
    ): DirectDebitService {
        $apiClient = $this->buildApiClient();
        $apiClient->authenticate($email, $password);

        return new DirectDebitService($apiClient);
    }
}
