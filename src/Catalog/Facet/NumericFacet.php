<?php

/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Catalog\Facet;

/**
 * Facet represented by a numeric range. For example, the price facet.
 */
final class NumericFacet extends Facet
{
    /** @var float */
    private $min;

    /** @var float */
    private $max;

    /**
     * @internal
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        parent::__construct($data);
        $this->min = $data['values']['min'] ?? 0;
        $this->max = $data['values']['max'] ?? PHP_INT_MAX;
    }

    public function getMin(): float
    {
        return $this->min;
    }

    public function getMax(): float
    {
        return $this->max;
    }
}
