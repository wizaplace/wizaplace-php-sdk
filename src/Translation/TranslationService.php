<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\Translation;

use GuzzleHttp\RequestOptions;
use Psr\Http\Message\StreamInterface;
use Wizaplace\AbstractService;
use Wizaplace\User\ApiKey;

class TranslationService extends AbstractService
{
    /**
     * @param string|StreamInterface $xliffCatalog
     */
    public function pushXliffCatalog(ApiKey $apiKey, $xliffCatalog, string $locale)
    {
        $locale = $this->getPrimaryLanguage($locale);
        $options = $this->addAuth(
            [
                RequestOptions::HEADERS => [
                    "Content-Type" => "application/x-xliff+xml",
                ],
                RequestOptions::HTTP_ERRORS => true,
                RequestOptions::BODY => $xliffCatalog,
            ],
            $apiKey
        );
        $this->client->request("POST", "translations/front/".$locale, $options);
    }

    public function getXliffCatalog(string $locale): StreamInterface
    {
        $locale = $this->getPrimaryLanguage($locale);
        $options = [
            RequestOptions::HEADERS => [
                "Accept" => "application/x-xliff+xml",
            ],
        ];
        $response = $this->client->request("GET", "translations/front/".$locale, $options);

        return $response->getBody();
    }

    private static function getPrimaryLanguage(string $locale): string
    {
        if (function_exists('\locale_get_primary_language')) {
            $primaryLanguage = \locale_get_primary_language($locale);
            if (is_null($primaryLanguage)) {
                throw new \InvalidArgumentException("Invalid locale '{$locale}'");
            }

            return $primaryLanguage;
        }

        return strtolower(substr($locale, 0, 2));
    }
}
