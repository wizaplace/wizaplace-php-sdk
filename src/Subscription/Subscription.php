<?php

/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @license     Proprietary
 * @copyright   Copyright (c) Wizacha
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Subscription;

final class Subscription extends SubscriptionSummary
{
    /** @var SubscriptionItem[] */
    private $items;

    /** @var SubscriptionTax[] */
    private $taxes;

    public function __construct(array $data)
    {
        parent::__construct($data);

        $this->items = array_map(
            function (array $_data): SubscriptionItem {
                return new SubscriptionItem($_data);
            },
            $data['items']
        );
        $this->taxes = array_map(
            function (array $_data): SubscriptionTax {
                return new SubscriptionTax($_data);
            },
            $data['taxes']
        );
    }

    /** @return SubscriptionItem[] */
    public function getItems(): array
    {
        return $this->items;
    }

    /** @return SubscriptionTax[] */
    public function getTaxes(): array
    {
        return $this->taxes;
    }
}
