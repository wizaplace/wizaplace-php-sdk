<?php

/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Vendor\Promotion\Rules;

use Wizaplace\SDK\Vendor\Promotion\BasketRuleType;

/**
 * Class BasketQuantityInferiorToRule
 * @package Wizaplace\SDK\Vendor\Promotion\Rules
 *
 * Basket promotion rule which is valid if the basket's quantity is strictly inferior to the given value.
 */
final class BasketQuantityInferiorToRule implements BasketRule
{
    /**
     * @var int
     */
    private $value;

    /**
     * BasketQuantityInferiorToRule constructor.
     *
     * @param int $value
     */
    public function __construct(int $value)
    {
        $this->value = $value;
    }

    /**
     * @return BasketRuleType
     */
    public function getType(): BasketRuleType
    {
        return BasketRuleType::BASKET_QUANTITY_INFERIOR_TO();
    }

    /**
     * @return int
     */
    public function getValue(): int
    {
        return $this->value;
    }
}
