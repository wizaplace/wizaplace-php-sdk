<?php

/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Basket;

/**
 * Class BasketOrder
 * @package Wizaplace\SDK\Basket
 */
final class BasketOrder
{
    /** @var int */
    private $id;

    /**
     * @internal
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->id = $data['id'];
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }
}
