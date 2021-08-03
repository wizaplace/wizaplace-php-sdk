<?php

/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Vendor\Promotion\Targets;

use Wizaplace\SDK\Vendor\Promotion\BasketPromotionTargetType;

/**
 * Class CategoriesTarget
 * @package Wizaplace\SDK\Vendor\Promotion\Targets
 */
final class CategoriesTarget implements BasketPromotionTarget
{
    /** @var int[] */
    private $categoriesIds;

    /**
     * CategoriesTarget constructor.
     *
     * @param int ...$categoriesIds
     */
    public function __construct(int ...$categoriesIds)
    {
        $this->categoriesIds = $categoriesIds;
    }

    /**
     * @return int[]
     */
    public function getCategoriesIds(): array
    {
        return $this->categoriesIds;
    }

    /**
     * @internal for serialization purposes only
     */
    public function getType(): BasketPromotionTargetType
    {
        return BasketPromotionTargetType::PRODUCT_CATEGORY();
    }
}
