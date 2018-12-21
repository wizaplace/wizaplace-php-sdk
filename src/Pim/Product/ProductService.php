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

    public function getShipping(int $productId, int $shippingId) : Shipping
    {
        $this->client->mustBeAuthenticated();
        try {
            $data = $this->client->get("products/${productId}/shippings/${shippingId}");
        } catch (ClientException $e) {
            if ($e->getCode() === 404) {
                throw new NotFound("product #${productId} or shipping #${shippingId} not found", $e);
            }

            throw $e;
        }

        return new Shipping($data);
    }

    /**
     * @param int $productId
     *
     * @return Shipping[]
     * @throws NotFound
     * @throws \Wizaplace\SDK\Authentication\AuthenticationRequired
     */
    public function getShippings(int $productId) : array
    {
        $shippings = [];

        $this->client->mustBeAuthenticated();
        try {
            $data = $this->client->get("products/${productId}/shippings");

            foreach ($data as $shipping) {
                $shippings[] = new Shipping($shipping);
            }
        } catch (ClientException $e) {
            if ($e->getCode() === 404) {
                throw new NotFound("product #${productId} not found", $e);
            }

            throw $e;
        }

        return $shippings;
    }

    public function putShipping(int $shippingId, UpdateShippingCommand $command) : void
    {
        $this->client->mustBeAuthenticated();

        $command->validate();

        $productId = $command->getProductId();

        try {
            $this->client->put("products/${productId}/shippings/${shippingId}", [
                RequestOptions::JSON => $command->toArray(),
            ]);
        } catch (ClientException $e) {
            if ($e->getCode() === 404) {
                throw new NotFound("product #${productId} or shipping #${shippingId} not found", $e);
            }

            throw $e;
        }
    }

    public function addVideo(int $productId, string $url) : array
    {
        $this->client->mustBeAuthenticated();

        try {
            return $this->client->post("products/${productId}/video", [
                RequestOptions::FORM_PARAMS => [
                    'url' => $url,
                ],
            ]);
        } catch (ClientException $e) {
            if ($e->getCode() === 404) {
                throw new \Exception("Product #${productId} not found", $e);
            }

            if ($e->getCode() === 500) {
                throw new \Exception("Unable to upload video", $e);
            }

            throw $e;
        }
    }

    public function deleteVideo(int $productId)
    {
        $this->client->mustBeAuthenticated();

        try {
            return $this->client->rawRequest("DELETE", "products/${productId}/video");
        } catch (ClientException $e) {
            if ($e->getCode() === 404) {
                throw new \Exception("Product #${productId} not found", $e);
            }

            throw $e;
        }
    }
}
