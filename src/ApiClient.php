<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\SDK;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Jean85\PrettyVersions;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;
use Wizaplace\SDK\Authentication\ApiKey;
use Wizaplace\SDK\Authentication\AuthenticationRequired;
use Wizaplace\SDK\Authentication\BadCredentials;
use Wizaplace\SDK\Exception\JsonDecodingError;

final class ApiClient
{
    /** @var Client */
    private $httpClient;

    /** @var null|ApiKey */
    private $apiKey;

    /** @var string */
    private $version;

    public function __construct(Client $client)
    {
        $this->httpClient = $client;

        try {
            $this->version = PrettyVersions::getVersion('wizaplace/sdk')->getShortVersion();
        } catch (\OutOfBoundsException $exception) {
            // Ignore error if version cannot be detected
            $this->version = 'unknown';
        }
    }

    /**
     * @throws BadCredentials
     */
    public function authenticate(string $email, string $password): ApiKey
    {
        try {
            $apiKeyData = $this->get(
                'users/authenticate',
                [
                    'auth' => [$email, $password],
                ]
            );
        } catch (ClientException $e) {
            if ($e->getResponse()->getStatusCode() === 401) {
                throw new BadCredentials($e);
            }
            throw $e;
        }

        $apiKey = new ApiKey($apiKeyData);
        $this->setApiKey($apiKey);

        return $apiKey;
    }

    public function setApiKey(?ApiKey $apiKey = null): void
    {
        $this->apiKey = $apiKey;
    }

    public function getApiKey(): ?ApiKey
    {
        return $this->apiKey;
    }

    /**
     * @throws AuthenticationRequired
     */
    public function mustBeAuthenticated(): void
    {
        if (is_null($this->getApiKey())) {
            throw new AuthenticationRequired();
        }
    }

    public function get(string $endpoint, array $options = [])
    {
        return $this->jsonDecode(
            $this->rawRequest("GET", $endpoint, $options)
                ->getBody()
                ->getContents()
        );
    }

    public function post(string $endpoint, array $options = [])
    {
        return $this->jsonDecode(
            $this->rawRequest("POST", $endpoint, $options)
                ->getBody()
                ->getContents()
        );
    }

    public function put(string $endpoint, array $options = [])
    {
        return $this->jsonDecode(
            $this->rawRequest("PUT", $endpoint, $options)
                ->getBody()
                ->getContents()
        );
    }

    public function delete(string $endpoint, array $options = [])
    {
        return $this->jsonDecode(
            $this->rawRequest("DELETE", $endpoint, $options)
                ->getBody()
                ->getContents()
        );
    }

    /**
     * @param $uri string|UriInterface
     */
    public function rawRequest(string $method, $uri, array $options = []): ResponseInterface
    {
        $options['headers']['User-Agent'] = 'Wizaplace-PHP-SDK/'.$this->version;

        return $this->httpClient->request($method, $uri, $this->addAuth($options));
    }

    public function getBaseUri(): ?UriInterface
    {
        return $this->httpClient->getConfig('base_uri');
    }

    /**
     * Does the same as json_decode(), but with proper error handling
     * @see \json_decode()
     */
    private function jsonDecode(string $json, bool $assoc = true, int $depth = 512, int $options = 0)
    {
        if (empty($json)) {
            return null;
        }

        $data = \json_decode($json, $assoc, $depth, $options);

        $lastJsonError = json_last_error();
        if (JSON_ERROR_NONE !== $lastJsonError) {
            throw new JsonDecodingError(
                'Unable to parse JSON data: '.json_last_error_msg(),
                $lastJsonError
            );
        }

        return $data;
    }

    private function addAuth(array $options): array
    {
        if (!is_null($this->apiKey)) {
            $options['headers']['Authorization'] = 'token '.$this->apiKey->getKey();
        }

        return $options;
    }
}
