<?php

/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Vendor\Promotion\Rules;

use Wizaplace\SDK\Vendor\Promotion\CatalogRuleType;

/**
 * Class ProductPriceSuperiorToRule
 * @package Wizaplace\SDK\Vendor\Promotion\Rules
 *
 * Catalog promotion rule which is valid if the product's price is strictly superior to the given value.
 */
final class ProductPriceSuperiorToRule implements CatalogRule
{
    /**
     * @var float
     */
    private $value;

    /**
     * ProductPriceSuperiorToRule constructor.
     *
     * @param float $value
     */
    public function __construct(float $value)
    {
        $this->value = $value;
    }

    /**
     * @return CatalogRuleType
     */
    public function getType(): CatalogRuleType
    {
        return CatalogRuleType::PRODUCT_PRICE_SUPERIOR_TO();
    }

    /**
     * @return float
     */
    public function getValue(): float
    {
        return $this->value;
    }
}
