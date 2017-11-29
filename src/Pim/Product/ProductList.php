<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types=1);

namespace Wizaplace\SDK\Pim\Product;

use Wizaplace\SDK\Pagination;
use function theodorejb\polycast\to_int;

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
            'nbPages' => $this->calculateNbPages(to_int($data['params']['items_per_page']), to_int($data['params']['total_items'])),
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

    private function calculateNbPages(int $itemsPerPage, int $totalItems) : int
    {
        $nbPages = to_int(floor($totalItems / $itemsPerPage));

        if (($totalItems % $itemsPerPage) !== 0) {
            $nbPages++;
        }

        return $nbPages;
    }
}
