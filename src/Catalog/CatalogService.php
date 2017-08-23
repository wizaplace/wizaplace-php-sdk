<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\Catalog;

use GuzzleHttp\Exception\ClientException;
use Wizaplace\AbstractService;
use Wizaplace\Exception\NotFound;
use Wizaplace\Image\Image;

final class CatalogService extends AbstractService
{
    public function getProductById(int $id) : Product
    {
        try {
            $response = $this->client->get("catalog/products/{$id}");
        } catch (ClientException $exception) {
            if ($exception->getResponse()->getStatusCode() === 404) {
                throw new NotFound("Product #{$id} not found.", $exception);
            } else {
                throw $exception;
            }
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
            } else {
                throw $exception;
            }
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

        return new AttributeVariant(
            $variantData['id'],
            $variantData['attributeId'],
            $variantData['name'],
            $variantData['slug'],
            isset($variantData['image']) ? new Image($variantData['image']) : null
        );
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
