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

class CatalogServiceTest extends ApiTestCase
{
    public function testGetProductById()
    {
        $catalogService = $this->buildCatalogService();

        $product = $catalogService->getProductById(1);

        $this->assertEquals(1, $product->getId());
        $this->assertEquals('test-product-slug', $product->getSlug());
        // @TODO: more assertions
    }

    public function testGetProductWithComplexAttributes()
    {
        $catalogService = $this->buildCatalogService();

        $product = $catalogService->getProductById(7);

        $this->assertEquals(7, $product->getId());
        $this->assertEquals('test-product-complex-attributes', $product->getSlug());

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
        // @TODO: more assertions
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

    private function buildCatalogService(): CatalogService
    {
        return new CatalogService($this->buildApiClient());
    }
}
