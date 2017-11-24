<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types=1);

namespace Wizaplace\SDK\Pim\Product;

use Wizaplace\SDK\Pagination;

class ProductList
{
    /** @var ProductSummary[] */
    private $products;

    /** @var Pagination */
    private $pagination;

    /**
     * @internal
     */
    public function __construct(array $data)
    {
        $this->products = array_map(static function (array $data): ProductSummary {
            return new ProductSummary($data);
        }, $data['products']);
        $this->pagination = new Pagination([
            'page' => $data['params']['page'],
            'nbResults' => $data['params']['total_items'],
            'nbPages' => floor($data['params']['total_items']/$data['params']['items_per_page']) + ($data['params']['total_items']%$data['params']['items_per_page'] ? 1 : 0),
            'resultsPerPage' => $data['params']['items_per_page'],
        ]);
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
}
