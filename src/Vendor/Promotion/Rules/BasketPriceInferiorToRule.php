<?php

/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Vendor\Promotion\Rules;

use Wizaplace\SDK\Vendor\Promotion\BasketRuleType;

/**
 * Class BasketPriceInferiorToRule
 * @package Wizaplace\SDK\Vendor\Promotion\Rules
 *
 * Basket promotion rule which is valid if the basket's price is strictly inferior to the given value.
 */
final class BasketPriceInferiorToRule implements BasketRule
{
    /**
     * @var float
     */
    private $value;

    /**
     * BasketPriceInferiorToRule constructor.
     *
     * @param float $value
     */
    public function __construct(float $value)
    {
        $this->value = $value;
    }

    /**
     * @return BasketRuleType
     */
    public function getType(): BasketRuleType
    {
        return BasketRuleType::BASKET_PRICE_INFERIOR_TO();
    }

    /**
     * @return float
     */
    public function getValue(): float
    {
        return $this->value;
    }
}
