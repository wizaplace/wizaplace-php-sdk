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

    /**
     * PercentageDiscount constructor.
     *
     * @param float $percentage
     */
    public function __construct(float $percentage)
    {
        $this->percentage = $percentage;
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
}
