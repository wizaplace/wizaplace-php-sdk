<?php

/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Basket;

/**
 * Class Shipping
 * @package Wizaplace\SDK\Basket
 */
final class Shipping
{
    /** @var int */
    private $id;

    /** @var string */
    private $name;

    /** @var float */
    private $price;

    /** @var string */
    private $deliveryTime;

    /** @var bool */
    private $selected;

    /** @var Price */
    private $shippingPrice;

    /** @var null|string  */
    private $image;

    /** @var null|float */
    private $carriagePaidThreshold;

    /**
     * @internal
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->id = $data['id'];
        $this->name = $data['name'];
        $this->price = $data['price'];
        $this->deliveryTime = $data['deliveryTime'];
        $this->selected = $data['selected'];
        $this->shippingPrice = new Price($data['shippingPrice']);
        $this->image = $data['image'];
        $this->carriagePaidThreshold = $data['carriagePaidThreshold'] ?? null;
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

    /**
     * @deprecated {@see \Wizaplace\SDK\Basket\Shipping::getShippingPrice} instead
     */
    public function getPrice(): float
    {
        return $this->price;
    }

    /**
     * @return string example: "24h"
     */
    public function getDeliveryTime(): string
    {
        return $this->deliveryTime;
    }

    /**
     * @return bool
     */
    public function isSelected(): bool
    {
        return $this->selected;
    }

    /**
     * @return Price
     */
    public function getShippingPrice(): Price
    {
        return $this->shippingPrice;
    }

    /**
     * @return string|null
     */
    public function getImage(): ?string
    {
        return $this->image;
    }

    public function getCarriagePaidThreshold(): ?float
    {
        return $this->carriagePaidThreshold;
    }
}
