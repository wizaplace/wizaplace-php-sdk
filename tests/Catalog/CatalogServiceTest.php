<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\Tests\Catalog;

use VCR\VCR;
use Wizaplace\Catalog\CatalogService;
use Wizaplace\Exception\NotFound;
use Wizaplace\Tests\ApiTest;

class CatalogServiceTest extends ApiTest
{
    public function testGetProductById()
    {
        try {
            VCR::turnOn();
            VCR::insertCassette($this->getName());

            $catalogService = new CatalogService($this->getGuzzleClient());

            $product = $catalogService->getProductById(1);

            $this->assertEquals(1, $product->getId());
            $this->assertEquals('test-product-slug', $product->getSlug());
            // @TODO: more assertions
        } finally {
            VCR::turnOff();
        }
    }

    public function testGetNonExistingProductById()
    {
        try {
            VCR::turnOn();
            VCR::insertCassette($this->getName());

            $catalogService = new CatalogService($this->getGuzzleClient());

            $this->expectException(NotFound::class);
            $catalogService->getProductById(404);
        } finally {
            VCR::turnOff();
        }
    }

    public function testSearchOneProductByName()
    {
        try {
            VCR::turnOn();
            VCR::insertCassette($this->getName());

            $catalogService = new CatalogService($this->getGuzzleClient());

            $result = $catalogService->search('optio corporis similique voluptatum');

            $products = $result->getProducts();
            $this->assertCount(1, $products);

            $product = $products[0];
            $this->assertEquals(1, $product->getId());
            $this->assertEquals('test-product-slug', $product->getSlug());
            // @TODO: more assertions
        } finally {
            VCR::turnOff();
            static::$historyContainer = []; // @FIXME: small hack due to fr3d/swagger-assertions not properly checking URL query non-string types.
        }
    }
}
