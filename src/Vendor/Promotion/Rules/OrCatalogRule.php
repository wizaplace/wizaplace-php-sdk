<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types=1);

namespace Wizaplace\SDK\Vendor\Promotion\Rules;

use Wizaplace\SDK\Vendor\Promotion\CatalogRuleType;

/**
 * Class OrCatalogRule
 * @package Wizaplace\SDK\Vendor\Promotion\Rules
 *
 * Catalog promotion rule which is valid if at least one of its items is valid.
 */
final class OrCatalogRule implements CatalogRule
{
    /**
     * @var CatalogRule[]
     */
    private $items;

    /**
     * OrCatalogRule constructor.
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
        return CatalogRuleType::OR();
    }

    /**
     * @return CatalogRule[]
     */
    public function getItems(): array
    {
        return $this->items;
    }
}
