<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\Translation;

use GuzzleHttp\RequestOptions;
use Psr\Http\Message\StreamInterface;
use Wizaplace\AbstractService;
use Wizaplace\Authentication\AuthenticationRequired;
use Wizaplace\User\ApiKey;

final class TranslationService extends AbstractService
{
    /**
     * @param string|StreamInterface $xliffCatalog
     * @throws AuthenticationRequired
     */
    public function pushXliffCatalog($xliffCatalog, string $locale)
    {
        $this->client->mustBeAuthenticated();
        $options = [
            RequestOptions::HEADERS => [
                "Content-Type" => "application/x-xliff+xml",
            ],
            RequestOptions::HTTP_ERRORS => true,
            RequestOptions::BODY => $xliffCatalog,
        ];
        $this->client->post("translations/front/".$locale, $options);
    }

    public function getXliffCatalog(string $locale): StreamInterface
    {
        $options = [
            RequestOptions::HEADERS => [
                "Accept" => "application/x-xliff+xml",
            ],
        ];
        $response = $this->client->rawRequest("GET", "translations/front/".$locale, $options);

        return $response->getBody();
    }
}
