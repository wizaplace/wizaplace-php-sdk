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

        $company = $catalogService->getCompanyById(1);

        $this->assertEquals(1, $company->getId());
        $this->assertEquals('Marchand de test', $company->getName());
        $this->assertEquals('marchand-de-test', $company->getSlug());
        $this->assertEquals('Marchand de test', $company->getDescription());
        $this->assertEquals(false, $company->isProfessional());
        $this->assertEquals(0, $company->getLocation()->getLatitude());
        $this->assertEquals(0, $company->getLocation()->getLongitude());
    }

    private function buildCatalogService(): CatalogService
    {
        return new CatalogService($this->buildApiClient());
    }
}
