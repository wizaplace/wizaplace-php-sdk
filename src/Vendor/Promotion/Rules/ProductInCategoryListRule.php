<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types=1);

namespace Wizaplace\SDK\Vendor\Promotion\Rules;

use Wizaplace\SDK\Vendor\Promotion\CatalogRuleType;

/**
 * Catalog promotion rule which is valid if the product belongs to at least one of the given categories.
 */
final class ProductInCategoryListRule implements CatalogRule
{
    /**
     * @var int[]
     */
    private $categoriesIds;

    public function __construct(int ...$categoriesIds)
    {
        $this->categoriesIds = $categoriesIds;
    }

    public function getType(): CatalogRuleType
    {
        return CatalogRuleType::PRODUCT_IN_CATEGORY_LIST();
    }

    /**
     * @return int[]
     */
    public function getCategoriesIds(): array
    {
        return $this->categoriesIds;
    }
}
