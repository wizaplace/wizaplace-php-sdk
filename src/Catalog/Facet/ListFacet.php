<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types=1);

namespace Wizaplace\SDK\Catalog\Facet;

/**
 * Class ListFacet
 * @package Wizaplace\SDK\Catalog\Facet
 *
 * Facet represented by a list of values you can choose from.
 */
final class ListFacet extends Facet
{
    /** @var ListFacetValue[] */
    private $values;

    /**
     * @internal
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        parent::__construct($data);
        $this->values = array_map(static function (array $valueData) {
            return new ListFacetValue($valueData);
        }, $data['values']);
    }

    /**
     * @return ListFacetValue[]
     */
    public function getValues(): array
    {
        return $this->values;
    }
}
