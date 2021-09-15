<?php

/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Pim\Product\RelatedProduct;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\RequestOptions;
use Wizaplace\SDK\AbstractService;
use Wizaplace\SDK\Exception\AccessDenied;
use Wizaplace\SDK\Exception\Conflict;
use Wizaplace\SDK\Exception\NotFound;
use Wizaplace\SDK\Exception\SomeParametersAreInvalid;

class RelatedProductService extends AbstractService
{
    public function addRelatedProduct(int $productId, RelatedProduct $relatedProduct): RelatedProduct
    {
        $this->client->mustBeAuthenticated();

        try {
            $response = $this->client->post(
                "products/{$productId}/related",
                [
                    RequestOptions::JSON => $relatedProduct->jsonSerialize(),
                ]
            );

            return new RelatedProduct($response);
        } catch (ClientException $e) {
            switch ($e->getResponse()->getStatusCode()) {
                case 400:
                    throw new SomeParametersAreInvalid($e->getMessage());
                case 403:
                    throw new AccessDenied($e->getMessage());
                case 404:
                    throw new NotFound($e->getMessage());
                case 409:
                    throw new Conflict($e->getMessage());
                default:
                    throw $e;
            }
        }
    }

    private function deleteRelatedProduct(string $route): void
    {
        $this->client->mustBeAuthenticated();

        try {
            $this->client->delete($route);
        } catch (ClientException $e) {
            switch ($e->getResponse()->getStatusCode()) {
                case 400:
                    throw new SomeParametersAreInvalid($e->getMessage());
                case 403:
                    throw new AccessDenied($e->getMessage());
                case 404:
                    throw new NotFound($e->getMessage());
                default:
                    throw $e;
            }
        }
    }

    /**
     * @throws SomeParametersAreInvalid
     * @throws NotFound
     * @throws AccessDenied
     */
    public function deleteAllRelatedProduct(int $productId): void
    {
        $this->deleteRelatedProduct("products/{$productId}/related");
    }

    /**
     * @throws SomeParametersAreInvalid
     * @throws NotFound
     * @throws AccessDenied
     */
    public function deleteRelatedProductWithType(
        int $productId,
        string $type
    ): void {
        $this->deleteRelatedProduct("products/{$productId}/related?type={$type}");
    }

    /**
     * @throws SomeParametersAreInvalid
     * @throws NotFound
     * @throws AccessDenied
     */
    public function deleteRelatedProductWithRelatedProductIdAndType(
        int $productId,
        int $relatedProductId,
        string $type
    ): void {
        $this->deleteRelatedProduct("products/{$productId}/related?productId={$relatedProductId}&type={$type}");
    }

    /**
     * @throws SomeParametersAreInvalid
     * @throws NotFound
     * @throws AccessDenied
     */
    public function deleteRelatedProductWithRelatedProductId(
        int $productId,
        int $relatedProductId
    ): void {
        $this->deleteRelatedProduct("products/{$productId}/related?productId={$relatedProductId}");
    }
}
