<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types=1);

namespace Wizaplace\SDK\Vendor\Promotion\Discounts;

use Wizaplace\SDK\Vendor\Promotion\DiscountType;

interface Discount
{
    /**
     * @internal for serialization purposes only
     */
    public function getType(): DiscountType;
}
