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
    /** @var DirectDebitService */
    protected $directDebitService;

    public function setUp(): void
    {
        parent::setUp();
        $this->directDebitService = $this->buildDirectDebitPaymentServiceTest();
    }

    public function testCreateMandateInvalidProcessor(): void
    {
        static::expectException(SomeParametersAreInvalid::class);
        static::expectExceptionMessage('Invalid payment id');

        $this->directDebitService->createMandate(
            [
                'iban' => 'DE23100000001234567890',
                'bic' => 'MARKDEF1100',
                'bankName' => 'World bank',
                'gender' => 'M',
                'firstName' => 'Robert',
                'lastName' => 'Jean',
                'paymentId' => 40000,
            ]
        );
    }

    public function testCreateMandateInvalidData(): void
    {
        static::expectException(SomeParametersAreInvalid::class);
        static::expectExceptionMessage('This is not a valid Business Identifier Code (BIC).');

        $this->directDebitService->createMandate(
            [
                'iban' => 'DE23100000001234567890',
                'bic' => 'Wrong bic',
                'bankName' => 'World bank',
                'gender' => 'M',
                'firstName' => 'Robert',
                'lastName' => 'Jean',
                'paymentId' => 5,
            ]
        );
    }

    public function testGetMandates(): void
    {
        $response = $this->directDebitService->getMandates();

        $expectedResult = [
            [
                "createdAt" => "2020-04-08T14:00:05+02:00",
                "iban" => "FR14*******************2606",
                "issuerBankId" => "CCBPFRPPVER",
                "bankName" => "World bank",
                "gender" => "M",
                "firstName" => "Robert",
                "lastName" => "Jean"
            ]
        ];

        static::assertEquals($expectedResult, $response);
    }

    public function testGetMandatesNoMandate(): void
    {
        $response = $this->directDebitService->getMandates();

        $expectedResult = [[]];

        static::assertEquals($expectedResult, $response);
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
