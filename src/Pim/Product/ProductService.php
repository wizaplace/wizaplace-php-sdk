<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types=1);

namespace Wizaplace\SDK\Pim\Product;

use GuzzleHttp\RequestOptions;
use Wizaplace\SDK\AbstractService;
use function theodorejb\polycast\to_int;

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

    public function createProduct(CreateProductCommand $command): int
    {
        $this->client->mustBeAuthenticated();
        $command->validate();

        $data = $this->client->post('products', [
            RequestOptions::JSON => $command->toArray(),
        ]);

        return to_int($data['product_id']);
    }
}
