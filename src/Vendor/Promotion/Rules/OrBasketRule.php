<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types=1);

namespace Wizaplace\SDK\Vendor\Promotion\Rules;

use Wizaplace\SDK\Vendor\Promotion\BasketRuleType;

/**
 * Basket promotion rule which is valid if at least one of its items is valid.
 */
final class OrBasketRule implements BasketRule
{
    /**
     * @var BasketRule[]
     */
    private $items;

    public function __construct(BasketRule ...$items)
    {
        $this->items = $items;
    }

    public function getType(): BasketRuleType
    {
        return BasketRuleType::OR();
    }

    /**
     * @return BasketRule[]
     */
    public function getItems(): array
    {
        return $this->items;
    }
}
