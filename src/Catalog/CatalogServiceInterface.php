<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types=1);

namespace Wizaplace\SDK\Catalog;

use Wizaplace\SDK\Exception\NotFound;
use Wizaplace\SDK\Exception\SomeParametersAreInvalid;

interface CatalogServiceInterface
{
    public function getAllProducts(): \Generator;

    public function getProductById(string $id): Product;

    /**
     * @return Product[]
     */
    public function getProductsByCode(string $code): array;

    /**
     * @return Product[]
     */
    public function getProductsBySupplierReference(string $supplierReference): array;

    /**
     * @return CategoryTree[]
     */
    public function getCategoryTree(): array;

    public function getCategory(int $id): Category;

    /**
     * @return Category[]
     */
    public function getCategories(): array;

    public function search(
        string $query = '',
        array $filters = [],
        array $sorting = [],
        int $resultsPerPage = 12,
        int $page = 1,
        ?GeoFilter $geoFilter = null
    ): SearchResult;

    public function getCompanyById(int $id): CompanyDetail;

    /**
     * @return CompanyDetail[]
     */
    public function getCompanies(): array;

    /**
     * @return Attribute[]
     */
    public function getAttributes(): array;

    public function getAttribute(int $attributeId): Attribute;

    public function getAttributeVariant(int $variantId): AttributeVariant;

    /**
     * @return AttributeVariant[]
     */
    public function getAttributeVariants(int $attributeId): array;

    /**
     * Report a suspicious product to the marketplace administrator.
     *
     * @throws NotFound
     * @throws SomeParametersAreInvalid
     */
    public function reportProduct(ProductReport $report): void;

    /**
     * Convenience method to extract a brand from a product
     *
     * @param ProductSummary|Product $product
     * @return null|ProductAttributeValue
     * @throws \TypeError
     */
    public function getBrand($product): ?ProductAttributeValue;

    public function getBrandFromProductSummary(ProductSummary $product): ?ProductAttributeValue;

    public function getBrandFromProduct(Product $product): ?ProductAttributeValue;
}
