<?php

/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Vendor\Promotion\Rules;

use Wizaplace\SDK\Vendor\Promotion\CatalogRuleType;

/**
 * Class AndCatalogRule
 * @package Wizaplace\SDK\Vendor\Promotion\Rules
 *
 * Catalog promotion rule which is valid if all its items are valid.
 */
final class AndCatalogRule implements CatalogRule
{
    /**
     * @var CatalogRule[]
     */
    private $items;

    /**
     * AndCatalogRule constructor.
     *
     * @param CatalogRule ...$items
     */
    public function __construct(CatalogRule ...$items)
    {
        $this->items = $items;
    }

    /**
     * @return CatalogRuleType
     */
    public function getType(): CatalogRuleType
    {
        return CatalogRuleType::AND();
    }

    /**
     * @return CatalogRule[]
     */
    public function getItems(): array
    {
        return $this->items;
    }
}
