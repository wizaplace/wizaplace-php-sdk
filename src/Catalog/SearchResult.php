<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\SDK\Catalog;

use Wizaplace\SDK\Catalog\Facet\Facet;
use Wizaplace\SDK\Pagination;

final class SearchResult implements \JsonSerializable
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

    /**
     * @inheritdoc
     */
    public function jsonSerialize(): array
    {
        return [
            'products' => $this->getProducts(),
            'pagination' => $this->getPagination(),
            'facets' => $this->getFacets(),
        ];
    }
}
