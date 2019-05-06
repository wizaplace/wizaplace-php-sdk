<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types=1);

namespace Wizaplace\SDK\Tests\Currency;

use Wizaplace\SDK\Currency\CurrencyService;
use Wizaplace\SDK\Tests\ApiTestCase;

class CurrencyTest extends ApiTestCase
{
    public function testGetCountries(): void
    {
        $currencyService = $this->buildCurrencyService('admin@wizaplace.com', 'password');
        $getCurrencyCountries = $currencyService->getCountries('CHF');

        static::assertSame("CH", $getCurrencyCountries[0]['countryCode']);
        static::assertSame("LI", $getCurrencyCountries[1]['countryCode']);
    }

    public function testAddCountry(): void
    {
        $currencyService = $this->buildCurrencyService('admin@wizaplace.com', 'password');
        $currencyService->addCountry('CHF', 'ZZ');
        $getCurrencyCountries = $currencyService->getCountries('CHF');

        static::assertSame("CH", $getCurrencyCountries[0]['countryCode']);
        static::assertSame("LI", $getCurrencyCountries[1]['countryCode']);
        static::assertSame("ZZ", $getCurrencyCountries[2]['countryCode']);
    }

    private function buildCurrencyService($userEmail = 'admin@wizaplace.com', $userPassword = 'password'): CurrencyService
    {
        $apiClient = $this->buildApiClient();
        $apiClient->authenticate($userEmail, $userPassword);

        return new CurrencyService($apiClient);
    }
}
