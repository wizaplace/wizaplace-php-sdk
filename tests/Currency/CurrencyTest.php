<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types=1);

namespace Wizaplace\SDK\Tests\Currency;

use Wizaplace\SDK\Currency\Currency;
use Wizaplace\SDK\Currency\CurrencyCountries;
use Wizaplace\SDK\Currency\CurrencyService;
use Wizaplace\SDK\Tests\ApiTestCase;

class CurrencyTest extends ApiTestCase
{
    public function testGetAllCurrencies(): void
    {
        $currencyService = $this->buildCurrencyService('admin@wizaplace.com', 'password');
        $currencies = $currencyService->getAll();
        static::assertCount(156, $currencies);

        /** @var Currency $currency */
        $currency = array_shift($currencies);
        static::assertInstanceOf(Currency::class, $currency);
        static::assertCount(1, $currency->getCountryCodes());
        static::assertInstanceOf(CurrencyCountries::class, $currency->getCountryCodes()[0]);
        static::assertSame("AE", $currency->getCountryCodes()[0]->getCountryCode());
    }

    public function testGetCountries(): void
    {
        $currencyService = $this->buildCurrencyService('admin@wizaplace.com', 'password');
        $getCurrencyCountries = $currencyService->getCountries('CHF');

        static::assertSame("CH", $getCurrencyCountries[0]->getCountryCode());
        static::assertSame("LI", $getCurrencyCountries[1]->getCountryCode());
    }

    public function testAddCountry(): void
    {
        $currencyService = $this->buildCurrencyService('admin@wizaplace.com', 'password');
        $currencyService->addCountry('CHF', 'ZZ');
        $getCurrencyCountries = $currencyService->getCountries('CHF');

        static::assertSame("CH", $getCurrencyCountries[0]->getCountryCode());
        static::assertSame("LI", $getCurrencyCountries[1]->getCountryCode());
        static::assertSame("ZZ", $getCurrencyCountries[2]->getCountryCode());
    }

    public function testDeleteCountry(): void
    {
        $currencyService = $this->buildCurrencyService('admin@wizaplace.com', 'password');
        $currencyService->removeCountry('CHF', 'ZZ');
        $getCurrencyCountries = $currencyService->getCountries('CHF');

        static::assertSame("CH", $getCurrencyCountries[0]->getCountryCode());
        static::assertSame("LI", $getCurrencyCountries[1]->getCountryCode());
    }

    private function buildCurrencyService($userEmail = 'admin@wizaplace.com', $userPassword = 'password'): CurrencyService
    {
        $apiClient = $this->buildApiClient();
        $apiClient->authenticate($userEmail, $userPassword);

        return new CurrencyService($apiClient);
    }
}
