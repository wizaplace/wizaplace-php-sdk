<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types=1);

namespace Wizaplace\SDK\Pim\Product;

use GuzzleHttp\RequestOptions;
use Wizaplace\SDK\AbstractService;

final class ProductService extends AbstractService
{
    public function getProductById(int $productId): Product
    {
        $this->client->mustBeAuthenticated();
        $data = $this->client->get("products/$productId");

        return new Product($data);
    }

    public function listProducts(?ProductListFilter $filter = null, $page = 1, $itemsCountPerPage = 100): ProductList
    {
        $this->client->mustBeAuthenticated();
        $query = [
            'page' => $page,
            'items_per_page' => $itemsCountPerPage,
        ];

        if ($filter) {
            $query = array_merge($query, $filter->toArray());
        }

        $data = $this->client->get('products', [
            RequestOptions::QUERY => $query,
        ]);

        return new ProductList($data);
    }
}
