<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types=1);

namespace Wizaplace\SDK\Pim\Product;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\RequestOptions;
use Wizaplace\SDK\AbstractService;
use Wizaplace\SDK\Exception\NotFound;
use function theodorejb\polycast\to_int;

final class ProductService extends AbstractService
{
    public function getProductById(int $productId): Product
    {
        $this->client->mustBeAuthenticated();
        try {
            $data = $this->client->get("products/$productId");
        } catch (ClientException $e) {
            if ($e->getCode() === 404) {
                throw new NotFound("product #${productId} not found", $e);
            }
            throw $e;
        }

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

    public function updateProduct(UpdateProductCommand $command): int
    {
        $this->client->mustBeAuthenticated();
        $command->validate();

        $id = $command->getId();

        $data = $this->client->put("products/${id}", [
            RequestOptions::JSON => $command->toArray(),
        ]);

        return to_int($data['product_id']);
    }

    public function deleteProduct(int $productId): void
    {
        $this->client->mustBeAuthenticated();
        $this->client->delete("products/${productId}");
    }
}
