<?php
declare(strict_types = 1);

namespace Wizaplace\Tests\Favorite;

use Wizaplace\Favorite\Declination;
use Wizaplace\Favorite\Declination\DeclinationOption;
use Wizaplace\Favorite\Declination\DeclinationCompany;
use Wizaplace\Image\Image;
use PHPUnit\Framework\TestCase;

class DeclinationTest extends TestCase
{
    public function testInterface()
    {
        $favorite = new Declination([
            'id' => '42_1_2',
            'productId' => 42,
            'name' => 'Very comfortable chair',
            'code' => '4006381333933',
            'supplierReference' => 'abcdef',
            'prices' => [
                'priceWithoutVat' => 67.49,
                'priceWithTaxes' => 79.99,
                'vat' => 12.5,
            ],
            'greenTax' => 0.99,
            'crossedOutPrice' => 99.99,
            'reductionPercentage' => -20,
            'quantity' => 24,
            'affiliateLink' => 'http://example.com',
            'options' => [
                [
                    'id' => 1,
                    'name' => 'Color',
                    'variantId' => 2,
                    'variantName' => 'white',
                ],
            ],
            'images' => [
                ['id' => 8],
            ],
            'isUsed' => false,
            'description' => 'Very comfortable chair made in France',
            'shortDescription' => 'Chair made in France',
            'company' => [
                'id' => 1,
                'name' => 'Test company',
                'slug' => 'test-company',
                'image' => [
                    'id' => 8,
                ],
            ],
            'slug' => 'very-confortable-chair',
            'categorySlugPath' => 'some-category',
        ]);

        $this->assertSame('42_1_2', $favorite->getId());
        $this->assertSame(42, $favorite->getProductId());
        $this->assertSame('Very comfortable chair', $favorite->getName());
        $this->assertSame('4006381333933', $favorite->getCode());
        $this->assertSame('abcdef', $favorite->getSupplierReference());
        $this->assertSame(79.99, $favorite->getPriceWithTaxes());
        $this->assertSame(67.49, $favorite->getPriceWithoutVat());
        $this->assertSame(12.5, $favorite->getVat());
        $this->assertSame(0.99, $favorite->getGreenTax());
        $this->assertSame(99.99, $favorite->getCrossedOutPrice());
        $this->assertSame(-20.0, $favorite->getReductionPercentage());
        $this->assertSame(24, $favorite->getQuantity());
        $this->assertSame('http://example.com', $favorite->getAffiliateLink());
        $this->assertCount(1, $favorite->getOptions());
        $this->assertInstanceOf(DeclinationOption::class, $favorite->getOptions()[0]);
        $this->assertSame(1, $favorite->getOptions()[0]->getId());
        $this->assertSame('Color', $favorite->getOptions()[0]->getName());
        $this->assertSame(2, $favorite->getOptions()[0]->getVariantId());
        $this->assertSame('white', $favorite->getOptions()[0]->getVariantName());
        $this->assertCount(1, $favorite->getImages());
        $this->assertInstanceOf(Image::class, $favorite->getImages()[0]);
        $this->assertSame(8, $favorite->getImages()[0]->getId());
        $this->assertFalse($favorite->isUsed());
        $this->assertSame('Very comfortable chair made in France', $favorite->getDescription());
        $this->assertSame('Chair made in France', $favorite->getShortDescription());
        $this->assertInstanceOf(DeclinationCompany::class, $favorite->getCompany());
        $this->assertSame(1, $favorite->getCompany()->getId());
        $this->assertSame('Test company', $favorite->getCompany()->getName());
        $this->assertSame('test-company', $favorite->getCompany()->getSlug());
        $this->assertInstanceOf(Image::class, $favorite->getCompany()->getImage());
        $this->assertSame(8, $favorite->getCompany()->getImage()->getId());
        $this->assertSame('very-confortable-chair', $favorite->getSlug());
        $this->assertSame('some-category', $favorite->getCategorySlugPath());
    }
}
