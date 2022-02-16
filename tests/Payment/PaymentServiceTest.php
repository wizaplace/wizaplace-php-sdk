<?php

/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @license     Proprietary
 * @copyright   Copyright (c) Wizacha
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Tests\Payment;

use Wizaplace\SDK\Basket\Payment;
use Wizaplace\SDK\Basket\PaymentType;
use Wizaplace\SDK\Payment\PaymentService;
use Wizaplace\SDK\Tests\ApiTestCase;

final class PaymentServiceTest extends ApiTestCase
{
    public function testGetPaymentMethods(): void
    {
        $payments = $this->buildPaymentService()->getPaymentMethods();

        static::assertCount(1, $payments);
        static::assertInstanceOf(Payment::class, $payments[0]);
        static::assertSame(5, $payments[0]->getId());
        static::assertSame('Real Credit Card', $payments[0]->getName());
        static::assertSame('Real Credit Card', $payments[0]->getDescription());
        static::assertSame(0, $payments[0]->getPosition());
        static::assertNull($payments[0]->getImage());
        static::assertSame(PaymentType::CREDIT_CARD()->getValue(), $payments[0]->getType()->getValue());
        static::assertNull($payments[0]->getExternalReference());
    }

    private function buildPaymentService(): PaymentService
    {
        $apiClient = $this->buildApiClient();

        return new PaymentService($apiClient);
    }
}
