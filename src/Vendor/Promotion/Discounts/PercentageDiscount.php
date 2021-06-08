<?php

/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Vendor\Promotion\Discounts;

use Wizaplace\SDK\Vendor\Promotion\DiscountType;

/**
 * Class PercentageDiscount
 * @package Wizaplace\SDK\Vendor\Promotion\Discounts
 */
final class PercentageDiscount implements Discount
{
    /**
     * @var float
     */
    private $percentage;

    /** @var null|float */
    private $maxAmount;

    /**
     * PercentageDiscount constructor.
     *
     * @param float $percentage
     * @param null|float $maxAmount
     */
    public function __construct(float $percentage, ?float $maxAmount = null)
    {
        $this->percentage = $percentage;
        $this->maxAmount = $maxAmount;
    }

    /**
     * @return DiscountType
     */
    public function getType(): DiscountType
    {
        return DiscountType::PERCENTAGE();
    }

    /**
     * @return float
     */
    public function getPercentage(): float
    {
        return $this->percentage;
    }

    /** @return null|float */
    public function getMaxAmount(): ?float
    {
        return $this->maxAmount;
    }
}
