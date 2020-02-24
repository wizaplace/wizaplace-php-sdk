<?php

/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Vendor\Order;

/**
 * Class Tax
 * @package Wizaplace\SDK\Vendor\Order
 */
class Tax
{
    /** @var int */
    private $id;

    /** @var string */
    private $name;

    /**
     * @internal
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->id = $data['tax_id'];
        $this->name = $data['tax'];
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
    public function getName(): string
    {
        return $this->name;
    }
}
