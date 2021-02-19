<?php

/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @license     Proprietary
 * @copyright   Copyright (c) Wizacha
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Subscription;

class SubscriptionPriceTier
{
    /** @var int */
    protected $lowerLimit;

    /** @var null|float */
    protected $includingTaxes;

    /** @var null|float */
    protected $excludingTaxes;

    /** @var null|float */
    protected $taxes;

    public function __construct($priceTiers)
    {
        $this->lowerLimit = $priceTiers['lowerLimit'];
        $this->includingTaxes = $priceTiers['includingTaxes'];
        $this->excludingTaxes = $priceTiers['excludingTaxes'];
        $this->taxes = $priceTiers['taxes'];
    }

    public function getLowerLimit(): int
    {
        return $this->lowerLimit;
    }

    public function getIncludingTaxes(): ?float
    {
        return $this->includingTaxes;
    }

    public function getExcludingTaxes(): ?float
    {
        return $this->excludingTaxes;
    }

    public function getTaxes(): ?float
    {
        return $this->taxes;
    }
}
