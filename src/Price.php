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
        $this->excludingTaxes = \floatval(0);
        $this->includingTaxes = \floatval(0);

        if (isset($data['excludingTaxes'])) {
            $this->excludingTaxes = \floatval($data['excludingTaxes']);
        }

        if (isset($data['excluding_taxes'])) {
            $this->excludingTaxes = \floatval($data['excluding_taxes']);
        }

        if (isset($data['includingTaxes'])) {
            $this->includingTaxes = \floatval($data['includingTaxes']);
        }

        if (isset($data['including_taxes'])) {
            $this->includingTaxes = \floatval($data['including_taxes']);
        }

        $this->taxes = \floatval($data['taxes'] ?? 0);
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
