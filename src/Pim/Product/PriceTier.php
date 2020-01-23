<?php
/**
 *  @author      Wizacha DevTeam <dev@wizacha.com>
 *  @copyright   Copyright (c) Wizacha
 *  @license     Proprietary
 */
declare(strict_types=1);

namespace Wizaplace\SDK\Pim\Product;

/**
 * PriceTiers is a feature. If this feature is not activated on your environment, every product and every declination will have a unique price tier, with lowerLimit 0
 * A priceTier is a price for a quantity.
 * Example: You want set a price to 10€ if quantity is between 0 and 10, and a price of 9€ if quantity is greater than 10:
 * lowerLimit 0
 * price 10
 *
 * lowerLimit 11
 * price 9
 * Regarding the last priceTier (the one with the greater lowerLimit), its price applies for a quantity between its lowerLimit and ∞
 */
class PriceTier
{
    /** @var int */
    protected $lowerLimit;

    /** @var null|float */
    protected $price;

    public function __construct(array $data)
    {
        $this->lowerLimit = $data['lowerLimit'];
        $this->price = $data['price'] ?? null;
    }

    /** @return int */
    public function getLowerLimit(): int
    {
        return $this->lowerLimit;
    }

    /** @param int $lowerLimit */
    public function setLowerLimit(int $lowerLimit): self
    {
        $this->lowerLimit = $lowerLimit;

        return $this;
    }

    /** @return null|float */
    public function getPrice(): ?float
    {
        return $this->price;
    }

    /** @param float $price */
    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }
}
