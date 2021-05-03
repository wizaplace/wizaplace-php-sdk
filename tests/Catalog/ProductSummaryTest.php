<?php

/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Tests\Catalog;

use PHPUnit\Framework\TestCase;
use Wizaplace\SDK\Catalog\DeclinationImages;
use Wizaplace\SDK\Catalog\ProductSummary;
use Wizaplace\SDK\Image\Image;

final class ProductSummaryTest extends TestCase
{
    public function testProductSummary()
    {
        $product = new ProductSummary(
            [
                'productId' => 23,
                'name' => 'name',
                'code' => '3445678764534',
                'subtitle' => 'subtitle',
                'shortDescription' => 'shortDescription',
                'minimumPrice' => 1.0,
                'crossedOutPrice' => 19.9,
                'isAvailable' => true,
                'url' => '/url/',
                'createdAt' => (new \DateTime())->format("Ymd H:i:s"),
                'updatedAt' => (new \DateTime())->format("Ymd H:i:s"),
                'declinationCount' => 2,
                'affiliateLink' => 'http://affiliatelink.com',
                'mainImage' => ['id' => 8],
                'images' => [['id' => 8], ['id' => 9]],
                'declinationsImages' => [
                    [
                        'declinationId' => '1_1_1',
                        'images' => [['id' => 11]],
                    ],
                    [
                        'declinationId' => '1_2_1',
                        'images' => [['id' => 12]],
                    ],
                ],
                'conditions' => [],
                'attributes' => [],
                'categoryPath' => [],
                'slug' => 'product slug',
                'companies' => [],
            ]
        );

        $this->assertSame('23', $product->getId());
        $this->assertSame('name', $product->getName());
        $this->assertSame('3445678764534', $product->getCode());
        $this->assertSame('subtitle', $product->getSubtitle());
        $this->assertSame('shortDescription', $product->getShortDescription());
        $this->assertSame(1.0, $product->getMinimumPrice());
        $this->assertSame(19.9, $product->getCrossedOutPrice());
        $this->assertSame(2, $product->getDeclinationCount());
        $this->assertEquals(new Image(['id' => 8]), $product->getMainImage());
        $this->assertEquals(
            [
                new Image(['id' => 8]),
                new Image(['id' => 9]),
            ],
            $product->getImages()
        );
        $this->assertEquals(
            [
                new DeclinationImages([
                    'declinationId' => '1_1_1',
                    'images' => [new Image(['id' => 11])],
                ]),
                new DeclinationImages([
                    'declinationId' => '1_2_1',
                    'images' => [new Image(['id' => 12])],
                ]),
            ],
            $product->getDeclinationsImages()
        );
        $this->assertSame([], $product->getConditions());
        $this->assertSame([], $product->getAttributes());
        $this->assertSame([], $product->getCategoryPath());
        $this->assertSame('product slug', $product->getSlug());
        $this->assertSame([], $product->getCompanies());
    }
}
