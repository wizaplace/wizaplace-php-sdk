<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace;

use GuzzleHttp\Client;
use Wizaplace\User\ApiKey;

abstract class AbstractService
{
    /** @var  Client */
    protected $client;

    /** @var string */
    protected $baseUrl;

    public function __construct(string $baseUrl, Client $client)
    {
        $this->baseUrl = $baseUrl;
        $this->client = $client;
    }

    /**
     * @return mixed
     */
    protected function get(string $endpoint, array $options = [], ApiKey $apiKey = null)
    {
        $options = $this->addAuth($options, $apiKey);

        return json_decode(
            $this->client->get($this->baseUrl.$endpoint, $options)
                ->getBody()
                ->getContents(),
            true
        );
    }
    /**
     * @return mixed
     */
    protected function post(string $endpoint, array $options = [], ApiKey $apiKey = null)
    {
        $options = $this->addAuth($options, $apiKey);

        return json_decode(
            $this->client->post($this->baseUrl.$endpoint, $options)
                ->getBody()
                ->getContents(),
            true
        );
    }

    protected function put(string $endpoint, array $options = [], ApiKey $apiKey = null)
    {
        $options = $this->addAuth($options, $apiKey);

        return json_decode(
            $this->client->put($this->baseUrl.$endpoint, $options)
                ->getBody()
                ->getContents(),
            true
        );
    }

    protected function delete(string $endpoint, array $options = [], ApiKey $apiKey = null)
    {
        $options = $this->addAuth($options, $apiKey);

        return json_decode(
            $this->client->delete($this->baseUrl.$endpoint, $options)
                ->getBody()
                ->getContents(),
            true
        );
    }

    private function addAuth(array $options, ?ApiKey $apiKey):array
    {
        if ($apiKey) {
            $options['headers']['Authorization'] = 'token '.$apiKey->getKey();
        }

        return $options;
    }
}
