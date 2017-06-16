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
        $catalogService = new CatalogService($this->getGuzzleClient());

        $product = $catalogService->getProductById(1);

        $this->assertEquals(1, $product->getId());
        $this->assertEquals('test-product-slug', $product->getSlug());
        // @TODO: more assertions
    }

    public function testGetNonExistingProductById()
    {
        $catalogService = new CatalogService($this->getGuzzleClient());

        $this->expectException(NotFound::class);
        $catalogService->getProductById(404);
    }

    public function testSearchOneProductByName()
    {
        $catalogService = new CatalogService($this->getGuzzleClient());

        $result = $catalogService->search('optio corporis similique voluptatum');

        $products = $result->getProducts();
        $this->assertCount(1, $products);

        $product = $products[0];
        $this->assertEquals(1, $product->getId());
        $this->assertEquals('test-product-slug', $product->getSlug());
        // @TODO: more assertions
    }
}
