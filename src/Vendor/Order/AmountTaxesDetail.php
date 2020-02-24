<?php

/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Vendor\Order;

class AmountTaxesDetail
{
    /** @var string */
    private $name;

    /** @var float */
    private $excludingTaxes;

    /** @var float */
    private $taxes;

    /** @var float */
    private $includingTaxes;

    public function __construct(string $name, float $excludingTaxes, float $taxes, float $includingTaxes)
    {
        $this->name = $name;
        $this->excludingTaxes = $excludingTaxes;
        $this->taxes = $taxes;
        $this->includingTaxes = $includingTaxes;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getIncludingTaxes(): float
    {
        return $this->includingTaxes;
    }

    public function getExcludingTaxes(): float
    {
        return $this->excludingTaxes;
    }

    public function getTaxes(): float
    {
        return $this->taxes;
    }
}
