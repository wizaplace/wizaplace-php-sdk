<?php

/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Authentication;

use Wizaplace\SDK\ApiClient;

/**
 * Class ApiKey
 * @package Wizaplace\SDK\Authentication
 *
 * @see ApiClient::authenticate()
 */
final class ApiKey
{
    /** @var int */
    private $id;

    /** @var string */
    private $key;

    /**
     * @internal
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->key = $data['apiKey'];
        $this->id = $data['id'];
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }
}
