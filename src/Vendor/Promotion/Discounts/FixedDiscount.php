<?php

/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Vendor\Promotion\Discounts;

use Wizaplace\SDK\Vendor\Promotion\DiscountType;

/**
 * Class FixedDiscount
 * @package Wizaplace\SDK\Vendor\Promotion\Discounts
 */
final class FixedDiscount implements Discount
{
    /**
     * @var float
     */
    private $value;

    /** @var null|float */
    private $maxAmount;

    /**
     * FixedDiscount constructor.
     *
     * @param float $value
     * @param null|float $maxAmount
     */
    public function __construct(float $value, ?float $maxAmount = null)
    {
        $this->value = $value;
        $this->maxAmount = $maxAmount;
    }

    /**
     * @return DiscountType
     */
    public function getType(): DiscountType
    {
        return DiscountType::FIXED();
    }

    /**
     * @return float
     */
    public function getValue(): float
    {
        return $this->value;
    }

    /** @return null|float */
    public function getMaxAmount(): ?float
    {
        return $this->maxAmount;
    }
}
