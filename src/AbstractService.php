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

    protected function jsonDecode(string $json)
    {
        $result = json_decode($json, true);
        if (is_null($result)) {
            $jsonLastErrorCode = json_last_error();
            if ($jsonLastErrorCode !== JSON_ERROR_NONE) {
                throw new JsonDecodingError(json_last_error_msg(), $jsonLastErrorCode);
            }
        }

        return $result;
    }

    private function addAuth(array $options, ?ApiKey $apiKey):array
    {
        if ($apiKey) {
            $options['headers']['Authorization'] = 'token '.$apiKey->getKey();
        }

        return $options;
    }
}
