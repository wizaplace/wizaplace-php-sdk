<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace;

use GuzzleHttp\Client;
use Wizaplace\Exception\JsonDecodingError;
use Wizaplace\User\ApiKey;

abstract class AbstractService
{
    /** @var Client */
    protected $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    protected function get(string $endpoint, array $options = [], ApiKey $apiKey = null)
    {
        $options = $this->addAuth($options, $apiKey);

        return $this->jsonDecode(
            $this->client->get($endpoint, $options)
                ->getBody()
                ->getContents()
        );
    }

    protected function post(string $endpoint, array $options = [], ApiKey $apiKey = null)
    {
        $options = $this->addAuth($options, $apiKey);

        return $this->jsonDecode(
            $this->client->post($endpoint, $options)
                ->getBody()
                ->getContents()
        );
    }

    protected function put(string $endpoint, array $options = [], ApiKey $apiKey = null)
    {
        $options = $this->addAuth($options, $apiKey);

        return $this->jsonDecode(
            $this->client->put($endpoint, $options)
                ->getBody()
                ->getContents()
        );
    }

    protected function delete(string $endpoint, array $options = [], ApiKey $apiKey = null)
    {
        $options = $this->addAuth($options, $apiKey);

        return $this->jsonDecode(
            $this->client->delete($endpoint, $options)
                ->getBody()
                ->getContents()
        );
    }

    /**
     * Does the same as json_decode(), but with proper error handling
     * @see \json_decode()
     */
    protected function jsonDecode(string $json, bool $assoc = true, int $depth = 512, int $options = 0)
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

    private function addAuth(array $options, ?ApiKey $apiKey): array
    {
        if ($apiKey) {
            $options['headers']['Authorization'] = 'token '.$apiKey->getKey();
        }

        return $options;
    }
}
