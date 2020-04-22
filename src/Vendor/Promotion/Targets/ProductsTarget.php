<?php

/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Vendor\Promotion\Targets;

use Wizaplace\SDK\Vendor\Promotion\BasketPromotionTargetType;

/**
 * Class ProductsTarget
 * @package Wizaplace\SDK\Vendor\Promotion\Targets
 */
final class ProductsTarget implements BasketPromotionTarget
{
    /** @var int[] */
    private $productsIds;

    /**
     * ProductsTarget constructor.
     *
     * @param int ...$productsIds
     */
    public function __construct(int ...$productsIds)
    {
        $this->productsIds = $productsIds;
    }

    /**
     * @return int[]
     */
    public function getProductsIds(): array
    {
        return $this->productsIds;
    }

    /**
     * @internal for serialization purposes only
     */
    public function getType(): BasketPromotionTargetType
    {
        return BasketPromotionTargetType::PRODUCTS();
    }
}
