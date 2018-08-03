<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types=1);

namespace Wizaplace\SDK\Catalog;

use Wizaplace\SDK\Exception\NotFound;
use Wizaplace\SDK\Exception\SomeParametersAreInvalid;

abstract class AbstractCatalogServiceDecorator implements CatalogServiceInterface
{
    /** @var CatalogServiceInterface */
    private $decorated;

    public function __construct(CatalogServiceInterface $decorated)
    {
        $this->decorated = $decorated;
    }

    public function getAllProducts(): \Generator
    {
        return $this->decorated->getAllProducts();
    }

    public function getProductById(string $id): Product
    {
        return $this->decorated->getProductById($id);
    }

    /**
     * @return Product[]
     */
    public function getProductsByCode(string $code): array
    {
        return $this->decorated->getProductsByCode($code);
    }

    /**
     * @return Product[]
     */
    public function getProductsBySupplierReference(string $supplierReference): array
    {
        return $this->decorated->getProductsBySupplierReference($supplierReference);
    }

    /**
     * @return CategoryTree[]
     */
    public function getCategoryTree(): array
    {
        return $this->decorated->getCategoryTree();
    }

    public function getCategory(int $id): Category
    {
        return $this->decorated->getCategory($id);
    }

    /**
     * @return Category[]
     */
    public function getCategories(): array
    {
        return $this->decorated->getCategories();
    }

    public function search(
        string $query = '',
        array $filters = [],
        array $sorting = [],
        int $resultsPerPage = 12,
        int $page = 1,
        ?GeoFilter $geoFilter = null
    ): SearchResult {
        return $this->decorated->search($query, $filters, $sorting, $resultsPerPage, $page, $geoFilter);
    }

    public function getCompanyById(int $id): CompanyDetail
    {
        return $this->decorated->getCompanyById($id);
    }

    /**
     * @return CompanyDetail[]
     */
    public function getCompanies(): array
    {
        return $this->decorated->getCompanies();
    }

    /**
     * @return Attribute[]
     */
    public function getAttributes(): array
    {
        return $this->decorated->getAttributes();
    }

    public function getAttribute(int $attributeId): Attribute
    {
        return $this->decorated->getAttribute($attributeId);
    }

    public function getAttributeVariant(int $variantId): AttributeVariant
    {
        return $this->decorated->getAttributeVariant($variantId);
    }

    /**
     * @return AttributeVariant[]
     */
    public function getAttributeVariants(int $attributeId): array
    {
        return $this->decorated->getAttributeVariants($attributeId);
    }

    /**
     * Report a suspicious product to the marketplace administrator.
     *
     * @throws NotFound
     * @throws SomeParametersAreInvalid
     */
    public function reportProduct(ProductReport $report): void
    {
        $this->decorated->reportProduct($report);
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
        return $this->decorated->getBrand($product);
    }

    public function getBrandFromProductSummary(ProductSummary $product): ?ProductAttributeValue
    {
        return $this->decorated->getBrandFromProductSummary($product);
    }

    public function getBrandFromProduct(Product $product): ?ProductAttributeValue
    {
        return $this->decorated->getBrandFromProduct($product);
    }
}
