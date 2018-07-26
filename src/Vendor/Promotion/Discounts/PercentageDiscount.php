<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types=1);

namespace Wizaplace\SDK\Vendor\Promotion\Discounts;

use Wizaplace\SDK\Vendor\Promotion\DiscountType;

final class PercentageDiscount implements Discount
{
    /**
     * @var float
     */
    private $percentage;

    public function __construct(float $percentage)
    {
        $this->percentage = $percentage;
    }

    public function getType(): DiscountType
    {
        return DiscountType::PERCENTAGE();
    }

    public function getPercentage(): float
    {
        return $this->percentage;
    }
}
