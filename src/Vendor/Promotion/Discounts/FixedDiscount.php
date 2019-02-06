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

    /**
     * FixedDiscount constructor.
     *
     * @param float $value
     */
    public function __construct(float $value)
    {
        $this->value = $value;
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
}
