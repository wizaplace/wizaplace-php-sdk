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
     */
    public function __construct(array $data)
    {
        parent::__construct($data);
        $this->min = $data['values']['min'];
        $this->max = $data['values']['max'];
    }

    public function getMin(): float
    {
        return $this->min;
    }

    public function getMax(): float
    {
        return $this->max;
    }

    /**
     * @inheritdoc
     */
    public function jsonSerialize(): array
    {
        return array_merge(parent::jsonSerialize(), [
            'type' => 'numeric',
            'min' => $this->getMin(),
            'max' => $this->getMax(),
        ]);
    }
}
