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
}
