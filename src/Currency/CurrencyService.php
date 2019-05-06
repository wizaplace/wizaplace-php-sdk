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
    public function getCountries(string $currencyCode): array
    {
        $this->client->mustBeAuthenticated();
        try {
            $currencyCountriesData = $this->client->get('currencies/'.$currencyCode.'/countries');

            return $currencyCountriesData['countryCodes'];
        } catch (ClientException $e) {
            if ($e->getResponse()->getStatusCode() === 403) {
                throw new AccessDenied("You must be authenticated as an admin.");
            }
            if ($e->getResponse()->getStatusCode() === 404) {
                throw new NotFound("Currency '".$currencyCode."' not found.");
            }
            throw $e;
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
            if ($e->getResponse()->getStatusCode() === 403) {
                throw new AccessDenied("You must be authenticated as an admin.");
            }
            if ($e->getResponse()->getStatusCode() === 404) {
                throw new NotFound("Currency '".$currencyCode."' not found.");
            }
            if ($e->getResponse()->getStatusCode() === 400) {
                throw new SomeParametersAreInvalid("CountryCode '".$countryCode."' already exist for currency '".$currencyCode."'.");
            }
            throw $e;
        }
    }

    public function removeCountry(string $currencyCode, string $countryCode): self
    {
        try {
            $this->client->mustBeAuthenticated();
            $this->client->delete("currencies/{$currencyCode}/countries/{$countryCode}");
        } catch (ClientException $e) {
            if ($e->getResponse()->getStatusCode() === 403) {
                throw new AccessDenied("You must be authenticated as an admin.");
            }
            if ($e->getResponse()->getStatusCode() === 404) {
                throw new NotFound("Currency '".$currencyCode."' not found.");
            }
            throw $e;
        }

        return $this;
    }
}
