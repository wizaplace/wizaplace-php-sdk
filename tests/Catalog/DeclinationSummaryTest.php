<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\SDK\Tests\Catalog;

use PHPUnit\Framework\TestCase;
use Wizaplace\SDK\Catalog\CompanySummary;
use Wizaplace\SDK\Catalog\DeclinationOption;
use Wizaplace\SDK\Catalog\DeclinationSummary;
use Wizaplace\SDK\Catalog\ProductCategory;
use Wizaplace\SDK\Image\Image;

final class DeclinationSummaryTest extends TestCase
{
    public function testInterface()
    {
        $favorite = new DeclinationSummary([
            'id' => '42_1_2',
            'productId' => 42,
            'name' => 'Very comfortable chair',
            'code' => '4006381333933',
            'prices' => [
                'priceWithoutVat' => 67.49,
                'priceWithTaxes' => 79.99,
                'vat' => 12.5,
            ],
            'crossedOutPrice' => 99.99,
            'amount' => 24,
            'affiliateLink' => 'http://example.com',
            'options' => [
                [
                    'id' => 1,
                    'name' => 'Color',
                    'variantId' => 2,
                    'variantName' => 'white',
                ],
            ],
            'mainImage' => [
                'id' => 8,
            ],
            'company' => [
                'id' => 1,
                'name' => 'Test company',
                'slug' => 'test-company',
                'averageRating' => 3.5,
                'isProfessional' => true,
                'image' => [
                    'id' => 8,
                ],
            ],
            'slug' => 'very-confortable-chair',
            'categoryPath' => [
                [
                    'id' => 1,
                    'name' => 'Food',
                    'slug' => 'food',
                ],
            ],
            'isAvailable' => false,
        ]);

        $this->assertSame('42_1_2', $favorite->getId());
        $this->assertSame(42, $favorite->getProductId());
        $this->assertSame('Very comfortable chair', $favorite->getName());
        $this->assertSame('4006381333933', $favorite->getCode());
        $this->assertSame(79.99, $favorite->getPriceWithTaxes());
        $this->assertSame(67.49, $favorite->getPriceWithoutVat());
        $this->assertSame(12.5, $favorite->getVat());
        $this->assertSame(99.99, $favorite->getCrossedOutPrice());
        $this->assertSame(24, $favorite->getAmount());
        $this->assertSame('http://example.com', $favorite->getAffiliateLink());
        $this->assertCount(1, $favorite->getOptions());
        $this->assertInstanceOf(DeclinationOption::class, $favorite->getOptions()[0]);
        $this->assertSame(1, $favorite->getOptions()[0]->getId());
        $this->assertSame('Color', $favorite->getOptions()[0]->getName());
        $this->assertSame(2, $favorite->getOptions()[0]->getVariantId());
        $this->assertSame('white', $favorite->getOptions()[0]->getVariantName());
        $this->assertInstanceOf(Image::class, $favorite->getMainImage());
        $this->assertSame(8, $favorite->getMainImage()->getId());
        $this->assertInstanceOf(CompanySummary::class, $favorite->getCompany());
        $this->assertSame(1, $favorite->getCompany()->getId());
        $this->assertSame('Test company', $favorite->getCompany()->getName());
        $this->assertSame('test-company', $favorite->getCompany()->getSlug());
        $this->assertInstanceOf(Image::class, $favorite->getCompany()->getImage());
        $this->assertSame(8, $favorite->getCompany()->getImage()->getId());
        $this->assertSame(3.5, $favorite->getCompany()->getAverageRating());
        $this->assertTrue($favorite->getCompany()->isProfessional());
        $this->assertSame('very-confortable-chair', $favorite->getSlug());
        $this->assertCount(1, $favorite->getCategoryPath());
        $this->assertInstanceOf(ProductCategory::class, $favorite->getCategoryPath()[0]);
        $this->assertSame(1, $favorite->getCategoryPath()[0]->getId());
        $this->assertSame('Food', $favorite->getCategoryPath()[0]->getName());
        $this->assertSame('food', $favorite->getCategoryPath()[0]->getSlug());
        $this->assertFalse($favorite->isAvailable());
    }
}
