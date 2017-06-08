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

    protected function jsonDecode(string $json, $assoc = true, $depth = 512, $options = 0)
    {
        if ($json === '' || $json === null) {
            return null;
        }

        $data = \json_decode($json, $assoc, $depth, $options);

        if (JSON_ERROR_NONE !== json_last_error()) {
            $last = json_last_error();
            $message = 'Unable to parse JSON data: '.json_last_error_msg();
            throw new JsonDecodingError($message, $last);
        }

        return $data;
    }

    private function addAuth(array $options, ?ApiKey $apiKey):array
    {
        if ($apiKey) {
            $options['headers']['Authorization'] = 'token '.$apiKey->getKey();
        }

        return $options;
    }
}
