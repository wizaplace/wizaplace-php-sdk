<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types=1);

namespace Wizaplace\SDK\Vendor\Promotion\Rules;

use Wizaplace\SDK\Vendor\Promotion\BasketRuleType;

/**
 * Class BasketHasProductInListRule
 * @package Wizaplace\SDK\Vendor\Promotion\Rules
 */
final class BasketHasProductInListRule implements BasketRule
{
    /**
     * @var int[]
     */
    private $productsIds;

    /**
     * BasketHasProductInListRule constructor.
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
    public function getType(): BasketRuleType
    {
        return BasketRuleType::BASKET_HAS_PRODUCT_IN_LIST();
    }
}
