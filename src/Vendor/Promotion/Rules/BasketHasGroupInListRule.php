<?php

/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Vendor\Promotion\Rules;

use Wizaplace\SDK\Vendor\Promotion\BasketRuleType;

/**
 * Class BasketHasProductInListRule
 * @package Wizaplace\SDK\Vendor\Promotion\Rules
 */
final class BasketHasGroupInListRule implements BasketRule
{
    /** @var string[] */
    private $groupsIds;

    /**
     * BasketHasGroupInListRule constructor.
     *
     * @param  string[] $groupsIds
     */
    public function __construct(array $groupsIds)
    {
        $this->groupsIds = $groupsIds;
    }

    /** @return string[] */
    public function getGroupsIds(): array
    {
        return $this->groupsIds;
    }

    public function getType(): BasketRuleType
    {
        return BasketRuleType::BASKET_HAS_GROUP_IN_LIST();
    }
}
