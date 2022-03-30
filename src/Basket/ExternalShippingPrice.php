<?php

/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @license     Proprietary
 * @copyright   Copyright (c) Wizacha
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Basket;

/**
 * Class Shipping
 * Used to set custom price for shipping in basket
 * @package Wizaplace\SDK\Basket
 */
class ExternalShippingPrice
{
    /** @var int $shippingGroupId */
    private $shippingGroupId;

    /** @var int $shippingId */
    private $shippingId;

    /** @var float $price */
    private $price;

    public function __construct(int $shippingGroupId, int $shippingId, float $price)
    {
        $this->shippingGroupId = $shippingGroupId;
        $this->shippingId = $shippingId;
        $this->price = $price;
    }

    public function getShippingGroupId(): int
    {
        return $this->shippingGroupId;
    }

    public function setShippingGroupId(int $shippingGroupId): self
    {
        $this->shippingGroupId = $shippingGroupId;

        return $this;
    }

    public function getShippingId(): int
    {
        return $this->shippingId;
    }

    public function setShippingId(int $shippingId): self
    {
        $this->shippingId = $shippingId;

        return $this;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }
}
