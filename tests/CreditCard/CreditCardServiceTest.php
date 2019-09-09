<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @license     Proprietary
 * @copyright   Copyright (c) Wizacha
 */
declare(strict_types=1);

namespace Wizaplace\SDK\Tests\CreditCard;

use Wizaplace\SDK\CreditCard\CreditCard;
use Wizaplace\SDK\CreditCard\CreditCardService;
use Wizaplace\SDK\Tests\ApiTestCase;

final class CreditCardServiceTest extends ApiTestCase
{
    public function testGetCreditCards(): void
    {
        $creditCardService = $this->buildCreditCardService();
        $creditCards = $creditCardService->getCreditCards(3);

        static::assertSame(0, $creditCards->getOffset());
        static::assertSame(10, $creditCards->getLimit());
        static::assertSame(3, $creditCards->getTotal());
        static::assertCount(3, $creditCards->getItems());

        foreach ($creditCards->getItems() as $creditCard) {
            /** @var CreditCard $creditCard */
            $this->assertCreditCard($creditCard);
        }
    }

    public function testGetCreditCard(): void
    {
        $creditCardService = $this->buildCreditCardService();

        $this->assertCreditCard(
            $creditCardService->getCreditCard(3, "73172562-a474-4ce7-93c2-8b1e57269762")
        );
    }

    private function assertCreditCard(CreditCard $creditCard): void
    {
        static::assertInstanceOf(CreditCard::class, $creditCard);
        static::assertUuid($creditCard->getId());
        static::assertSame(3, $creditCard->getUserId());
        static::assertSame("VISA", $creditCard->getBrand());
        static::assertSame(19, mb_strlen($creditCard->getPan()));
        static::assertSame('01', $creditCard->getExpiryMonth());
        static::assertSame('2020', $creditCard->getExpiryYear());
        static::assertSame('US', $creditCard->getCountry());
        static::assertInstanceOf(\DateTime::class, $creditCard->getCreatedAt());
    }

    private function buildCreditCardService(string $email = 'user@wizaplace.com', string $password = 'password'): CreditCardService
    {
        $apiClient = $this->buildApiClient();
        $apiClient->authenticate($email, $password);

        return new CreditCardService($apiClient);
    }
}
