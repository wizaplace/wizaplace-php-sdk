<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @license     Proprietary
 * @copyright   Copyright (c) Wizacha
 */
declare(strict_types=1);

namespace Wizaplace\SDK;

final class Price
{
    /** @var float */
    private $excludingTaxes;

    /** @var float */
    private $includingTaxes;

    /** @var float */
    private $taxes;

    public function __construct(array $data)
    {
        $this->excludingTaxes = floatval($data['excludingTaxes']);
        $this->includingTaxes = floatval($data['includingTaxes']);
        $this->taxes = floatval($data['taxes']);
    }

    public function getExcludingTaxes(): float
    {
        return $this->excludingTaxes;
    }

    public function getIncludingTaxes(): float
    {
        return $this->includingTaxes;
    }

    public function getTaxes(): float
    {
        return $this->taxes;
    }
}
