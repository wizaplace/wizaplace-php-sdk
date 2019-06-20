<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types=1);

namespace Wizaplace\SDK\Catalog;

use Psr\Http\Message\ResponseInterface;
use Wizaplace\SDK\Exception\NotFound;
use Wizaplace\SDK\Exception\SomeParametersAreInvalid;

/**
 * Class AbstractCatalogServiceDecorator
 * @package Wizaplace\SDK\Catalog
 */
abstract class AbstractCatalogServiceDecorator implements CatalogServiceInterface
{
    /** @var CatalogServiceInterface */
    private $decorated;

    /**
     * AbstractCatalogServiceDecorator constructor.
     *
     * @param CatalogServiceInterface $decorated
     */
    public function __construct(CatalogServiceInterface $decorated)
    {
        $this->decorated = $decorated;
    }

    /**
     * @param string|NULL $language
     *
     * @return \Generator
     */
    public function getAllProducts(string $language = null): \Generator
    {
        return $this->decorated->getAllProducts($language);
    }

    /**
     * @param string $id
     *
     * @return Product
     */
    public function getProductById(string $id): Product
    {
        return $this->decorated->getProductById($id);
    }

    /**
     * @param string $id
     *
     * @return Declination
     */
    public function getDeclinationById(string $id): Declination
    {
        return $this->decorated->getDeclinationById($id);
    }

    /**
     * @param string $code
     *
     * @return Product[]
     */
    public function getProductsByCode(string $code): array
    {
        return $this->decorated->getProductsByCode($code);
    }

    /**
     * @param string $supplierReference
     *
     * @return Product[]
     */
    public function getProductsBySupplierReference(string $supplierReference): array
    {
        return $this->decorated->getProductsBySupplierReference($supplierReference);
    }

    public function getProductsByFilters(ProductFilter $productFilter): array
    {
        return $this->decorated->getProductsByFilters($productFilter);
    }

    /**
     * @return CategoryTree[]
     */
    public function getCategoryTree(): array
    {
        return $this->decorated->getCategoryTree();
    }

    /**
     * @param int $id
     *
     * @return Category
     */
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

    /**
     * @param string         $query
     * @param array          $filters
     * @param array          $sorting
     * @param int            $resultsPerPage
     * @param int            $page
     * @param GeoFilter|null $geoFilter
     *
     * @return SearchResult
     */
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

    /**
     * @param int $id
     *
     * @return CompanyDetail
     */
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

    /**
     * @param int $attributeId
     *
     * @return Attribute
     */
    public function getAttribute(int $attributeId): Attribute
    {
        return $this->decorated->getAttribute($attributeId);
    }

    /**
     * @param int $variantId
     *
     * @return AttributeVariant
     */
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

    /**
     * @param ProductSummary $product
     *
     * @return ProductAttributeValue|null
     */
    public function getBrandFromProductSummary(ProductSummary $product): ?ProductAttributeValue
    {
        return $this->decorated->getBrandFromProductSummary($product);
    }

    /**
     * @param Product $product
     *
     * @return ProductAttributeValue|null
     */
    public function getBrandFromProduct(Product $product): ?ProductAttributeValue
    {
        return $this->decorated->getBrandFromProduct($product);
    }

    /**
     * @param string $attachmentId
     *
     * @return ResponseInterface
     */
    public function getProductAttachment(string $attachmentId): ResponseInterface
    {
        return $this->decorated->getProductAttachment($attachmentId);
    }
}
