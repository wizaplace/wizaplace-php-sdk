<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types=1);

namespace Wizaplace\SDK\Vendor\Order;

/**
 * Manage a AmountTaxesDetail Collection
 */
class AmountsTaxesDetails implements \Countable
{
    public const TOTALS = 'totals';
    public const SHIPPING_COSTS = 'shipping_costs';
    public const COMMISSIONS = 'commissions';
    public const VENDOR_SHARE = 'vendor_share';

    /**
     * @var AmountTaxesDetail[]
     */
    private $items = [];

    public function get(string $name): ?AmountTaxesDetail
    {
        foreach ($this->items as $item) {
            if ($item->getName() === $name) {
                return $item;
            }
        }

        return null;
    }

    public function add(AmountTaxesDetail $amountTaxDetail): AmountsTaxesDetails
    {
        $this->items[] = $amountTaxDetail;

        return $this;
    }

    public function count(): int
    {
        return count($this->items);
    }

    public static function getKeys(): array
    {
        return [
            static::TOTALS,
            static::SHIPPING_COSTS,
            static::COMMISSIONS,
            static::VENDOR_SHARE,
        ];
    }
}
