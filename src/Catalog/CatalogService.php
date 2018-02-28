<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\SDK\Catalog;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\RequestOptions;
use Wizaplace\SDK\AbstractService;
use Wizaplace\SDK\Exception\NotFound;
use Wizaplace\SDK\Exception\ProductNotFound;
use Wizaplace\SDK\Exception\SomeParametersAreInvalid;
use function theodorejb\polycast\to_string;

final class CatalogService extends AbstractService
{
    /**
     * @throws ProductNotFound
     */
    public function getProductById(string $id) : Product
    {
        $response = $this->client->get("catalog/products/{$id}");

        return new Product($response, $this->client->getBaseUri());
    }

    /**
     * @return CategoryTree[]
     */
    public function getCategoryTree():array
    {
        $categoryTree = $this->client->get('catalog/categories/tree');

        return CategoryTree::buildCollection($categoryTree);
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
     * @return CompanyDetail[]
     */
    public function getCompanies(): array
    {
        $response = $this->client->get("catalog/companies");

        $companies = array_map(function ($companyData) {
            return new CompanyListItem($companyData);
        }, $response);

        return $companies;
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
     * @throws ProductNotFound
     * @throws SomeParametersAreInvalid
     */
    public function reportProduct(ProductReport $report): void
    {
        $report->validate();

        try {
            $this->client->post("catalog/products/{$report->getProductId()}/report", [
                RequestOptions::JSON => [
                    'productId' => $report->getProductId(),
                    'name' => $report->getReporterName(),
                    'email' => $report->getReporterEmail(),
                    'message' => $report->getMessage(),
                ],
            ]);
        } catch (ClientException $e) {
            switch ($e->getCode()) {
                case 400:
                    throw new SomeParametersAreInvalid(to_string($e->getResponse()->getBody()), 400, $e);
            }
            throw $e;
        }
    }

    /**
     * Convenience method to extract a brand from a product
     *
     * @param ProductSummary|Product $product
     * @return null|ProductAttributeValue
     * @throws \TypeError
     */
    public function getBrand($product): ?ProductAttributeValue
    {
        if ($product instanceof ProductSummary) {
            return $this->getBrandFromProductSummary($product);
        }

        if ($product instanceof Product) {
            return $this->getBrandFromProduct($product);
        }

        throw new \TypeError('Unexpected type for $product in getBrand : '.(is_object($product) ? get_class($product) : gettype($product)));
    }

    public function getBrandFromProductSummary(ProductSummary $product): ?ProductAttributeValue
    {
        foreach ($product->getAttributes() as $attribute) {
            if ($attribute->getType()->equals(AttributeType::LIST_BRAND())) {
                $values = $attribute->getValues();
                $brand = reset($values);

                return $brand;
            }
        }

        return null;
    }

    public function getBrandFromProduct(Product $product): ?ProductAttributeValue
    {
        foreach ($product->getAttributes() as $attribute) {
            if ($attribute->getType()->equals(AttributeType::LIST_BRAND())) {
                $values = $attribute->getValueIds();
                $variant = $this->getAttributeVariant(reset($values));

                return new ProductAttributeValue([
                    'id' => $variant->getId(),
                    'slug' => $variant->getSlug(),
                    'name' => $variant->getName(),
                    'attributeId' => $variant->getAttributeId(),
                    'image' => $variant->getImage(),
                ]);
            }
        }

        return null;
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
