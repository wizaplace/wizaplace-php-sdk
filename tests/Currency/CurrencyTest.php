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

    public function testGetCurrency(): void
    {
        $currencyService = $this->buildCurrencyService('admin@wizaplace.com', 'password');
        $currency = $currencyService->getCurrency('CHF');

        static::assertInstanceOf(Currency::class, $currency);
        static::assertInstanceOf(CurrencyCountries::class, $currency->getCountryCodes()[0]);
        static::assertSame("CHF", $currency->getCode());
        static::assertSame("CH", $currency->getCountryCodes()[0]->getCountryCode());
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

    public function testGetCurrenciesByFilters(): void
    {
        $currencyService = $this->buildCurrencyService('admin@wizaplace.com', 'password');

        $currencies = $currencyService->getByFilters(['countryCode' => 'GB']);
        static::assertCount(1, $currencies);
        /** @var Currency $currency */
        $currency = array_shift($currencies);
        static::assertInstanceOf(Currency::class, $currency);
        static::assertSame("GBP", $currency->getCode());

        $currencies = $currencyService->getByFilters([]);
        $this->assertInternalType('array', $currencies);
    }

    public function testGetCurrenciesByCountryCode(): void
    {
        $currencyService = $this->buildCurrencyService('admin@wizaplace.com', 'password');

        $currency = $currencyService->getByCountryCode('GB');
        static::assertInstanceOf(Currency::class, $currency);
        static::assertSame("GBP", $currency->getCode());
    }

    public function testGetCurrenciesByXXCountryCode(): void
    {
        $currencyService = $this->buildCurrencyService('admin@wizaplace.com', 'password');

        $currency = $currencyService->getByCountryCode('XX');
        static::assertNull($currency);
    }

    public function testGetCurrenciesByEmptyCountryCode(): void
    {
        $currencyService = $this->buildCurrencyService('admin@wizaplace.com', 'password');

        $currency = $currencyService->getByCountryCode('');
        static::assertNull($currency);
    }

    public function testUpdateCurrency(): void
    {
        // Get a currency
        $currencyService = $this->buildCurrencyService('admin@wizaplace.com', 'password');
        $currencies = $currencyService->getAll();
        $currency = array_shift($currencies);
        static::assertInstanceOf(Currency::class, $currency);

        // Update
        $currency->setEnabled(true);
        $currency->setExchangeRate(12.8);
        $currency = $currencyService->updateCurrency($currency);
        static::assertInternalType('array', $currency);

        // Check if updated
        static::assertTrue($currency['enabled']);
        static::assertSame(12.8, $currency['exchangeRate']);
    }

    private function buildCurrencyService($userEmail = 'admin@wizaplace.com', $userPassword = 'password'): CurrencyService
    {
        $apiClient = $this->buildApiClient();
        $apiClient->authenticate($userEmail, $userPassword);

        return new CurrencyService($apiClient);
    }
}
