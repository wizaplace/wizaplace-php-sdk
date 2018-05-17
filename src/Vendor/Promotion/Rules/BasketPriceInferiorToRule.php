<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types=1);

namespace Wizaplace\SDK\Vendor\Promotion\Rules;

use Wizaplace\SDK\Vendor\Promotion\BasketRuleType;

/**
 * Basket promotion rule which is valid if the basket's price is strictly inferior to the given value.
 */
final class BasketPriceInferiorToRule implements BasketRule
{
    /**
     * @var float
     */
    private $value;

    public function __construct(float $value)
    {
        $this->value = $value;
    }

    public function getType(): BasketRuleType
    {
        return BasketRuleType::BASKET_PRICE_INFERIOR_TO();
    }

    public function getValue(): float
    {
        return $this->value;
    }
}
