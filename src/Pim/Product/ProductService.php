<?php

/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Pim\Product;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\RequestOptions;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UploadedFileInterface;
use Wizaplace\SDK\AbstractService;
use Wizaplace\SDK\Authentication\AuthenticationRequired;
use Wizaplace\SDK\Exception\NotFound;

use function theodorejb\polycast\to_int;

/**
 * Class ProductService
 * @package Wizaplace\SDK\Pim\Product
 */
final class ProductService extends AbstractService
{
    /**
     * @param int $productId
     *
     * @return Product
     * @throws AuthenticationRequired
     * @throws NotFound
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Wizaplace\SDK\Exception\JsonDecodingError
     * @throws \Exception
     */
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

    /**
     * @param ProductListFilter|null $filter
     * @param int                    $page
     * @param int                    $itemsCountPerPage
     *
     * @return ProductList
     * @throws AuthenticationRequired
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Wizaplace\SDK\Exception\JsonDecodingError
     */
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

        $data = $this->client->get(
            'products',
            [
                RequestOptions::QUERY => $query,
            ]
        );

        return new ProductList($data);
    }

    /**
     * @param CreateProductCommand $command
     *
     * @return int
     * @throws AuthenticationRequired
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Wizaplace\SDK\Exception\JsonDecodingError
     * @throws \Wizaplace\SDK\Exception\SomeParametersAreInvalid
     */
    public function createProduct(CreateProductCommand $command): int
    {
        $this->client->mustBeAuthenticated();
        $command->validate();

        $data = $this->client->post(
            'products',
            [
                RequestOptions::JSON => $command->toArray(),
            ]
        );

        return to_int($data['product_id']);
    }

    /**
     * @param UpdateProductCommand $command
     *
     * @return int
     * @throws AuthenticationRequired
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Wizaplace\SDK\Exception\JsonDecodingError
     * @throws \Wizaplace\SDK\Exception\SomeParametersAreInvalid
     */
    public function updateProduct(UpdateProductCommand $command): int
    {
        $this->client->mustBeAuthenticated();
        $command->validate();

        $id = $command->getId();

        $data = $this->client->put(
            "products/${id}",
            [
                RequestOptions::JSON => $command->toArray(),
            ]
        );

        return to_int($data['product_id']);
    }

    /**
     * @param int $productId
     *
     * @throws AuthenticationRequired
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Wizaplace\SDK\Exception\JsonDecodingError
     */
    public function deleteProduct(int $productId): void
    {
        $this->client->mustBeAuthenticated();
        $this->client->delete("products/${productId}");
    }

    /**
     * @param int $productId
     * @param int $shippingId
     *
     * @return Shipping
     * @throws AuthenticationRequired
     * @throws NotFound
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Wizaplace\SDK\Exception\JsonDecodingError
     */
    public function getShipping(int $productId, int $shippingId): Shipping
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
     * @throws AuthenticationRequired
     * @throws NotFound
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Wizaplace\SDK\Exception\JsonDecodingError
     */
    public function getShippings(int $productId): array
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

    /**
     * @param int                   $shippingId
     * @param UpdateShippingCommand $command
     *
     * @throws AuthenticationRequired
     * @throws NotFound
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Wizaplace\SDK\Exception\JsonDecodingError
     * @throws \Wizaplace\SDK\Exception\SomeParametersAreInvalid
     */
    public function putShipping(int $shippingId, UpdateShippingCommand $command): void
    {
        $this->client->mustBeAuthenticated();

        $command->validate();

        $productId = $command->getProductId();

        try {
            $this->client->put(
                "products/${productId}/shippings/${shippingId}",
                [
                    RequestOptions::JSON => $command->toArray(),
                ]
            );
        } catch (ClientException $e) {
            if ($e->getCode() === 404) {
                throw new NotFound("product #${productId} or shipping #${shippingId} not found", $e);
            }

            throw $e;
        }
    }

    /**
     * Allow to get a list of countries codes of enabled divisions for the product
     *
     * @param int $productId
     *
     * @return array
     * @throws AuthenticationRequired
     * @throws NotFound
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Wizaplace\SDK\Exception\JsonDecodingError
     */
    public function getDivisionsCountriesCodes(int $productId): array
    {
        $this->client->mustBeAuthenticated();

        try {
            return $this->client->get("products/{$productId}/divisions");
        } catch (ClientException $e) {
            if ($e->getResponse()->getStatusCode() === 404) {
                throw new NotFound($e);
            }
            throw $e;
        }
    }

    /**
     * Allow to get a list of divsions enabled for the product
     *
     * @param int    $productId
     * @param string $countryCode
     *
     * @return array
     * @throws AuthenticationRequired
     * @throws NotFound
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Wizaplace\SDK\Exception\JsonDecodingError
     */
    public function getDivisions(int $productId, string $countryCode): array
    {
        $this->client->mustBeAuthenticated();

        try {
            return $this->client->get("products/{$productId}/divisions/{$countryCode}");
        } catch (ClientException $e) {
            if ($e->getResponse()->getStatusCode() === 404) {
                throw new NotFound($e);
            }
            throw $e;
        }
    }

    /**
     * Allow to disable divisions for the product
     *
     * @param int    $productId
     * @param string $countryCode
     * @param array  $codes
     *
     * @return array
     * @throws AuthenticationRequired
     * @throws NotFound
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Wizaplace\SDK\Exception\JsonDecodingError
     */
    public function putDivisions(int $productId, string $countryCode, array $codes): array
    {
        $this->client->mustBeAuthenticated();

        try {
            return $this->client->put(
                "products/{$productId}/divisions/{$countryCode}",
                [
                    RequestOptions::FORM_PARAMS => [
                        'code' => $codes,
                    ],
                ]
            );
        } catch (ClientException $e) {
            if ($e->getResponse()->getStatusCode() === 404) {
                throw new NotFound($e);
            }
            throw $e;
        }
    }

    /**
     * @param int    $productId
     * @param string $url
     *
     * @return array
     * @throws AuthenticationRequired
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Wizaplace\SDK\Exception\JsonDecodingError
     */
    public function addVideo(int $productId, string $url): array
    {
        $this->client->mustBeAuthenticated();

        try {
            return $this->client->post(
                "products/${productId}/video",
                [
                    RequestOptions::FORM_PARAMS => [
                        'url' => $url,
                    ],
                ]
            );
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

    /**
     * @param int $productId
     *
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
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

    /**
     * @param string $ean
     * @param int    $stock
     *
     * @return string Number of updated product
     * @throws NotFound
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Wizaplace\SDK\Exception\JsonDecodingError
     */
    public function updateStock(string $ean, int $stock): ?string
    {
        $this->client->mustBeAuthenticated();

        try {
            return $this->client->put(
                "pim/products/{$ean}/stocks",
                [
                    RequestOptions::JSON => [
                        'stock' => $stock,
                    ],
                ]
            );
        } catch (ClientException $e) {
            if ($e->getCode() === 404) {
                throw new NotFound("Product EAN #{$ean} not found", $e);
            }

            throw $e;
        }
    }

    /**
     * @param int $productId
     * @param UploadedFileInterface[] $files
     * @param string[] $urls
     *
     * @return string[] Array of attachments' id
     * @throws NotFound
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Wizaplace\SDK\Exception\JsonDecodingError
     */
    public function addAttachments(int $productId, array $files, array $urls): array
    {
        $this->client->mustBeAuthenticated();

        $data = [];
        if (\count($urls) > 0) {
            foreach ($urls as $url) {
                $data[] = [
                    'name'     => 'attachments[]',
                    'contents' => $url,
                ];
            }
        }

        if (\count($files) > 0) {
            /** @var UploadedFileInterface $file */
            foreach ($files as $file) {
                if (false === $file instanceof UploadedFileInterface) {
                    throw new \InvalidArgumentException('The $files parameter must be an array of ' . UploadedFileInterface::class . '.');
                }

                $data[] = [
                    'name'     => 'attachments[]',
                    'contents' => $file->getStream(),
                    'filename' => $file->getClientFilename(),
                ];
            }
        }

        try {
            return $this->client->post(
                "pim/products/$productId/attachments",
                [
                    RequestOptions::MULTIPART => $data,
                ]
            );
        } catch (ClientException $e) {
            if ($e->getCode() === 404) {
                throw new NotFound("Product #$productId not found.", $e);
            }

            throw $e;
        }
    }

    /**
     * @param int $productId
     * @param string $attachmentId
     *
     * @return \Psr\Http\Message\ResponseInterface
     * @throws NotFound
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Wizaplace\SDK\Exception\JsonDecodingError
     */
    public function removeAttachment(int $productId, string $attachmentId): ResponseInterface
    {
        $this->client->mustBeAuthenticated();

        try {
            return $this->client->rawRequest("DELETE", "pim/products/$productId/attachments/$attachmentId");
        } catch (ClientException $e) {
            if ($e->getCode() === 404) {
                throw new NotFound("Product #$productId not found.", $e);
            }

            throw $e;
        }
    }
}
