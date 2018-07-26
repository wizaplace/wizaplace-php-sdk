<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types=1);

namespace Wizaplace\SDK\Vendor\Promotion\Rules;

use Wizaplace\SDK\Vendor\Promotion\CatalogRuleType;

/**
 * Catalog promotion rule which is valid if the product's price is strictly superior to the given value.
 */
final class ProductPriceSuperiorToRule implements CatalogRule
{
    /**
     * @var float
     */
    private $value;

    public function __construct(float $value)
    {
        $this->value = $value;
    }

    public function getType(): CatalogRuleType
    {
        return CatalogRuleType::PRODUCT_PRICE_SUPERIOR_TO();
    }

    public function getValue(): float
    {
        return $this->value;
    }
}
