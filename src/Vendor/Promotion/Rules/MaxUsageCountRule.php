<?php

/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Vendor\Promotion\Rules;

use Wizaplace\SDK\Vendor\Promotion\BasketRuleType;

/**
 * Class MaxUsageCountRule
 * @package Wizaplace\SDK\Vendor\Promotion\Rules
 */
final class MaxUsageCountRule implements BasketRule
{
    /** @var int */
    private $value;

    /**
     * MaxUsageCountRule constructor.
     *
     * @param int $value
     */
    public function __construct(int $value)
    {
        $this->value = $value;
    }

    /**
     * @internal for serialization purposes only
     */
    public function getType(): BasketRuleType
    {
        return BasketRuleType::MAX_USAGE_COUNT();
    }

    /**
     * @return int
     */
    public function getValue(): int
    {
        return $this->value;
    }
}
