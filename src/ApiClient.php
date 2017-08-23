<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;
use Wizaplace\Authentication\ApiKey;
use Wizaplace\Authentication\AuthenticationRequired;
use Wizaplace\Authentication\BadCredentials;
use Wizaplace\Exception\JsonDecodingError;

final class ApiClient
{
    /** @var Client */
    private $httpClient;

    /** @var null|ApiKey */
    private $apiKey;

    public function __construct(Client $client)
    {
        $this->httpClient = $client;
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
