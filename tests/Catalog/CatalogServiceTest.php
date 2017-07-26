<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\Tests\Catalog;

use Wizaplace\Catalog\CatalogService;
use Wizaplace\Exception\NotFound;
use Wizaplace\Tests\ApiTestCase;

/**
 * @see CatalogService
 */
class CatalogServiceTest extends ApiTestCase
{
    public function testGetProductById()
    {
        $catalogService = $this->buildCatalogService();

        $product = $catalogService->getProductById(1);

        $this->assertEquals(1, $product->getId());
        $this->assertEquals('test-product-slug', $product->getSlug());
        $this->assertEquals('optio corporis similique voluptatum', $product->getName());
        $this->assertEquals('', $product->getDescription());
        $this->assertCount(1, $product->getDeclinations());
        $this->assertCount(0, $product->getAttributes());
        $this->assertCount(1, $product->getCategoryPath());
        $this->assertEquals(['informatique'], $product->getCategorySlugs());
        $this->assertEquals('6086375420678', $product->getCode());
        $this->assertGreaterThan(1500000000, $product->getCreationDate()->getTimestamp());
        $this->assertEquals('http://wizaplace.loc/informatique/test-product-slug.html', $product->getUrl());
        $this->assertEquals(20, $product->getMinPrice());
        $this->assertCount(1, $product->getCompanies());
        $this->assertCount(1, $product->getShippings());
        $this->assertEquals('', $product->getShortDescription());
        $this->assertEquals('', $product->getSupplierReference());
        $this->assertTrue($product->isTransactional());
        $this->assertEquals(0, $product->getGreenTax());
        $this->assertEquals(1.23, $product->getWeight());
        $this->assertEquals(3, $product->getAverageRating());
    }

    public function testGetProductWithComplexAttributes()
    {
        $catalogService = $this->buildCatalogService();

        $product = $catalogService->getProductById(7);

        $this->assertEquals(7, $product->getId());
        $this->assertEquals('test-product-complex-attributes', $product->getSlug());
        $this->assertEquals(1.23, $product->getWeight());
        $this->assertEquals(0, $product->getGreenTax());
        $this->assertEquals(null, $product->getAverageRating());

        $attributes = $product->getAttributes();

        // Check attributes group
        $this->assertCount(9, $attributes);
        $attributesGroup = $attributes[2];
        $this->assertEquals('Groupe attributs', $attributesGroup->getName());
        $this->assertNull($attributesGroup->getValue());
        $this->assertCount(3, $attributesGroup->getChildren());

        // Bulk checks on all attributes
        foreach ($attributes as $attribute) {
            $value = $attribute->getValue();
            if (!is_null($value) && !is_string($value) && !is_array($value)) {
                throw new \TypeError('\Wizaplace\Catalog\ProductAttribute::getValue must return null, a string, or an array. Got '.var_export($value, true));
            }
            $this->assertNotEmpty($attribute->getName());
            $attribute->getChildren();
            $attribute->getImageUrls();
        }

        // @TODO: more assertions
    }

    public function testGetNonExistingProductById()
    {
        $catalogService = $this->buildCatalogService();

        $this->expectException(NotFound::class);
        $catalogService->getProductById(404);
    }

    public function testSearchOneProductByName()
    {
        $catalogService = $this->buildCatalogService();

        $result = $catalogService->search('optio corporis similique voluptatum');

        $products = $result->getProducts();
        $this->assertCount(1, $products);

        $product = $products[0];
        $this->assertEquals(1, $product->getId());
        $this->assertEquals('test-product-slug', $product->getSlug());
        $this->assertTrue($product->isAvailable());
        $this->assertGreaterThan(1400000000, $product->getCreatedAt()->getTimestamp());
        $this->assertEquals(3, $product->getAverageRating());
        $this->assertEquals('optio corporis similique voluptatum', $product->getName());
        $this->assertEquals(['N'], $product->getCondition());
        $this->assertEquals(1, $product->getDeclinationCount());
        $this->assertEquals('', $product->getSubtitle());
        $this->assertGreaterThanOrEqual($product->getCreatedAt()->getTimestamp(), $product->getUpdatedAt()->getTimestamp());
        $this->assertEquals('', $product->getAffiliateLink());
        $this->assertEquals('/informatique/test-product-slug.html', $product->getUrl());
        $this->assertEquals(['informatique'], $product->getCategorySlugs());
        $this->assertCount(1, $product->getCategoryPath());
        $this->assertNull($product->getMainImage());
        $this->assertEquals(20, $product->getMinimumPrice());
        $this->assertNull($product->getCrossedOutPrice());
        $this->assertCount(0, $product->getAttributes());


        $pagination = $result->getPagination();
        $this->assertEquals(1, $pagination->getNbPages());
        $this->assertEquals(1, $pagination->getNbResults());
        $this->assertEquals(1, $pagination->getPage());
        $this->assertEquals(12, $pagination->getResultsPerPage());

        $facets = $result->getFacets();
        $this->assertCount(9, $facets);
        $this->assertEquals('categories', $facets[0]->getName());
        $this->assertEquals('Catégorie', $facets[0]->getLabel());
        $this->assertEquals([
            3 => [
            'label' => 'Informatique',
            'count' => '1',
            'position' => 0,
            ],
        ], $facets[0]->getValues());
        $this->assertFalse($facets[0]->isIsNumeric());
    }

    public function testGetCompanyById()
    {
        $catalogService = $this->buildCatalogService();

        $company = $catalogService->getCompanyById(4);

        $this->assertEquals(4, $company->getId());
        $this->assertEquals('Test company', $company->getName());
        $this->assertEquals('test-company', $company->getSlug());
        $this->assertEquals('Test company', $company->getDescription());
        $this->assertEquals(true, $company->isProfessional());
        $this->assertEquals(null, $company->getLocation());
        $this->assertEquals(2, $company->getAverageRating());
    }

    public function testGetCategory()
    {
        $category = $this->buildCatalogService()->getCategory(2);
        $this->assertEquals(2, $category->getId());
        $this->assertEquals('categorie-principale', $category->getSlug());

        // @TODO: more assertions
    }

    public function testGetCategoryTree()
    {
        $categoryTree = $this->buildCatalogService()->getCategoryTree();
        $this->assertCount(3, $categoryTree);

        $firstCateogry = $categoryTree[0]->getCategory();

        $this->assertEquals(2, $firstCateogry->getId());
        $this->assertEquals('categorie-principale', $firstCateogry->getSlug());
        // @TODO: more assertions
    }

    private function buildCatalogService(): CatalogService
    {
        return new CatalogService($this->buildApiClient());
    }
}
