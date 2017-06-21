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
        $this->client->post("translations/front/".$locale, $options);
    }

    public function getXliffCatalog(string $locale): StreamInterface
    {
        $options = [
            RequestOptions::HEADERS => [
                "Accept" => "application/x-xliff+xml",
            ],
        ];
        $response = $this->client->get("translations/front/".$locale, $options);

        return $response->getBody();
    }
}
