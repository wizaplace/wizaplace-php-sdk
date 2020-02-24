<?php

/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Vendor\Promotion\Rules;

use Wizaplace\SDK\Vendor\Promotion\BasketRuleType;

/**
 * Class AndBasketRule
 * @package Wizaplace\SDK\Vendor\Promotion\Rules
 *
 * Basket promotion rule which is valid if all its items are valid.
 */
final class AndBasketRule implements BasketRule
{
    /**
     * @var BasketRule[]
     */
    private $items;

    /**
     * AndBasketRule constructor.
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
        return BasketRuleType::AND();
    }

    /**
     * @return BasketRule[]
     */
    public function getItems(): array
    {
        return $this->items;
    }
}
