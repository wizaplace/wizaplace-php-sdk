<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;
use Wizaplace\Exception\AuthenticationRequired;
use Wizaplace\Exception\JsonDecodingError;
use Wizaplace\User\ApiKey;

final class ApiClient
{
    /** @var Client */
    private $client;

    /** @var null|ApiKey */
    private $apiKey;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function setApiKey(?ApiKey $apiKey = null): void
    {
        $this->apiKey = $apiKey;
    }

    public function getApiKey(): ?ApiKey
    {
        return $this->apiKey;
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
        try {
            return $this->client->request($method, $uri, $this->addAuth($options));
        } catch (ClientException $e) {
            if (is_null($this->apiKey) && $e->getResponse()->getStatusCode() === 401) {
                throw new AuthenticationRequired($e);
            } else {
                throw $e;
            }
        }
    }

    public function getBaseUri(): ?UriInterface
    {
        return $this->client->getConfig('base_uri');
    }

    /**
     * Does the same as json_decode(), but with proper error handling
     * @see \json_decode()
     */
    private function jsonDecode(string $json, bool $assoc = true, int $depth = 512, int $options = 0)
    {
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
        if ($this->apiKey) {
            $options['headers']['Authorization'] = 'token '.$this->apiKey->getKey();
        }

        return $options;
    }
}
