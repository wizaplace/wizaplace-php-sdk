<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\SDK\Catalog;

use Wizaplace\SDK\Catalog\Facet\Facet;
use Wizaplace\SDK\Pagination;

/**
 * Class SearchResult
 * @package Wizaplace\SDK\Catalog
 */
final class SearchResult
{
    /** @var ProductSummary[] */
    private $products;
    /** @var Pagination */
    private $pagination;
    /** @var Facet[] */
    private $facets;

    /**
     * @internal
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->products = array_map(static function (array $product) : ProductSummary {
            return new ProductSummary($product);
        }, $data['results']);
        $this->pagination = new Pagination($data['pagination']);
        $this->facets = array_map(static function (array $facet) : Facet {
            return Facet::buildFromJson($facet);
        }, $data['facets']);
    }

    /**
     * @return ProductSummary[]
     */
    public function getProducts(): array
    {
        return $this->products;
    }

    /**
     * @return Pagination
     */
    public function getPagination(): Pagination
    {
        return $this->pagination;
    }

    /**
     * @return Facet[]
     */
    public function getFacets(): array
    {
        return $this->facets;
    }
}
