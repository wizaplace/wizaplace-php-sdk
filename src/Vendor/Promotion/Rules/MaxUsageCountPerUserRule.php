<?php
declare(strict_types=1);

namespace Wizaplace\SDK\Vendor\Promotion\Rules;

use Wizaplace\SDK\Vendor\Promotion\BasketRuleType;

final class MaxUsageCountPerUserRule implements BasketRule
{
    /** @var int */
    private $value;

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

    public function getValue(): int
    {
        return $this->value;
    }
}
