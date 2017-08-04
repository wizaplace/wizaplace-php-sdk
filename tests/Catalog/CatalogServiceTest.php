<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\Tests\Catalog;

use Wizaplace\Catalog\AttributeType;
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
        $companies = $product->getCompanies();
        $this->assertCount(1, $companies);
        $this->assertEquals('Test company', $companies[0]->getName());
        $this->assertEquals('test-company', $companies[0]->getSlug());
        $this->assertEquals(5, $companies[0]->getId());
        $this->assertNull($companies[0]->getImage());


        $pagination = $result->getPagination();
        $this->assertEquals(1, $pagination->getNbPages());
        $this->assertEquals(1, $pagination->getNbResults());
        $this->assertEquals(1, $pagination->getPage());
        $this->assertEquals(12, $pagination->getResultsPerPage());

        $facets = $result->getFacets();
        $this->assertCount(8, $facets);
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

        $company = $catalogService->getCompanyById(5);

        $this->assertEquals(5, $company->getId());
        $this->assertEquals('Test company', $company->getName());
        $this->assertEquals('test-company', $company->getSlug());
        $this->assertEquals('Test company', $company->getDescription());
        $this->assertEquals('40 rue Laure Diebold', $company->getAddress());
        $this->assertEquals('01 02 03 04 05', $company->getPhoneNumber());
        $this->assertEquals(true, $company->isProfessional());
        $this->assertEquals(null, $company->getLocation());
        $this->assertEquals(2, $company->getAverageRating());
    }

    public function testGetC2cCompanyById()
    {
        $catalogService = $this->buildCatalogService();

        $company = $catalogService->getCompanyById(4);

        $this->assertEquals(4, $company->getId());
        $this->assertEquals('C2C company', $company->getName());
        $this->assertEquals('c2c-company', $company->getSlug());
        $this->assertEquals('C2C company', $company->getDescription());
        $this->assertEquals('', $company->getAddress());
        $this->assertEquals('', $company->getPhoneNumber());
        $this->assertEquals(false, $company->isProfessional());
        $this->assertEquals(null, $company->getLocation());
        $this->assertEquals(null, $company->getAverageRating());
    }

    public function testGetCategory()
    {
        $category = $this->buildCatalogService()->getCategory(2);
        $this->assertEquals(2, $category->getId());
        $this->assertEquals('Catégorie principale', $category->getName());
        $this->assertEquals('categorie-principale', $category->getSlug());
        $this->assertEquals('', $category->getDescription());
        $this->assertEquals(10, $category->getPosition());
        $this->assertEquals(0, $category->getProductCount());

        // @TODO: more assertions
    }

    public function testGetCategoryTree()
    {
        $categoryTree = $this->buildCatalogService()->getCategoryTree();
        $this->assertCount(3, $categoryTree);

        $firstCategory = $categoryTree[0]->getCategory();

        $this->assertEquals(2, $firstCategory->getId());
        $this->assertEquals('categorie-principale', $firstCategory->getSlug());
        // @TODO: more assertions
    }

    public function testGetAttributes()
    {
        $attributes = $this->buildCatalogService()->getAttributes();

        $this->assertCount(9, $attributes);
        foreach ($attributes as $attribute) {
            $this->assertGreaterThan(0, $attribute->getId());
            $this->assertGreaterThanOrEqual(0, $attribute->getPosition());
            $this->assertNotEmpty($attribute->getName());
            $this->assertNotEmpty($attribute->getType()->getValue());
        }
    }

    public function testGetAttribute()
    {
        $attribute = $this->buildCatalogService()->getAttribute(1);

        $this->assertEquals(1, $attribute->getId());
        $this->assertEquals(0, $attribute->getPosition());
        $this->assertEquals('Couleur', $attribute->getName());
        $this->assertEquals(AttributeType::CHECKBOX_MULTIPLE(), $attribute->getType());
        $this->assertNull($attribute->getParentId());
    }

    public function testGetNonExistingAttribute()
    {
        $this->expectException(NotFound::class);
        $this->buildCatalogService()->getAttribute(404);
    }

    public function testGetAttributeVariant()
    {
        $variant = $this->buildCatalogService()->getAttributeVariant(3);

        $this->assertEquals(3, $variant->getId());
        $this->assertEquals(1, $variant->getAttributeId());
        $this->assertEquals('Rouge', $variant->getName());
        $this->assertEquals('rouge', $variant->getSlug());
        $this->assertNull($variant->getImage());
    }

    public function testGetNonExistingAttributeVariant()
    {
        $this->expectException(NotFound::class);
        $this->buildCatalogService()->getAttributeVariant(404);
    }

    private function buildCatalogService(): CatalogService
    {
        return new CatalogService($this->buildApiClient());
    }
}
