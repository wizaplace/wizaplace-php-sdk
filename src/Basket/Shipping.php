<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\SDK\Basket;

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

    /**
     * @internal
     */
    public function __construct(array $data)
    {
        $this->id = $data['id'];
        $this->name = $data['name'];
        $this->price = $data['price'];
        $this->deliveryTime = $data['deliveryTime'];
        $this->selected = $data['selected'];
        $this->shippingPrice = new Price($data['shippingPrice']);
    }

    public function getId(): int
    {
        return $this->id;
    }

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

    public function isSelected(): bool
    {
        return $this->selected;
    }

    public function getShippingPrice(): Price
    {
        return $this->shippingPrice;
    }
}
