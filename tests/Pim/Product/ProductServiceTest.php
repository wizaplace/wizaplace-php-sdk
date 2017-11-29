<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types=1);

namespace Wizaplace\SDK\Tests\Pim\Product;

use Wizaplace\SDK\Pagination;
use Wizaplace\SDK\Pim\Product\Product;
use Wizaplace\SDK\Pim\Product\ProductApprovalStatus;
use Wizaplace\SDK\Pim\Product\ProductAttachment;
use Wizaplace\SDK\Pim\Product\ProductDeclination;
use Wizaplace\SDK\Pim\Product\ProductGeolocation;
use Wizaplace\SDK\Pim\Product\ProductImage;
use Wizaplace\SDK\Pim\Product\ProductListFilter;
use Wizaplace\SDK\Pim\Product\ProductService;
use Wizaplace\SDK\Pim\Product\ProductStatus;
use Wizaplace\SDK\Pim\Product\ProductSummary;
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
        $this->assertNull($product->getAffiliateLink());
        $declination = $product->getDeclinations()[0];
        $this->assertSame(15, $declination->getQuantity());
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
        $this->assertSame(10, $declination->getQuantity());
        $this->assertSame([6 => 1], $declination->getOptionsVariants());
        $this->assertSame(15.5, $declination->getPrice());
        $this->assertSame(0.0, $declination->getCrossedOutPrice());
        $this->assertSame('color_white', $declination->getCode());
        $this->assertNull($declination->getAffiliateLink());
    }

    public function testGetProductWithAttachments()
    {
        $product = $this->buildProductService()->getProductById(7);

        $this->assertSame(7, $product->getId());
        $attachments = $product->getAttachments();
        $this->assertContainsOnly(ProductAttachment::class, $attachments);
        $this->assertCount(2, $attachments);

        $attachment = $attachments[0];
        $this->assertNotEmpty($attachment->getId());
        $this->assertSame('Manuel de montage', $attachment->getLabel());
    }

    public function testGetProductWithGeolocation()
    {
        $product = $this->buildProductService()->getProductById(6);

        $this->assertSame(6, $product->getId());
        $geolocation = $product->getGeolocation();
        $this->assertInstanceOf(ProductGeolocation::class, $geolocation);

        $this->assertSame('Wizacha', $geolocation->getLabel());
        $this->assertSame('69009', $geolocation->getZipcode());
        $this->assertSame(45.778848, $geolocation->getLatitude());
        $this->assertSame(4.800039, $geolocation->getLongitude());
    }

    public function testListProductsWithDefaultArgs()
    {
        $result = $this->buildProductService()->listProducts();
        $products = $result->getProducts();
        $this->assertContainsOnly(ProductSummary::class, $products);
        $this->assertLessThanOrEqual(100, count($products));
        $this->assertGreaterThan(0, count($products));

        $pagination = $result->getPagination();
        $this->assertInstanceOf(Pagination::class, $pagination);
        $this->assertSame(1, $pagination->getPage());
        $this->assertSame(100, $pagination->getResultsPerPage());
        $this->assertSame(9, $pagination->getNbResults());
        $this->assertSame(1, $pagination->getNbPages());

        foreach ($products as $product) {
            $this->assertGreaterThan(0, $product->getId());
            $this->assertNotEmpty($product->getCode());
            $this->assertNotEmpty($product->getName());
            $this->assertGreaterThan(0, $product->getMainCategoryId());
            $this->assertGreaterThan(1500000000, $product->getCreatedAt()->getTimestamp());
            $this->assertGreaterThanOrEqual($product->getCreatedAt()->getTimestamp(), $product->getLastUpdateAt()->getTimestamp());
            $this->assertGreaterThan(0, $product->getCompanyId());
        }
    }

    public function testListProductWithGeolocation()
    {
        $filter = (new ProductListFilter())->byProductCode('20230495445');
        $products = $this->buildProductService()->listProducts($filter)->getProducts();
        $this->assertContainsOnly(ProductSummary::class, $products);
        $this->assertCount(1, $products);

        $geolocation = $products[0]->getGeolocation();
        $this->assertInstanceOf(ProductGeolocation::class, $geolocation);

        $this->assertSame('Wizacha', $geolocation->getLabel());
        $this->assertSame('69009', $geolocation->getZipcode());
        $this->assertSame(45.778848, $geolocation->getLatitude());
        $this->assertSame(4.800039, $geolocation->getLongitude());
    }

    public function testListProductWithAttachments()
    {
        $filter = (new ProductListFilter())->byProductCode('20230495446');
        $products = $this->buildProductService()->listProducts($filter)->getProducts();
        $this->assertContainsOnly(ProductSummary::class, $products);
        $this->assertCount(1, $products);

        $attachments = $products[0]->getAttachments();
        $this->assertContainsOnly(ProductAttachment::class, $attachments);
        $this->assertCount(2, $attachments);

        $attachment = $attachments[0];
        $this->assertNotEmpty($attachment->getId());
        $this->assertSame('Manuel de montage', $attachment->getLabel());
    }

    public function testListProductPagination()
    {
        $result1 = $this->buildProductService()->listProducts(null, 1, 1);
        $productsPage1 = $result1->getProducts();
        $this->assertContainsOnly(ProductSummary::class, $productsPage1);
        $this->assertCount(1, $productsPage1);
        $paginationPage1 = $result1->getPagination();
        $this->assertInstanceOf(Pagination::class, $paginationPage1);
        $this->assertSame(1, $paginationPage1->getPage());
        $this->assertSame(1, $paginationPage1->getResultsPerPage());
        $this->assertSame(9, $paginationPage1->getNbResults());
        $this->assertSame(9, $paginationPage1->getNbPages());

        $result2 = $this->buildProductService()->listProducts(null, 2, 1);
        $productsPage2 = $result2->getProducts();
        $this->assertContainsOnly(ProductSummary::class, $productsPage2);
        $this->assertCount(1, $productsPage2);
        $paginationPage2 = $result2->getPagination();
        $this->assertInstanceOf(Pagination::class, $paginationPage2);
        $this->assertSame(2, $paginationPage2->getPage());
        $this->assertSame(1, $paginationPage2->getResultsPerPage());
        $this->assertSame(9, $paginationPage2->getNbResults());
        $this->assertSame(9, $paginationPage2->getNbPages());

        $this->assertNotEquals($productsPage2[0]->getId(), $productsPage1[0]->getId());

        $result = $this->buildProductService()->listProducts(null, 1, 2);
        $products = $result->getProducts();
        $this->assertContainsOnly(ProductSummary::class, $productsPage1);
        $this->assertCount(1, $productsPage1);
        $this->assertSame($productsPage1[0]->getId(), $products[0]->getId());
        $this->assertSame($productsPage2[0]->getId(), $products[1]->getId());
        $pagination = $result->getPagination();
        $this->assertInstanceOf(Pagination::class, $pagination);
        $this->assertSame(1, $pagination->getPage());
        $this->assertSame(2, $pagination->getResultsPerPage());
        $this->assertSame(9, $pagination->getNbResults());
        $this->assertSame(5, $pagination->getNbPages());
    }

    public function testListProductsWithCategoryFilter()
    {
        $filter = (new ProductListFilter())->byCategoryIds([3], false);
        $products = $this->buildProductService()->listProducts($filter)->getProducts();
        $this->assertContainsOnly(ProductSummary::class, $products);
        $this->assertGreaterThanOrEqual(2, count($products));

        foreach ($products as $product) {
            $this->assertSame(3, $product->getMainCategoryId());
        }
    }

    public function testListProductsWithCategoryAndSubCategoriesFilter()
    {
        $filter = (new ProductListFilter())->byCategoryIds([3], true);
        $products = $this->buildProductService()->listProducts($filter)->getProducts();
        $this->assertContainsOnly(ProductSummary::class, $products);
        $this->assertGreaterThanOrEqual(2, count($products));

        $categoriesIds = [];
        foreach ($products as $product) {
            $categoriesIds[$product->getMainCategoryId()] = true;
        }

        $this->assertSame([
            3 => true,
            4 => true,
        ], $categoriesIds);
    }

    /**
     * @dataProvider statusProvider
     */
    public function testListProductsWithStatusFilter(ProductStatus $status, int $minimumExpectedCount)
    {
        $filter = (new ProductListFilter())->byStatus($status);
        $products = $this->buildProductService()->listProducts($filter)->getProducts();
        $this->assertContainsOnly(ProductSummary::class, $products);
        $this->assertGreaterThanOrEqual($minimumExpectedCount, count($products));

        foreach ($products as $product) {
            $this->assertTrue($status->equals($product->getStatus()));
        }
    }

    /**
     * @see \Wizaplace\SDK\Tests\Pim\Product\ProductServiceTest::testListProductsWithStatusFilter
     */
    public function statusProvider(): array {
        return [
            'enabled' => [ProductStatus::ENABLED(), 2],
            'hidden' => [ProductStatus::HIDDEN(), 0], // @TODO: add hidden products in fixtures
            'disabled' => [ProductStatus::DISABLED(), 0], // @TODO: add disabled products in fixtures
        ];
    }

    /**
     * @dataProvider multiFilterProvider
     */
    public function testListProductsWithMultipleFilters(
        ?ProductStatus $status = null,
        ?array $categoryIds = null,
        bool $includeSubCategories,
        ?array $expectedCategoryIds,
        ?string $productCode = null,
        int $minimumExpectedCount = 1
    ) {
        $filter = new ProductListFilter();
        if ($status !== null) {
            $filter->byStatus($status);
        }
        if ($categoryIds !== null) {
            $filter->byCategoryIds($categoryIds, $includeSubCategories);
        }
        if ($productCode !== null) {
            $filter->byProductCode($productCode);
        }

        $products = $this->buildProductService()->listProducts($filter)->getProducts();
        $this->assertContainsOnly(ProductSummary::class, $products);
        $this->assertGreaterThanOrEqual($minimumExpectedCount, count($products));

        $categoriesIds = [];
        foreach ($products as $product) {
            $categoriesIds[$product->getMainCategoryId()] = true;
            if ($status !== null) {
                $this->assertTrue($status->equals($product->getStatus()));
            }
            if ($productCode !== null) {
                $this->assertSame($productCode, $product->getCode());
            }
        }

        if ($expectedCategoryIds !== null) {
            $this->assertSame($expectedCategoryIds, $categoriesIds);
        }
    }

    public function multiFilterProvider(): array {
        return [
            'enabled, in a specific category or its subcategories' => [
                ProductStatus::ENABLED(),
                [3],
                true,
                [
                    3 => true,
                    4 => true,
                ],
                null,
                2
            ],
            'enabled, in a specific category or its subcategories, with a specific product code' => [
                ProductStatus::ENABLED(),
                [3],
                true,
                [
                    4 => true,
                ],
                '0000001',
                1
            ],
            'disabled, in a specific category or its subcategories' => [
                ProductStatus::DISABLED(),
                [3],
                true,
                [],
                null,
                0
            ],
            'enabled, with a specific product code' => [
                ProductStatus::ENABLED(),
                null,
                false,
                null,
                '20230495445',
                1
            ],
            'disabled, with a specific product code which is enabled' => [
                ProductStatus::DISABLED(),
                null,
                false,
                [],
                '20230495445',
                0
            ],
        ];
    }

    private function buildProductService($userEmail = 'admin@wizaplace.com', $userPassword = 'password'): ProductService
    {
        $apiClient = $this->buildApiClient();
        $apiClient->authenticate($userEmail, $userPassword);

        return new ProductService($apiClient);
    }
}
