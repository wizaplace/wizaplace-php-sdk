<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types=1);

namespace Wizaplace\SDK\Vendor\Promotion\Rules;

use Wizaplace\SDK\Vendor\Promotion\CatalogRuleType;

/**
 * Catalog promotion rule which is valid if the product is in the given list.
 */
final class ProductInListRule implements CatalogRule
{
    /**
     * @var int[]
     */
    private $productsIds;

    public function __construct(int ...$productsIds)
    {
        $this->productsIds = $productsIds;
    }

    public function getType(): CatalogRuleType
    {
        return CatalogRuleType::PRODUCT_IN_LIST();
    }

    /**
     * @return int[]
     */
    public function getProductsIds(): array
    {
        return $this->productsIds;
    }
}
