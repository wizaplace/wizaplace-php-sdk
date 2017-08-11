<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\Authentication;

use Wizaplace\ApiClient;

/**
 * @see ApiClient::authenticate()
 */
class ApiKey
{
    /** @var int */
    private $id;

    /** @var string */
    private $key;

    /**
     * @internal
     */
    public function __construct(array $data)
    {
        $this->key = $data['apiKey'];
        $this->id = $data['id'];
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getKey(): string
    {
        return $this->key;
    }
}
