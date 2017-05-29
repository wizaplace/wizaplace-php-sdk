<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\User;

class ApiKey
{
    /** @var int */
    private $id;

    /** @var string */
    private $key;

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

    public function getKey(): string
    {
        return $this->key;
    }
}
