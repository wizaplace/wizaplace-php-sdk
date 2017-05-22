<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */

namespace Wizaplace\Basket;

class Shipping
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

    public function __construct(array $data)
    {
        $this->id = $data['id'];
        $this->name = $data['name'];
        $this->price = $data['price'];
        $this->deliveryTime = $data['deliveryTime'];
        $this->selected = $data['selected'];
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function getDeliveryTime(): string
    {
        return $this->deliveryTime;
    }

    public function isSelected(): bool
    {
        return $this->selected;
    }
}
