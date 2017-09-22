<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\SDK\Catalog;

use GuzzleHttp\Exception\ClientException;
use Wizaplace\SDK\AbstractService;
use Wizaplace\SDK\Exception\NotFound;
use Wizaplace\SDK\Exception\SomeParametersAreInvalid;

final class CatalogService extends AbstractService
{
    public function getProductById(int $id) : Product
    {
        try {
            $response = $this->client->get("catalog/products/{$id}");
        } catch (ClientException $exception) {
            if ($exception->getResponse()->getStatusCode() === 404) {
                throw new NotFound("Product #{$id} not found.", $exception);
            }

            throw $exception;
        }

        return new Product($response);
    }

    /**
     * @return CategoryTree[]
     */
    public function getCategoryTree():array
    {
        $categoryTree = $this->client->get('catalog/categories/tree');

        return array_map(static function (array $tree) : CategoryTree {
            return new CategoryTree($tree);
        }, $categoryTree);
    }

    public function getCategory(int $id): Category
    {
        $category = $this->client->get("catalog/categories/{$id}");

        return new Category($category);
    }

    public function search(string $query = '', array $filters = [], array $sorting = [], int $resultsPerPage = 12, int $page = 1): SearchResult
    {
        $results = $this->client->get(
            'catalog/search/products',
            [
                'query' => [
                    'filters' => $filters,
                    'sorting' => $sorting,
                    'resultsPerPage' => $resultsPerPage,
                    'page' => $page,
                    'query' => $query,
                ],
            ]
        );

        return new SearchResult($results);
    }

    public function getCompanyById(int $id): CompanyDetail
    {
        try {
            $response = $this->client->get("catalog/companies/{$id}");
        } catch (ClientException $exception) {
            if ($exception->getResponse()->getStatusCode() === 404) {
                throw new NotFound("Company #{$id} not found.", $exception);
            }

            throw $exception;
        }

        return new CompanyDetail($response);
    }

    /**
     * @return Attribute[]
     */
    public function getAttributes(): array
    {
        $attributesData = $this->client->get("catalog/attributes");

        return array_map([$this, 'unserializeAttribute'], $attributesData);
    }

    public function getAttribute(int $attributeId): Attribute
    {
        try {
            $attributeData = $this->client->get("catalog/attributes/$attributeId");
        } catch (ClientException $e) {
            if ($e->getResponse()->getStatusCode() === 404) {
                throw new NotFound("Attribute #$attributeId not found", $e);
            }
            throw $e;
        }

        return $this->unserializeAttribute($attributeData);
    }

    public function getAttributeVariant(int $variantId): AttributeVariant
    {
        try {
            $variantData = $this->client->get("catalog/attributes/variants/$variantId");
        } catch (ClientException $e) {
            if ($e->getResponse()->getStatusCode() === 404) {
                throw new NotFound("Attribute Variant #$variantId not found", $e);
            }
            throw $e;
        }

        return new AttributeVariant($variantData);
    }

    /**
     * @return AttributeVariant[]
     */
    public function getAttributeVariants(int $attributeId): array
    {
        try {
            $variantsData = $this->client->get("catalog/attributes/$attributeId/variants");
        } catch (ClientException $e) {
            if ($e->getResponse()->getStatusCode() === 404) {
                throw new NotFound("Attribute #$attributeId not found", $e);
            }
            throw $e;
        }

        return array_map(function (array $variantData): AttributeVariant {
            return new AttributeVariant($variantData);
        }, $variantsData);
    }

    /**
     * Report a suspicious product to the marketplace administrator.
     *
     * @throws NotFound
     * @throws SomeParametersAreInvalid
     */
    public function reportProduct(ProductReport $report): void
    {
        $report->validate();

        try {
            $this->client->post("catalog/products/{$report->getProductId()}/report", [
                'json' => [
                    'productId' => $report->getProductId(),
                    'name' => $report->getReporterName(),
                    'email' => $report->getReporterEmail(),
                    'message' => $report->getMessage(),
                ],
            ]);
        } catch (ClientException $e) {
            switch ($e->getCode()) {
                case 404:
                    throw new NotFound("Product #{$report->getProductId()} not found", $e);
                case 400:
                    throw new SomeParametersAreInvalid((string) $e->getResponse()->getBody(), 400, $e);
            }
            throw $e;
        }
    }

    private function unserializeAttribute(array $attributeData): Attribute
    {
        return new Attribute(
            $attributeData['id'],
            $attributeData['name'],
            new AttributeType($attributeData['type']),
            $attributeData['position'],
            $attributeData['parentId'] ?? null
        );
    }
}
