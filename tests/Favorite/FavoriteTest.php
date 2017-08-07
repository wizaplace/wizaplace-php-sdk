<?php
declare(strict_types = 1);

namespace Wizaplace\Tests\Favorite;

use Wizaplace\Favorite\Favorite;
use Wizaplace\Image\Image;
use PHPUnit\Framework\TestCase;

class FavoriteTest extends TestCase
{
    public function testInterface()
    {
        $favorite = new Favorite([
            'productId' => 42,
            'name' => 'Very comfortable chair',
            'code' => '4006381333933',
            'supplierReference' => 'abcdef',
            'price' => 79.99,
            'prices' => [
                'priceWithoutVat' => 67.49,
                'priceWithTaxes' => 79.99,
                'vat' => 12.5,
            ],
            'greenTax' => 0.99,
            'quantity' => 24,
            'affiliateLink' => 'http://example.com',
            'images' => [
                ['id' => 8],
            ],
            'slug' => 'very-confortable-chair',
            'categorySlugPath' => 'some-category',
        ]);

        $this->assertSame(42, $favorite->getProductId());
        $this->assertSame('Very comfortable chair', $favorite->getName());
        $this->assertSame('4006381333933', $favorite->getCode());
        $this->assertSame('abcdef', $favorite->getSupplierReference());
        $this->assertSame(79.99, $favorite->getPrice());
        $this->assertSame(67.49, $favorite->getPriceWithoutVat());
        $this->assertSame(12.5, $favorite->getVat());
        $this->assertSame(0.99, $favorite->getGreenTax());
        $this->assertSame(24, $favorite->getQuantity());
        $this->assertSame('http://example.com', $favorite->getAffiliateLink());
        $this->assertCount(1, $favorite->getImages());
        $this->assertInstanceOf(Image::class, $favorite->getImages()[0]);
        $this->assertSame(8, $favorite->getImages()[0]->getId());
        $this->assertSame('very-confortable-chair', $favorite->getSlug());
        $this->assertSame('some-category', $favorite->getCategorySlugPath());
    }
}
