<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\Catalog;

use Wizaplace\Pagination;

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
     */
    public function __construct(array $data)
    {
        $this->products = array_map(static function (array $product) : ProductSummary {
            return new ProductSummary($product);
        }, $data['results']);
        $this->pagination = new Pagination($data['pagination']);
        $this->facets = array_map(static function (array $facet) : Facet {
            return new Facet($facet);
        }, $data['facets']);
    }

    /**
     * @return ProductSummary[]
     */
    public function getProducts(): array
    {
        return $this->products;
    }

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
