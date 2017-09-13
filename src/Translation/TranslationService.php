<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\SDK\Translation;

use GuzzleHttp\RequestOptions;
use Psr\Http\Message\StreamInterface;
use Wizaplace\SDK\AbstractService;

final class TranslationService extends AbstractService
{
    /**
     * @param string|StreamInterface $xliffCatalog
     * @param string $locale
     * @param string $password special system user's password, needed for authentication
     */
    public function pushXliffCatalog($xliffCatalog, string $locale, string $password)
    {
        $options = [
            RequestOptions::HEADERS => [
                "Content-Type" => "application/x-xliff+xml",
            ],
            RequestOptions::HTTP_ERRORS => true,
            RequestOptions::BODY => $xliffCatalog,
            RequestOptions::AUTH => ['system', $password],
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
