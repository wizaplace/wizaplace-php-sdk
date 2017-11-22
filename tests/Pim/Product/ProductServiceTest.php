<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types=1);

namespace Wizaplace\SDK\Tests\Pim\Product;

use Wizaplace\SDK\Pim\Product\Product;
use Wizaplace\SDK\Pim\Product\ProductApprovalStatus;
use Wizaplace\SDK\Pim\Product\ProductAttachment;
use Wizaplace\SDK\Pim\Product\ProductDeclination;
use Wizaplace\SDK\Pim\Product\ProductImage;
use Wizaplace\SDK\Pim\Product\ProductService;
use Wizaplace\SDK\Pim\Product\ProductStatus;
use Wizaplace\SDK\Tests\ApiTestCase;

final class ProductServiceTest extends ApiTestCase
{
    public function testGetProductById()
    {
        $product = $this->buildProductService()->getProductById(5);

        $this->assertInstanceOf(Product::class, $product);
        $this->assertSame(5, $product->getId());
        $this->assertSame('Product with complex attributes', $product->getName());
        $this->assertSame('32094574920', $product->getCode());
        $this->assertSame('TEST-ATTRIBUTES', $product->getSupplierReference());
        $this->assertSame('', $product->getFullDescription());
        $this->assertSame('', $product->getShortDescription());
        $this->assertInstanceOf(\DateTimeInterface::class, $product->getCreatedAt());
        $this->assertGreaterThan(1500000000, $product->getCreatedAt()->getTimestamp());
        $this->assertInstanceOf(\DateTimeInterface::class, $product->getLastUpdateAt());
        $this->assertGreaterThanOrEqual($product->getCreatedAt()->getTimestamp(), $product->getLastUpdateAt()->getTimestamp());
        $this->assertFalse($product->isDownloadable());
        $this->assertFalse($product->hasFreeShipping());
        $this->assertSame(1.23, $product->getWeight());
        $this->assertSame(3, $product->getCompanyId());
        $this->assertSame(5, $product->getMainCategoryId());
        $this->assertNull($product->getAffiliateLink());
        $this->assertTrue(ProductStatus::ENABLED()->equals($product->getStatus()));
        $this->assertTrue(ProductApprovalStatus::APPROVED()->equals($product->getApprovalStatus()));
        $this->assertSame(0.0, $product->getGreenTax());
        $this->assertNull($product->getGeolocation());
        $this->assertTrue($product->isBrandNew());
        $this->assertSame([
            'Free attribute multiple' => [
                'réponse - 1 #',
                'réponse - 2 @',
                4985,
            ],
            'Free attribute simple' => [
                'valeur simple du free attribute #12M%M_°09£*/.?',
            ],
            'Free attribute simple mais en tableau' => [
                'une bien belle valeur déjà encapsulée',
            ],
            'Free attribute integer ?' => [
                92254094,
            ],
            'Free attribute integer mais en tableau' => [
                'la même histoire par ici',
            ],
        ], $product->getFreeAttributes());
        $this->assertContainsOnly(ProductAttachment::class, $product->getAttachments());
        $this->assertSame([2], $product->getTaxIds());
        $this->assertNull($product->getMainImage());
        $this->assertContainsOnly(ProductImage::class, $product->getAdditionalImages());
        $this->assertContainsOnly(ProductDeclination::class, $product->getDeclinations());
        $this->assertCount(1, $product->getDeclinations());
        $declination = $product->getDeclinations()[0];
        $this->assertSame(15, $declination->getAmount());
        $this->assertSame(15.0, $declination->getPrice());
        $this->assertEmpty($declination->getOptionsVariants());
        $this->assertNull($declination->getCrossedOutPrice());
        $this->assertNull($declination->getCode());
        $this->assertNull($declination->getAffiliateLink());
    }

    public function testGetProductWithOptionsById()
    {
        $product = $this->buildProductService()->getProductById(2);

        $this->assertSame(2, $product->getId());
        $this->assertContainsOnly(ProductDeclination::class, $product->getDeclinations());
        $this->assertCount(12, $product->getDeclinations());

        $declination = $product->getDeclinations()[0];
        $this->assertSame(10, $declination->getAmount());
        $this->assertSame([6 => 1], $declination->getOptionsVariants());
        $this->assertSame(15.5, $declination->getPrice());
        $this->assertSame(0.0, $declination->getCrossedOutPrice());
        $this->assertSame('color_white', $declination->getCode());
        $this->assertNull($declination->getAffiliateLink());
    }

    private function buildProductService($userEmail = 'admin@wizaplace.com', $userPassword = 'password'): ProductService
    {
        $apiClient = $this->buildApiClient();
        $apiClient->authenticate($userEmail, $userPassword);

        return new ProductService($apiClient);
    }
}
