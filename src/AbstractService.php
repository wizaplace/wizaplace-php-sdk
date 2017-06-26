<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace;

abstract class AbstractService
{
    /** @var ApiClient $client */
    protected $client;

    public function __construct(ApiClient $client)
    {
        $this->client = $client;
    }
}
