<?php

/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Pim\Product;

class ProductInventory
{
    /** @var array */
    private $combination;

    /** @var int */
    private $amount;

    /** @var float */
    private $price;

    public function __construct(array $data)
    {
        $this->combination = $data['combination'];
        $this->amount = $data['amount'];
        $this->price = $data['price'];
    }

    public function setCombination(array $combination): self
    {
        $this->combination = $combination;

        return $this;
    }

    public function getCombination(): array
    {
        return $this->combination;
    }

    public function setAmount(int $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getAmount(): int
    {
        return $this->amount;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getPrice(): float
    {
        return $this->price;
    }
}
