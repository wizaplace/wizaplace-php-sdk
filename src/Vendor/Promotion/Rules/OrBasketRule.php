<?php

/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Vendor\Promotion\Rules;

use Wizaplace\SDK\Vendor\Promotion\BasketRuleType;

/**
 * Class OrBasketRule
 * @package Wizaplace\SDK\Vendor\Promotion\Rules
 *
 * Basket promotion rule which is valid if at least one of its items is valid.
 */
final class OrBasketRule implements BasketRule
{
    /**
     * @var BasketRule[]
     */
    private $items;

    /**
     * OrBasketRule constructor.
     *
     * @param BasketRule ...$items
     */
    public function __construct(BasketRule ...$items)
    {
        $this->items = $items;
    }

    /**
     * @return BasketRuleType
     */
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
