<?php

/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Vendor\Promotion\Rules;

use Wizaplace\SDK\Vendor\Promotion\BasketRuleType;

/**
 * Class MaxUsageCountPerUserRule
 * @package Wizaplace\SDK\Vendor\Promotion\Rules
 */
final class MaxUsageCountPerUserRule implements BasketRule
{
    /** @var int */
    private $value;

    /**
     * MaxUsageCountPerUserRule constructor.
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
        return BasketRuleType::MAX_USAGE_COUNT_PER_USER();
    }

    /**
     * @return int
     */
    public function getValue(): int
    {
        return $this->value;
    }
}
