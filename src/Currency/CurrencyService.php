<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types=1);

namespace Wizaplace\SDK\Currency;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\RequestOptions;
use Wizaplace\SDK\AbstractService;
use Wizaplace\SDK\Exception\AccessDenied;
use Wizaplace\SDK\Exception\NotFound;
use Wizaplace\SDK\Exception\SomeParametersAreInvalid;

class CurrencyService extends AbstractService
{
    /** @return Currency[] */
    public function getAll(): array
    {
        $this->client->mustBeAuthenticated();
        try {
            $currencies = $this->client->get('currencies');

            return array_map(
                function (array $data): Currency {
                    return new Currency($data);
                },
                $currencies
            );
        } catch (ClientException $e) {
            switch ($e->getResponse()->getStatusCode()) {
                case 403:
                    throw new AccessDenied("You must be authenticated as an admin.");
                default:
                    throw $e;
            }
        }
    }

    /** @return CurrencyCountries[] */
    public function getCountries(string $currencyCode): array
    {
        $this->client->mustBeAuthenticated();
        try {
            $currencyCountriesData = $this->client->get('currencies/'.$currencyCode.'/countries');
            $data = [];
            foreach ($currencyCountriesData as $code) {
                $data[] = new CurrencyCountries($code);
            }

            return $data;
        } catch (ClientException $e) {
            switch ($e->getResponse()->getStatusCode()) {
                case 403:
                    throw new AccessDenied("You must be authenticated as an admin.");
                case 404:
                    throw new NotFound("Currency '$currencyCode' not found.");
                default:
                    throw $e;
            }
        }
    }

    public function addCountry(string $currencyCode, string $countryCode): array
    {
        $this->client->mustBeAuthenticated();

        try {
            return $this->client->post('currencies/'.$currencyCode.'/countries', [
                RequestOptions::FORM_PARAMS => [
                    'countryCode' => $countryCode,
                ],
            ]);
        } catch (ClientException $e) {
            switch ($e->getResponse()->getStatusCode()) {
                case 403:
                    throw new AccessDenied("You must be authenticated as an admin.");
                case 404:
                    throw new NotFound("Currency '$currencyCode' not found.");
                case 400:
                    throw new SomeParametersAreInvalid("CountryCode '".$countryCode."' already exist for currency '".$currencyCode."'.");
                default:
                    throw $e;
            }
        }
    }

    public function removeCountry(string $currencyCode, string $countryCode): self
    {
        try {
            $this->client->mustBeAuthenticated();
            $this->client->delete("currencies/{$currencyCode}/countries/{$countryCode}");
        } catch (ClientException $e) {
            switch ($e->getResponse()->getStatusCode()) {
                case 403:
                    throw new AccessDenied("You must be authenticated as an admin.");
                case 404:
                    throw new NotFound("Currency '$currencyCode' not found.");
                default:
                    throw $e;
            }
        }

        return $this;
    }

    /** @return Currency[] */
    public function getByFilters(array $filters): array
    {
        $this->client->mustBeAuthenticated();
        try {
            $currencies = $this->client->get('currencies?'.http_build_query($filters));

            return array_map(
                function (array $data): Currency {
                    return new Currency($data);
                },
                $currencies
            );
        } catch (ClientException $e) {
            switch ($e->getResponse()->getStatusCode()) {
                case 403:
                    throw new AccessDenied("You must be authenticated as an admin.");
                default:
                    throw $e;
            }
        }
    }

    public function getByCountryCode(string $code): ?Currency
    {
        $currencies = $this->getByFilters(['countryCode' => $code]);

        return count($currencies) > 0 ? array_shift($currencies) : null;
    }

    public function updateCurrency(Currency $currency): array
    {
        $this->client->mustBeAuthenticated();

        try {
            return $this->client->patch("currencies/{$currency->getCode()}", [
                RequestOptions::JSON => [
                    'enabled' => $currency->isEnabled(),
                    'exchangeRate' => $currency->getExchangeRate(),
                ],

            ]);
        } catch (ClientException $e) {
            switch ($e->getResponse()->getStatusCode()) {
                case 403:
                    throw new AccessDenied("You must be authenticated as an admin.");
                case 404:
                    throw new NotFound("Currency '{$currency->getCode()}' not found.");
                case 400:
                    throw new SomeParametersAreInvalid($e->getMessage());
                default:
                    throw $e;
            }
        }
    }

    public function getCurrency(string $currencyCode): Currency
    {
        $this->client->mustBeAuthenticated();
        try {
            return new Currency($this->client->get('currencies/'.$currencyCode));
        } catch (ClientException $e) {
            switch ($e->getResponse()->getStatusCode()) {
                case 403:
                    throw new AccessDenied("You must be authenticated as an admin.");
                case 404:
                    throw new NotFound("Currency '$currencyCode' not found.");
                default:
                    throw $e;
            }
        }
    }
}
