<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\SDK\Catalog\Search;

use Wizaplace\SDK\Pagination;

final class Result
{
    /** @var Product[] */
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
        $this->products = array_map(static function (array $product) : Product {
            return new Product($product);
        }, $data['results']);
        $this->pagination = new Pagination($data['pagination']);
        $this->facets = array_map(static function (array $facet) : Facet {
            return new Facet($facet);
        }, $data['facets']);
    }

    /**
     * @return Product[]
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
