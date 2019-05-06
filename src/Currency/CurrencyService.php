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
}
