<?php

/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Tests\Pim\Product;

use Composer\Autoload\ClassLoader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use GuzzleHttp\Psr7\Uri;
use Psr\Http\Message\UriInterface;
use Wizaplace\SDK\Exception\NotFound;
use Wizaplace\SDK\Exception\SomeParametersAreInvalid;
use Wizaplace\SDK\Pagination;
use Wizaplace\SDK\Pim\Product\CreateProductCommand;
use Wizaplace\SDK\Pim\Product\Product;
use Wizaplace\SDK\Pim\Product\ProductApprovalStatus;
use Wizaplace\SDK\Pim\Product\ProductAttachment;
use Wizaplace\SDK\Pim\Product\ProductAttachmentUpload;
use Wizaplace\SDK\Pim\Product\ProductDeclination;
use Wizaplace\SDK\Pim\Product\ProductDeclinationUpsertData;
use Wizaplace\SDK\Pim\Product\ProductGeolocation;
use Wizaplace\SDK\Pim\Product\ProductGeolocationUpsertData;
use Wizaplace\SDK\Pim\Product\ProductImageUpload;
use Wizaplace\SDK\Pim\Product\ProductInventory;
use Wizaplace\SDK\Pim\Product\ProductListFilter;
use Wizaplace\SDK\Pim\Product\RelatedProduct\RelatedProduct;
use Wizaplace\SDK\Pim\Product\ProductService;
use Wizaplace\SDK\Pim\Product\ProductStatus;
use Wizaplace\SDK\Pim\Product\ProductSummary;
use Wizaplace\SDK\Pim\Product\Shipping;
use Wizaplace\SDK\Pim\Product\UpdateProductCommand;
use Wizaplace\SDK\Pim\Product\UpdateShippingCommand;
use Wizaplace\SDK\Tests\ApiTestCase;

final class ProductServiceTest extends ApiTestCase
{
    public function testGetProductById(): void
    {
        $product = $this->buildProductService()->getProductById(8);

        static::assertInstanceOf(Product::class, $product);
        static::assertSame(8, $product->getId());
        static::assertSame('Product with complex attributes', $product->getName());
        static::assertSame('32094574920', $product->getCode());
        static::assertSame('TEST-ATTRIBUTES', $product->getSupplierReference());
        static::assertSame('', $product->getFullDescription());
        static::assertSame('', $product->getShortDescription());
        static::assertInstanceOf(\DateTimeInterface::class, $product->getCreatedAt());
        static::assertGreaterThan(1500000000, $product->getCreatedAt()->getTimestamp());
        static::assertInstanceOf(\DateTimeInterface::class, $product->getLastUpdateAt());
        static::assertGreaterThanOrEqual($product->getCreatedAt()->getTimestamp(), $product->getLastUpdateAt()->getTimestamp());
        static::assertFalse($product->isDownloadable());
        static::assertFalse($product->hasFreeShipping());
        static::assertSame(1.23, $product->getWeight());
        static::assertSame(3, $product->getCompanyId());
        static::assertSame(6, $product->getMainCategoryId());
        static::assertNull($product->getAffiliateLink());
        static::assertTrue(ProductStatus::ENABLED()->equals($product->getStatus()));
        static::assertTrue(ProductApprovalStatus::APPROVED()->equals($product->getApprovalStatus()));
        static::assertSame(0.0, $product->getGreenTax());
        static::assertNull($product->getGeolocation());
        static::assertTrue($product->isBrandNew());
        static::assertSame(
            [
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
            ],
            $product->getFreeAttributes()
        );
        static::assertContainsOnly(ProductAttachment::class, $product->getAttachments());
        static::assertSame([2], $product->getTaxIds());
        static::assertNull($product->getMainImage());
        static::assertInstanceOf(\DateTimeImmutable::class, $product->getAvailibilityDate());
        static::assertGreaterThan(130000000, $product->getAvailibilityDate()->getTimestamp());
        static::assertContainsOnly(UriInterface::class, $product->getAdditionalImages());
        static::assertContainsOnly(ProductDeclination::class, $product->getDeclinations());
        static::assertCount(1, $product->getDeclinations());
        static::assertNull($product->getAffiliateLink());
        $declination = $product->getDeclinations()[0];
        static::assertSame(15, $declination->getQuantity());
        static::assertSame(15.0, $declination->getPrice());
        static::assertSame(0, $declination->getPriceTiers()[0]->getLowerLimit());
        static::assertSame(15.0, $declination->getPriceTiers()[0]->getPrice());
        static::assertEmpty($declination->getOptionsVariants());
        static::assertNull($declination->getCrossedOutPrice());
        static::assertNull($declination->getCode());
        static::assertNull($declination->getAffiliateLink());
        static::assertSame('product', $product->getProductTemplateType());
        static::assertContainsOnly(ProductInventory::class, $product->getInventory());
    }

    public function testGetProductWithOptionsById(): void
    {
        $product = $this->buildProductService()->getProductById(2);

        static::assertSame(2, $product->getId());
        static::assertContainsOnly(ProductDeclination::class, $product->getDeclinations());
        static::assertCount(8, $product->getDeclinations());

        $declination = $product->getDeclinations()[0];
        static::assertSame(5, $declination->getQuantity());
        static::assertSame([1 => 1, 2 => 5], $declination->getOptionsVariants());
        static::assertSame(15.5, $declination->getPrice());
        static::assertSame(0, $declination->getPriceTiers()[0]->getLowerLimit());
        static::assertSame(15.5, $declination->getPriceTiers()[0]->getPrice());
        static::assertNull($declination->getCrossedOutPrice());
        static::assertSame('color_white_connectivity_wireles', $declination->getCode());
        static::assertNull($declination->getAffiliateLink());
    }

    public function testGetProductWithAttachments(): void
    {
        $product = $this->buildProductService()->getProductById(10);

        static::assertSame(10, $product->getId());
        $attachments = $product->getAttachments();
        static::assertContainsOnly(ProductAttachment::class, $attachments);
        static::assertCount(2, $attachments);

        $attachment = $attachments[0];
        static::assertNotEmpty($attachment->getId());
        static::assertSame('Manuel de montage', $attachment->getLabel());
    }

    public function testGetProductWithGeolocation(): void
    {
        $product = $this->buildProductService()->getProductById(9);

        static::assertSame(9, $product->getId());
        $geolocation = $product->getGeolocation();
        static::assertInstanceOf(ProductGeolocation::class, $geolocation);

        static::assertSame('Wizacha', $geolocation->getLabel());
        static::assertSame('69009', $geolocation->getZipcode());
        static::assertSame(45.778848, $geolocation->getLatitude());
        static::assertSame(4.800039, $geolocation->getLongitude());
    }

    public function testListProductsWithDefaultArgs(): void
    {
        $result = $this->buildProductService('admin@wizaplace.com', 'Windows.98')->listProducts();
        $products = $result->getProducts();
        static::assertContainsOnly(ProductSummary::class, $products);
        static::assertLessThanOrEqual(100, \count($products));
        static::assertGreaterThan(0, \count($products));

        $pagination = $result->getPagination();
        static::assertInstanceOf(Pagination::class, $pagination);
        static::assertSame(1, $pagination->getPage());
        static::assertSame(100, $pagination->getResultsPerPage());
        static::assertSame(39, $pagination->getNbResults());
        static::assertSame(1, $pagination->getNbPages());

        foreach ($products as $product) {
            static::assertGreaterThan(0, $product->getId());
            static::assertNotEmpty($product->getCode());
            static::assertNotEmpty($product->getName());
            static::assertGreaterThan(0, $product->getMainCategoryId());
            static::assertGreaterThan(1500000000, $product->getCreatedAt()->getTimestamp());
            static::assertGreaterThanOrEqual($product->getCreatedAt()->getTimestamp(), $product->getLastUpdateAt()->getTimestamp());
            static::assertGreaterThan(0, $product->getCompanyId());
            static::assertTrue(\is_array($product->getDivisions()));

            static::assertTrue(\is_array($product->getTaxIds()));

            if ($product->getId() === 1) {
                static::assertEquals(1, \count($product->getTaxIds()));
                static::assertEquals(3, $product->getTaxIds()[0]);
                static::assertNotEmpty($product->getShortDescription());
                static::assertNotEmpty($product->getFullDescription());
                static::assertNull($product->getVideo());
            }

            if ($product->getId() === 3) {
                static::assertEquals(1, \count($product->getTaxIds()));
                static::assertEquals(2, $product->getTaxIds()[0]);
                static::assertEmpty($product->getShortDescription());
                static::assertEmpty($product->getFullDescription());
                static::assertRegExp('/\/\/.*amazonaws\.com\/wizachatest\/videos\/.*\/.*\.mp4/', $product->getVideo());
            }
        }
    }

    public function testListProductWithGeolocation(): void
    {
        $filter = (new ProductListFilter())->byProductCode('20230495445');
        $products = $this->buildProductService()->listProducts($filter)->getProducts();
        static::assertContainsOnly(ProductSummary::class, $products);
        static::assertCount(1, $products);

        $geolocation = $products[0]->getGeolocation();
        static::assertInstanceOf(ProductGeolocation::class, $geolocation);

        static::assertSame('Wizacha', $geolocation->getLabel());
        static::assertSame('69009', $geolocation->getZipcode());
        static::assertSame(45.778848, $geolocation->getLatitude());
        static::assertSame(4.800039, $geolocation->getLongitude());
    }

    public function testListProductWithAttachments(): void
    {
        $filter = (new ProductListFilter())->byProductCode('20230495446');
        $products = $this->buildProductService()->listProducts($filter)->getProducts();
        static::assertContainsOnly(ProductSummary::class, $products);
        static::assertCount(1, $products);

        $attachments = $products[0]->getAttachments();
        static::assertContainsOnly(ProductAttachment::class, $attachments);
        static::assertCount(2, $attachments);

        $attachment = $attachments[0];
        static::assertNotEmpty($attachment->getId());
        static::assertSame('Manuel de montage', $attachment->getLabel());
    }

    public function testListProductWithQuotesData(): void
    {
        $filter = (new ProductListFilter())->byProductCode('product_with_quotes_data');
        $products = $this
            ->buildProductService('admin@wizaplace.com', ApiTestCase::VALID_PASSWORD)
            ->listProducts($filter)
            ->getProducts();
        static::assertContainsOnly(ProductSummary::class, $products);
        static::assertCount(1, $products);

        static::assertSame(5, $products[0]->getQuoteRequestsMinQuantity());
        static::assertTrue($products[0]->isExclusiveToQuoteRequests());
    }

    public function testListProductPagination(): void
    {
        $result1 = $this->buildProductService()->listProducts(null, 1, 1);
        $productsPage1 = $result1->getProducts();
        static::assertContainsOnly(ProductSummary::class, $productsPage1);
        static::assertCount(1, $productsPage1);
        $paginationPage1 = $result1->getPagination();
        static::assertInstanceOf(Pagination::class, $paginationPage1);
        static::assertSame(1, $paginationPage1->getPage());
        static::assertSame(1, $paginationPage1->getResultsPerPage());
        static::assertSame(9, $paginationPage1->getNbResults());
        static::assertSame(9, $paginationPage1->getNbPages());

        $result2 = $this->buildProductService()->listProducts(null, 2, 1);
        $productsPage2 = $result2->getProducts();
        static::assertContainsOnly(ProductSummary::class, $productsPage2);
        static::assertCount(1, $productsPage2);
        $paginationPage2 = $result2->getPagination();
        static::assertInstanceOf(Pagination::class, $paginationPage2);
        static::assertSame(2, $paginationPage2->getPage());
        static::assertSame(1, $paginationPage2->getResultsPerPage());
        static::assertSame(9, $paginationPage2->getNbResults());
        static::assertSame(9, $paginationPage2->getNbPages());

        static::assertNotEquals($productsPage2[0]->getId(), $productsPage1[0]->getId());

        $result = $this->buildProductService()->listProducts(null, 1, 2);
        $products = $result->getProducts();
        static::assertContainsOnly(ProductSummary::class, $productsPage1);
        static::assertCount(1, $productsPage1);
        static::assertSame($productsPage1[0]->getId(), $products[0]->getId());
        static::assertSame($productsPage2[0]->getId(), $products[1]->getId());
        $pagination = $result->getPagination();
        static::assertInstanceOf(Pagination::class, $pagination);
        static::assertSame(1, $pagination->getPage());
        static::assertSame(2, $pagination->getResultsPerPage());
        static::assertSame(9, $pagination->getNbResults());
        static::assertSame(5, $pagination->getNbPages());
    }

    public function testListProductsWithCategoryFilter(): void
    {
        $filter = (new ProductListFilter())->byCategoryIds([3], false);
        $products = $this->buildProductService()->listProducts($filter)->getProducts();
        static::assertContainsOnly(ProductSummary::class, $products);
        static::assertGreaterThanOrEqual(2, \count($products));

        foreach ($products as $product) {
            static::assertSame(3, $product->getMainCategoryId());
        }
    }

    public function testListProductsWithCategoryAndSubCategoriesFilter(): void
    {
        $filter = (new ProductListFilter())->byCategoryIds([3], true);
        $products = $this->buildProductService()->listProducts($filter)->getProducts();
        static::assertContainsOnly(ProductSummary::class, $products);
        static::assertGreaterThanOrEqual(2, \count($products));

        $categoriesIds = [];
        foreach ($products as $product) {
            $categoriesIds[$product->getMainCategoryId()] = true;
        }

        static::assertSame(
            [
                3 => true,
                4 => true,
            ],
            $categoriesIds
        );
    }

    public function testListProductsWithCompanyFilter(): void
    {
        $filter = (new ProductListFilter())->byCompanyIds([3]);
        $products = $this->buildProductService()->listProducts($filter)->getProducts();
        static::assertContainsOnly(ProductSummary::class, $products);
        static::assertGreaterThanOrEqual(2, \count($products));

        foreach ($products as $product) {
            static::assertSame(3, $product->getCompanyId());
        }
    }

    public function testDeleteProduct(): void
    {
        $service = $this->buildProductService();
        static::assertNotNull($service->getProductById(1));
        $service->deleteProduct(1);

        $this->expectException(NotFound::class);
        $service->getProductById(1);
    }

    /**
     * @dataProvider statusProvider
     */
    public function testListProductsWithStatusFilter(ProductStatus $status, int $minimumExpectedCount): void
    {
        $filter = (new ProductListFilter())->byStatus($status);
        $products = $this->buildProductService()->listProducts($filter)->getProducts();
        static::assertContainsOnly(ProductSummary::class, $products);
        static::assertGreaterThanOrEqual($minimumExpectedCount, \count($products));

        foreach ($products as $product) {
            static::assertTrue($status->equals($product->getStatus()));
        }
    }

    /**
     * @see \Wizaplace\SDK\Tests\Pim\Product\ProductServiceTest::testListProductsWithStatusFilter
     */
    public function statusProvider(): array
    {
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
        ?array $ids = null,
        ?array $productCodes = null,
        ?array $supplierReferences = null,
        int $minimumExpectedCount = 1
    ): void {
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
        if (\is_array($ids)) {
            $filter->byIds($ids);
        }
        if (\is_array($productCodes)) {
            $filter->byProductCodes($productCodes);
        }
        if (\is_array($supplierReferences)) {
            $filter->bySupplierReferences($supplierReferences);
        }

        $products = $this->buildProductService()->listProducts($filter)->getProducts();
        static::assertContainsOnly(ProductSummary::class, $products);
        static::assertGreaterThanOrEqual($minimumExpectedCount, \count($products));

        $categoriesIds = [];
        foreach ($products as $product) {
            $categoriesIds[$product->getMainCategoryId()] = true;
            if ($status !== null) {
                static::assertTrue($status->equals($product->getStatus()));
            }
            if ($productCode !== null) {
                static::assertSame($productCode, $product->getCode());
            }
            if ($ids !== null) {
                $this->assertContains($product->getId(), $ids);
            }
            if ($productCodes !== null) {
                $this->assertContains($product->getCode(), $productCodes);
            }
            if ($supplierReferences !== null) {
                $this->assertContains($product->getSupplierReference(), $supplierReferences);
            }
        }

        if ($expectedCategoryIds !== null) {
            static::assertSame($expectedCategoryIds, $categoriesIds);
        }
    }

    public function multiFilterProvider(): array
    {
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
                null,
                null,
                null,
                2,
            ],
            'enabled, in a specific category or its subcategories, with a specific product code' => [
                ProductStatus::ENABLED(),
                [3],
                true,
                [
                    4 => true,
                ],
                '0000001',
                null,
                null,
                null,
                1,
            ],
            'disabled, in a specific category or its subcategories' => [
                ProductStatus::DISABLED(),
                [3],
                true,
                [],
                null,
                null,
                null,
                null,
                0,
            ],
            'enabled, with a specific product code' => [
                ProductStatus::ENABLED(),
                null,
                false,
                null,
                '20230495445',
                null,
                null,
                null,
                1,
            ],
            'disabled, with a specific product code which is enabled' => [
                ProductStatus::DISABLED(),
                null,
                false,
                [],
                '20230495445',
                null,
                null,
                null,
                0,
            ],
            'enabled, with a specific list of ids' => [
                null,
                null,
                false,
                null,
                null,
                [14, 15],
                null,
                null,
                2,
            ],
            'enabled, with a specific list of productsCode' => [
                null,
                null,
                false,
                null,
                null,
                null,
                ['0000001', '20230495445'],
                null,
                2,
            ],
            'enabled, with a specific list of supplierReferences' => [
                null,
                null,
                false,
                null,
                null,
                null,
                null,
                ['INFO-001', 'INFO-002'],
                2,
            ],
        ];
    }

    public function testCreateComplexProduct(): void
    {
        $availibilityDate = new \DateTimeImmutable('@1519224245');
        $data = (new CreateProductCommand())
            ->setCode("code_full_D")
            ->setGreenTax(0.1)
            ->setInfiniteStock(true)
            ->setIsBrandNew(true)
            ->setName("Full product")
            ->setSupplierReference('supplierref_full')
            ->setStatus(ProductStatus::ENABLED())
            ->setMainCategoryId(4)
            ->setFreeAttributes(
                [
                    'freeAttr1' => 'freeAttr1Value',
                    'freeAttr2' => 42,
                    'freeAttr3' => ['freeAttr3Value', 42],
                ]
            )
            ->setHasFreeShipping(true)
            ->setWeight(0.2)
            ->setIsDownloadable(true)
            ->setMainImage(new Uri('https://sandbox.wizaplace.com/assets/bundles/app/images/favicon.png'))
            ->setAdditionalImages(
                [
                    (new ProductImageUpload())->setName('image1.png')
                    ->setMimeType('image/png')
                    ->setBase64Data('iVBORw0KGgoAAAANSUhEUgAAACAAAAAgCAYAAABzenr0AAAFkElEQVRYw72XS2wbVRSGvzszthPHcWYmzYOEvJo0IUlbmqZvWkDiISoVCVUUVapUIVZsWCAhgYQEQqxgBaqExIIVFQtAqCCxQYK2qKQtSUlTtSUNbp2kNImdxLHrOHHGj2Hha9dO3DZ9cSRr5Lln7jn3v/899z+CVVhTSxuADmwCtshnG7BGuswAPuA80A8MAeExv++uc4u7BBZAPbAPeE0G1u/wnQ2EZSLfAj8Bk2N+n33PCTS1tLmB/cDbwEZA494sCQwCnwHHxvy+hVUlIOGuBz4EDgFuHsxiwDfAR8CN5duiFgneCnwhIXfy4OYEeoBO4LRumOFIOJQbVJY5Pw4cAfYWGXsQU+ScRyS6KxFoamkrAz4BXn3IwfO3uw3w6IZ5PBIOJXIJSLYfBN57SLDfKYku4JpumBci4VBupQ2S7W4evbllrAYAVa7+sPwp/D9WBYzqhtmv6oZpAh8DzS6hsMep0+uoYDadIGanbk/tci8Nz7yI0dHNQnCK1FL8tr5ltfU0v/AypZVVLAQnsVNJBfAAP2pAL/AkwC6HzgeetZQIlU6tjE9jo1h2uuhGtj63j+ZXDoIQONweRn44CvbKgqc6nXQcOEzt1qdIJSxSS3GmBvqQMXsVYCtQDlAqFJxCQchkmtXSoisyFQe73FUIVUUoCjVbdlKim0V9PfWNmJ0bQAgUTUNxOLJD5cBWRRYJAXA+EWUitQSAoTjY7qgoOmmP5uX54XFKY4s5iM2O7qK+1T3bcXq8ACwEA8z5hvOB7FGA9uybqbTF2UQkN/q008ArCq8ADcFup07DRJBG/3imymgaNb0781cHgKPMw5r1PSAyFX96qJ/47HS+S7sClGX/pbE5boWYl+Rbp7rp1jwFk9apLjY5ytESSZ64MIyayvianRvw1DUU+Fasbcfb2JK5meKLBAf/xE4XcKpsxbG7nIxxKTkPQIlQ2O3UUfPurB0OnWolU6vMv6/A1CQALq+eWW0WX6FQs3kHqqsEgIj/H8LXRorW6Fj+i0U7xUlrjhR2LmCt6soltMupo8iE/pwZ5+r5s9mI1PTuxFGWQcxlmFR2bcyIBDtNYKCP5OKKGzmmACvSOm2FuSHJWKs66dUyJGpT3XRpZTLRNL8vhZjoP4UVvQmAt7EFvbUDgMqujbhrHgMgHppl5uJgMY6OKFI0FBzgQB4ZVQTPOg1Khcoep0G5JOWVZIzLyRg3x0eZG7mU8XWVUN2zDdXpoqZnO4qa8Z25OEgsMFlMPQ0qUsNF80fS2PxmzRKVZFzv8LDF4WWbowIhvzxphYjaSdIJi6n+PtLJZKbGbuhlzfpN6O1dAKSsJQIDfdipFVU1CvQrwDkpIgtsOI+MXqHxemkda2Vhmk5bnJEIAcxcGmR+4joApVXVrNt/CFeFkYlyfZSw70ox+IeAcwowJwVkqpCMaU5YoRwZuzUPLpE5NP2JCNdTt2r/UiTM9FA/2DZCUfE2tSKEANsmMHAaaz66PHhKxpxTpGI9VgyFU9YcI8lC5s7bKX5ZmiWZTxvbZqLvBIszwQLfheAkgb/OLKdYdvXHxvw+WwXQDTMqj+NLQK6cLdhpgmmLNs1NqVCZsxMcjU/yax4yWUtEo1jzN/HUNaCoGrHABCPff50jaH5ewPvAH5Fw6FaFkTL8c+CNfF0ggCrFSa3iImInuZGKF64+/5YUApdRSYlhEg/NEg+Hlt+QaeBL4J2sTBfLVHG9dNj7CMRJGvgZeHPM75soKssj4VBUN8w+YJ0UkOIhB39rzO/797Z9gUwirBvmSanduh6CSI0BXwHvLg9eNIE8JE7IhrMRqL6PLUkCA5JwR8b8vtD9Nqd1sjk9AGxeRXM6J8v7d7I5nbqv5rRIy1YhdVy2PW+VyAAEgKvyfA/IZ2Q17fl/LFcBJAQMmYcAAAAASUVORK5CYII='),
                    new Uri('https://sandbox.wizaplace.com/assets/bundles/app/images/favicon.png'),
                ]
            )
            ->setFullDescription("super full description")
            ->setShortDescription("super short description")
            ->setTaxIds([1, 2])
            ->setDeclinations(
                [
                    (new ProductDeclinationUpsertData([1 => 1, 2 => 5, 3 => 7]))
                    ->setCode('code_full_declA')
                    ->setPrice(3.5)
                    ->setQuantity(12)
                    ->setInfiniteStock(true)
                    ->setPriceTiers(
                        [
                            [
                                'lowerLimit' => 0,
                                'price' => 18.99,
                            ],
                            [
                                'lowerLimit' => 15,
                                'price' => 15.99,
                            ],
                        ]
                    ),
                    (new ProductDeclinationUpsertData([1 => 1, 2 => 6, 3 => 9]))
                    ->setPrice(100.0)
                    ->setCrossedOutPrice(1000.0)
                    ->setQuantity(3)
                    ->setInfiniteStock(true),
                ]
            )
            ->setGeolocation(
                (new ProductGeolocationUpsertData(/* latitude */ 45.778848, /* longitude */ 4.800039))
                    ->setLabel('Wizacha')
                    ->setZipcode('69009')
            )
            ->setAvailabilityDate($availibilityDate)
            ->setAttachments([new ProductAttachmentUpload('favicon', 'https://sandbox.wizaplace.com/assets/bundles/app/images/favicon.png')])
            ->setProductTemplateType('product');

        $productService = $this->buildProductService('vendor@wizaplace.com');
        $productId = $productService->createProduct($data);
        static::assertInternalType('int', $productId);
        static::assertGreaterThan(0, $productId);

        $product = $productService->getProductById($productId);
        static::assertInstanceOf(Product::class, $product);

        static::assertSame($productId, $product->getId());
        static::assertSame(4, $product->getMainCategoryId());
        static::assertSame("code_full_D", $product->getCode());
        static::assertSame("Full product", $product->getName());
        static::assertSame('supplierref_full', $product->getSupplierReference());
        static::assertSame("super full description", $product->getFullDescription());
        static::assertSame("super short description", $product->getShortDescription());
        static::assertTrue($product->isBrandNew());
        static::assertTrue($product->hasFreeShipping());
        static::assertSame([1, 2], $product->getTaxIds());
        static::assertSame(
            [
                'freeAttr1' => ['freeAttr1Value'],
                'freeAttr2' => [42],
                'freeAttr3' => ['freeAttr3Value', 42],
            ],
            $product->getFreeAttributes()
        );
        static::assertSame(0.1, $product->getGreenTax());
        static::assertTrue($product->hasInfiniteStock());
        static::assertSame(0.2, $product->getWeight());
        static::assertTrue(ProductStatus::ENABLED()->equals($product->getStatus()));
        static::assertTrue(ProductApprovalStatus::PENDING()->equals($product->getApprovalStatus()));
        static::assertTrue($product->isDownloadable());
        static::assertRegExp('#/images/detailed/0/[^.]+.png#', (string) $product->getMainImage());
        $additionalImages = $product->getAdditionalImages();
        static::assertCount(2, $additionalImages);
        static::assertRegExp('#/images/detailed/0/[^.]+.png#', (string) $additionalImages[0]);
        static::assertRegExp('#/images/detailed/0/[^.]+.png#', (string) $additionalImages[1]);
        static::assertNotEquals((string) $additionalImages[0], (string) $additionalImages[1]);

        $attachments = $product->getAttachments();
        static::assertContainsOnly(ProductAttachment::class, $attachments);
        static::assertCount(1, $attachments);
        static::assertSame('favicon', $attachments[0]->getLabel());
        static::assertNotEmpty($attachments[0]->getId());

        $geolocation = $product->getGeolocation();
        static::assertInstanceOf(ProductGeolocation::class, $geolocation);
        static::assertSame('Wizacha', $geolocation->getLabel());
        static::assertSame('69009', $geolocation->getZipcode());
        static::assertSame(45.778848, $geolocation->getLatitude());
        static::assertSame(4.800039, $geolocation->getLongitude());

        static::assertSame($availibilityDate->getTimestamp(), $product->getAvailibilityDate()->getTimestamp());
        static::assertSame('product', $product->getProductTemplateType());

        // Checking declinations
        $declinations = $product->getDeclinations();
        static::assertContainsOnly(ProductDeclination::class, $declinations);
        static::assertCount(4, $declinations);

        static::assertSame([1 => 1, 2 => 5, 3 => 7], $declinations[0]->getOptionsVariants());
        static::assertSame('code_full_declA', $declinations[0]->getCode());
        static::assertNull($declinations[0]->getAffiliateLink());
        static::assertNull($declinations[0]->getCrossedOutPrice());
        static::assertSame(12, $declinations[0]->getQuantity());
        static::assertSame(18.99, $declinations[0]->getPrice());
        static::assertSame(0, $declinations[0]->getPriceTiers()[0]->getLowerLimit());
        static::assertSame(18.99, $declinations[0]->getPriceTiers()[0]->getPrice());
        static::assertSame(15, $declinations[0]->getPriceTiers()[1]->getLowerLimit());
        static::assertSame(15.99, $declinations[0]->getPriceTiers()[1]->getPrice());

        // empty declination generated automatically to complete the matrix
        static::assertSame([1 => 1, 2 => 5, 3 => 9], $declinations[1]->getOptionsVariants());
        static::assertSame(0.0, $declinations[1]->getPrice());
        static::assertSame(0, $declinations[1]->getQuantity());
        static::assertSame(0, $declinations[1]->getPriceTiers()[0]->getLowerLimit());
        static::assertSame(0.0, $declinations[1]->getPriceTiers()[0]->getPrice());
        static::assertNull($declinations[1]->getCrossedOutPrice());
        static::assertNull($declinations[1]->getAffiliateLink());
        static::assertNull($declinations[1]->getCode());

        // empty declination generated automatically to complete the matrix
        static::assertSame([1 => 1, 2 => 6, 3 => 7], $declinations[2]->getOptionsVariants());
        static::assertSame(0.0, $declinations[2]->getPrice());
        static::assertSame(0, $declinations[2]->getQuantity());
        static::assertSame(0, $declinations[2]->getPriceTiers()[0]->getLowerLimit());
        static::assertSame(0.0, $declinations[2]->getPriceTiers()[0]->getPrice());
        static::assertNull($declinations[2]->getCrossedOutPrice());
        static::assertNull($declinations[2]->getAffiliateLink());
        static::assertNull($declinations[2]->getCode());

        static::assertSame([1 => 1, 2 => 6, 3 => 9], $declinations[3]->getOptionsVariants());
        static::assertSame(100.0, $declinations[3]->getPrice());
        static::assertSame(3, $declinations[3]->getQuantity());
        static::assertSame(0, $declinations[3]->getPriceTiers()[0]->getLowerLimit());
        static::assertSame(100.0, $declinations[3]->getPriceTiers()[0]->getPrice());
        static::assertSame(1000.0, $declinations[3]->getCrossedOutPrice());
        static::assertNull($declinations[3]->getAffiliateLink());
        static::assertNull($declinations[3]->getCode());
    }

    public function testPartialProductUpdate(): void
    {
        $data = (new CreateProductCommand())
            ->setCode("code_full_EE")
            ->setGreenTax(0.1)
            ->setIsBrandNew(true)
            ->setName("Full product")
            ->setSupplierReference('supplierref_full')
            ->setStatus(ProductStatus::ENABLED())
            ->setMainCategoryId(4)
            ->setFreeAttributes(
                [
                    'freeAttr1' => 'freeAttr1Value',
                    'freeAttr2' => 42,
                    'freeAttr3' => ['freeAttr3Value', 42],
                ]
            )
            ->setHasFreeShipping(true)
            ->setWeight(0.2)
            ->setIsDownloadable(true)
            ->setMainImage(new Uri('https://sandbox.wizaplace.com/assets/bundles/app/images/favicon.png'))
            ->setAdditionalImages(
                [
                    (new ProductImageUpload())->setName('image1.png')
                    ->setMimeType('image/png')
                    ->setBase64Data('iVBORw0KGgoAAAANSUhEUgAAACAAAAAgCAYAAABzenr0AAAFkElEQVRYw72XS2wbVRSGvzszthPHcWYmzYOEvJo0IUlbmqZvWkDiISoVCVUUVapUIVZsWCAhgYQEQqxgBaqExIIVFQtAqCCxQYK2qKQtSUlTtSUNbp2kNImdxLHrOHHGj2Hha9dO3DZ9cSRr5Lln7jn3v/899z+CVVhTSxuADmwCtshnG7BGuswAPuA80A8MAeExv++uc4u7BBZAPbAPeE0G1u/wnQ2EZSLfAj8Bk2N+n33PCTS1tLmB/cDbwEZA494sCQwCnwHHxvy+hVUlIOGuBz4EDgFuHsxiwDfAR8CN5duiFgneCnwhIXfy4OYEeoBO4LRumOFIOJQbVJY5Pw4cAfYWGXsQU+ScRyS6KxFoamkrAz4BXn3IwfO3uw3w6IZ5PBIOJXIJSLYfBN57SLDfKYku4JpumBci4VBupQ2S7W4evbllrAYAVa7+sPwp/D9WBYzqhtmv6oZpAh8DzS6hsMep0+uoYDadIGanbk/tci8Nz7yI0dHNQnCK1FL8tr5ltfU0v/AypZVVLAQnsVNJBfAAP2pAL/AkwC6HzgeetZQIlU6tjE9jo1h2uuhGtj63j+ZXDoIQONweRn44CvbKgqc6nXQcOEzt1qdIJSxSS3GmBvqQMXsVYCtQDlAqFJxCQchkmtXSoisyFQe73FUIVUUoCjVbdlKim0V9PfWNmJ0bQAgUTUNxOLJD5cBWRRYJAXA+EWUitQSAoTjY7qgoOmmP5uX54XFKY4s5iM2O7qK+1T3bcXq8ACwEA8z5hvOB7FGA9uybqbTF2UQkN/q008ArCq8ADcFup07DRJBG/3imymgaNb0781cHgKPMw5r1PSAyFX96qJ/47HS+S7sClGX/pbE5boWYl+Rbp7rp1jwFk9apLjY5ytESSZ64MIyayvianRvw1DUU+Fasbcfb2JK5meKLBAf/xE4XcKpsxbG7nIxxKTkPQIlQ2O3UUfPurB0OnWolU6vMv6/A1CQALq+eWW0WX6FQs3kHqqsEgIj/H8LXRorW6Fj+i0U7xUlrjhR2LmCt6soltMupo8iE/pwZ5+r5s9mI1PTuxFGWQcxlmFR2bcyIBDtNYKCP5OKKGzmmACvSOm2FuSHJWKs66dUyJGpT3XRpZTLRNL8vhZjoP4UVvQmAt7EFvbUDgMqujbhrHgMgHppl5uJgMY6OKFI0FBzgQB4ZVQTPOg1Khcoep0G5JOWVZIzLyRg3x0eZG7mU8XWVUN2zDdXpoqZnO4qa8Z25OEgsMFlMPQ0qUsNF80fS2PxmzRKVZFzv8LDF4WWbowIhvzxphYjaSdIJi6n+PtLJZKbGbuhlzfpN6O1dAKSsJQIDfdipFVU1CvQrwDkpIgtsOI+MXqHxemkda2Vhmk5bnJEIAcxcGmR+4joApVXVrNt/CFeFkYlyfZSw70ox+IeAcwowJwVkqpCMaU5YoRwZuzUPLpE5NP2JCNdTt2r/UiTM9FA/2DZCUfE2tSKEANsmMHAaaz66PHhKxpxTpGI9VgyFU9YcI8lC5s7bKX5ZmiWZTxvbZqLvBIszwQLfheAkgb/OLKdYdvXHxvw+WwXQDTMqj+NLQK6cLdhpgmmLNs1NqVCZsxMcjU/yax4yWUtEo1jzN/HUNaCoGrHABCPff50jaH5ewPvAH5Fw6FaFkTL8c+CNfF0ggCrFSa3iImInuZGKF64+/5YUApdRSYlhEg/NEg+Hlt+QaeBL4J2sTBfLVHG9dNj7CMRJGvgZeHPM75soKssj4VBUN8w+YJ0UkOIhB39rzO/797Z9gUwirBvmSanduh6CSI0BXwHvLg9eNIE8JE7IhrMRqL6PLUkCA5JwR8b8vtD9Nqd1sjk9AGxeRXM6J8v7d7I5nbqv5rRIy1YhdVy2PW+VyAAEgKvyfA/IZ2Q17fl/LFcBJAQMmYcAAAAASUVORK5CYII='),
                    new Uri('https://sandbox.wizaplace.com/assets/bundles/app/images/favicon.png'),
                ]
            )
            ->setFullDescription("super full description")
            ->setShortDescription("super short description")
            ->setTaxIds([1, 2])
            ->setDeclinations(
                [
                    (new ProductDeclinationUpsertData([1 => 1, 2 => 5, 3 => 7]))
                    ->setCode('code_full_declA')
                    ->setPrice(3.5)
                    ->setQuantity(12)
                    ->setInfiniteStock(true),
                    (new ProductDeclinationUpsertData([1 => 1, 2 => 6, 3 => 9]))
                    ->setPrice(100.0)
                    ->setCrossedOutPrice(1000.0)
                    ->setQuantity(3)
                    ->setInfiniteStock(true)
                    ->setPriceTiers(
                        [
                            [
                                'lowerLimit' => 0,
                                'price' => 99.59,
                            ],
                            [
                                'lowerLimit' => 30,
                                'price' => 80.99,
                            ],
                        ]
                    ),
                ]
            )
            ->setAttachments([new ProductAttachmentUpload('favicon', 'https://sandbox.wizaplace.com/assets/bundles/app/images/favicon.png')])
            ->setProductTemplateType('product');

        $productService = $this->buildProductService('vendor@wizaplace.com');
        $productId = $productService->createProduct($data);
        static::assertInternalType('int', $productId);
        static::assertGreaterThan(0, $productId);

        $productService->updateProduct((new UpdateProductCommand($productId))->setName('Full product 2'));

        $product = $productService->getProductById($productId);
        static::assertInstanceOf(Product::class, $product);

        static::assertSame($productId, $product->getId());
        static::assertSame(4, $product->getMainCategoryId());
        static::assertSame("code_full_EE", $product->getCode());
        static::assertSame("Full product 2", $product->getName());
        static::assertSame('supplierref_full', $product->getSupplierReference());
        static::assertSame("super full description", $product->getFullDescription());
        static::assertSame("super short description", $product->getShortDescription());
        static::assertTrue($product->isBrandNew());
        static::assertTrue($product->hasFreeShipping());
        static::assertSame([1, 2], $product->getTaxIds());
        static::assertSame(
            [
                'freeAttr1' => ['freeAttr1Value'],
                'freeAttr2' => [42],
                'freeAttr3' => ['freeAttr3Value', 42],
            ],
            $product->getFreeAttributes()
        );
        static::assertSame(0.1, $product->getGreenTax());
        static::assertSame(0.2, $product->getWeight());
        static::assertTrue(ProductStatus::ENABLED()->equals($product->getStatus()));
        static::assertTrue(ProductApprovalStatus::PENDING()->equals($product->getApprovalStatus()));
        static::assertTrue($product->isDownloadable());
        static::assertRegExp('#/images/detailed/0/[^.]+.png#', (string) $product->getMainImage());
        $additionalImages = $product->getAdditionalImages();
        static::assertCount(2, $additionalImages);
        static::assertRegExp('#/images/detailed/0/[^.]+.png#', (string) $additionalImages[0]);
        static::assertRegExp('#/images/detailed/0/[^.]+.png#', (string) $additionalImages[1]);
        static::assertNotEquals((string) $additionalImages[0], (string) $additionalImages[1]);

        $attachments = $product->getAttachments();
        static::assertContainsOnly(ProductAttachment::class, $attachments);
        static::assertCount(1, $attachments);
        static::assertSame('favicon', $attachments[0]->getLabel());
        static::assertNotEmpty($attachments[0]->getId());

        static::assertSame('product', $product->getProductTemplateType());

        // Checking declinations
        $declinations = $product->getDeclinations();
        static::assertContainsOnly(ProductDeclination::class, $declinations);
        static::assertCount(4, $declinations);

        static::assertSame([1 => 1, 2 => 5, 3 => 7], $declinations[0]->getOptionsVariants());
        static::assertSame('code_full_declA', $declinations[0]->getCode());
        static::assertNull($declinations[0]->getAffiliateLink());
        static::assertNull($declinations[0]->getCrossedOutPrice());
        static::assertSame(12, $declinations[0]->getQuantity());
        static::assertSame(3.5, $declinations[0]->getPrice());
        static::assertSame(0, $declinations[0]->getPriceTiers()[0]->getLowerLimit());
        static::assertSame(3.5, $declinations[0]->getPriceTiers()[0]->getPrice());


        // empty declination generated automatically to complete the matrix
        static::assertSame([1 => 1, 2 => 5, 3 => 9], $declinations[1]->getOptionsVariants());
        static::assertSame(0.0, $declinations[1]->getPrice());
        static::assertSame(0, $declinations[1]->getPriceTiers()[0]->getLowerLimit());
        static::assertSame(0.0, $declinations[1]->getPriceTiers()[0]->getPrice());
        static::assertSame(0, $declinations[1]->getQuantity());
        static::assertNull($declinations[1]->getCrossedOutPrice());
        static::assertNull($declinations[1]->getAffiliateLink());
        static::assertNull($declinations[1]->getCode());

        // empty declination generated automatically to complete the matrix
        static::assertSame([1 => 1, 2 => 6, 3 => 7], $declinations[2]->getOptionsVariants());
        static::assertSame(0.0, $declinations[2]->getPrice());
        static::assertSame(0, $declinations[2]->getPriceTiers()[0]->getLowerLimit());
        static::assertSame(0.0, $declinations[2]->getPriceTiers()[0]->getPrice());
        static::assertSame(0, $declinations[2]->getQuantity());
        static::assertNull($declinations[2]->getCrossedOutPrice());
        static::assertNull($declinations[2]->getAffiliateLink());
        static::assertNull($declinations[2]->getCode());

        static::assertSame([1 => 1, 2 => 6, 3 => 9], $declinations[3]->getOptionsVariants());
        static::assertSame(99.59, $declinations[3]->getPrice());
        static::assertSame(0, $declinations[3]->getPriceTiers()[0]->getLowerLimit());
        static::assertSame(99.59, $declinations[3]->getPriceTiers()[0]->getPrice());
        static::assertSame(30, $declinations[3]->getPriceTiers()[1]->getLowerLimit());
        static::assertSame(80.99, $declinations[3]->getPriceTiers()[1]->getPrice());
        static::assertSame(3, $declinations[3]->getQuantity());
        static::assertSame(1000.0, $declinations[3]->getCrossedOutPrice());
        static::assertNull($declinations[3]->getAffiliateLink());
        static::assertNull($declinations[3]->getCode());
    }

    public function testUpdateComplexProduct(): void
    {
        $data = (new CreateProductCommand())
            ->setCode("code_full_A")
            ->setGreenTax(0.1)
            ->setIsBrandNew(true)
            ->setName("Full product")
            ->setSupplierReference('supplierref_full')
            ->setStatus(ProductStatus::ENABLED())
            ->setMainCategoryId(4)
            ->setFreeAttributes(
                [
                    'freeAttr1' => 'freeAttr1Value',
                    'freeAttr2' => 42,
                    'freeAttr3' => ['freeAttr3Value', 42],
                ]
            )
            ->setHasFreeShipping(true)
            ->setWeight(0.2)
            ->setIsDownloadable(true)
            ->setMainImage(new Uri('https://www.google.com/favicon.ico'))
            ->setAdditionalImages(
                [
                    (new ProductImageUpload())->setName('image1.png')
                    ->setMimeType('image/png')
                    ->setBase64Data('iVBORw0KGgoAAAANSUhEUgAAACAAAAAgCAYAAABzenr0AAAFkElEQVRYw72XS2wbVRSGvzszthPHcWYmzYOEvJo0IUlbmqZvWkDiISoVCVUUVapUIVZsWCAhgYQEQqxgBaqExIIVFQtAqCCxQYK2qKQtSUlTtSUNbp2kNImdxLHrOHHGj2Hha9dO3DZ9cSRr5Lln7jn3v/899z+CVVhTSxuADmwCtshnG7BGuswAPuA80A8MAeExv++uc4u7BBZAPbAPeE0G1u/wnQ2EZSLfAj8Bk2N+n33PCTS1tLmB/cDbwEZA494sCQwCnwHHxvy+hVUlIOGuBz4EDgFuHsxiwDfAR8CN5duiFgneCnwhIXfy4OYEeoBO4LRumOFIOJQbVJY5Pw4cAfYWGXsQU+ScRyS6KxFoamkrAz4BXn3IwfO3uw3w6IZ5PBIOJXIJSLYfBN57SLDfKYku4JpumBci4VBupQ2S7W4evbllrAYAVa7+sPwp/D9WBYzqhtmv6oZpAh8DzS6hsMep0+uoYDadIGanbk/tci8Nz7yI0dHNQnCK1FL8tr5ltfU0v/AypZVVLAQnsVNJBfAAP2pAL/AkwC6HzgeetZQIlU6tjE9jo1h2uuhGtj63j+ZXDoIQONweRn44CvbKgqc6nXQcOEzt1qdIJSxSS3GmBvqQMXsVYCtQDlAqFJxCQchkmtXSoisyFQe73FUIVUUoCjVbdlKim0V9PfWNmJ0bQAgUTUNxOLJD5cBWRRYJAXA+EWUitQSAoTjY7qgoOmmP5uX54XFKY4s5iM2O7qK+1T3bcXq8ACwEA8z5hvOB7FGA9uybqbTF2UQkN/q008ArCq8ADcFup07DRJBG/3imymgaNb0781cHgKPMw5r1PSAyFX96qJ/47HS+S7sClGX/pbE5boWYl+Rbp7rp1jwFk9apLjY5ytESSZ64MIyayvianRvw1DUU+Fasbcfb2JK5meKLBAf/xE4XcKpsxbG7nIxxKTkPQIlQ2O3UUfPurB0OnWolU6vMv6/A1CQALq+eWW0WX6FQs3kHqqsEgIj/H8LXRorW6Fj+i0U7xUlrjhR2LmCt6soltMupo8iE/pwZ5+r5s9mI1PTuxFGWQcxlmFR2bcyIBDtNYKCP5OKKGzmmACvSOm2FuSHJWKs66dUyJGpT3XRpZTLRNL8vhZjoP4UVvQmAt7EFvbUDgMqujbhrHgMgHppl5uJgMY6OKFI0FBzgQB4ZVQTPOg1Khcoep0G5JOWVZIzLyRg3x0eZG7mU8XWVUN2zDdXpoqZnO4qa8Z25OEgsMFlMPQ0qUsNF80fS2PxmzRKVZFzv8LDF4WWbowIhvzxphYjaSdIJi6n+PtLJZKbGbuhlzfpN6O1dAKSsJQIDfdipFVU1CvQrwDkpIgtsOI+MXqHxemkda2Vhmk5bnJEIAcxcGmR+4joApVXVrNt/CFeFkYlyfZSw70ox+IeAcwowJwVkqpCMaU5YoRwZuzUPLpE5NP2JCNdTt2r/UiTM9FA/2DZCUfE2tSKEANsmMHAaaz66PHhKxpxTpGI9VgyFU9YcI8lC5s7bKX5ZmiWZTxvbZqLvBIszwQLfheAkgb/OLKdYdvXHxvw+WwXQDTMqj+NLQK6cLdhpgmmLNs1NqVCZsxMcjU/yax4yWUtEo1jzN/HUNaCoGrHABCPff50jaH5ewPvAH5Fw6FaFkTL8c+CNfF0ggCrFSa3iImInuZGKF64+/5YUApdRSYlhEg/NEg+Hlt+QaeBL4J2sTBfLVHG9dNj7CMRJGvgZeHPM75soKssj4VBUN8w+YJ0UkOIhB39rzO/797Z9gUwirBvmSanduh6CSI0BXwHvLg9eNIE8JE7IhrMRqL6PLUkCA5JwR8b8vtD9Nqd1sjk9AGxeRXM6J8v7d7I5nbqv5rRIy1YhdVy2PW+VyAAEgKvyfA/IZ2Q17fl/LFcBJAQMmYcAAAAASUVORK5CYII='),
                    new Uri('https://www.google.com/favicon.ico'),
                ]
            )
            ->setFullDescription("super full description")
            ->setShortDescription("super short description")
            ->setTaxIds([1, 2])
            ->setDeclinations(
                [
                    (new ProductDeclinationUpsertData([1 => 1, 2 => 5, 3 => 7]))
                    ->setCode('code_full_declA')
                    ->setPrice(3.5)
                    ->setQuantity(12)
                    ->setPriceTiers(
                        [
                            [
                                'lowerLimit' => 0,
                                'price' => 2.7,
                            ],
                            [
                                'lowerLimit' => 50,
                                'price' => 2.5,
                            ],
                        ]
                    ),
                    (new ProductDeclinationUpsertData([1 => 1, 2 => 6, 3 => 9]))
                    ->setPrice(100.0)
                    ->setCrossedOutPrice(1000.0)
                    ->setQuantity(3),
                ]
            )
        ->setAttachments([new ProductAttachmentUpload('favicon', 'https://www.google.com/favicon.ico')])
        ->setProductTemplateType('product');

        $productService = $this->buildProductService('vendor@wizaplace.com');
        $productId = $productService->createProduct($data);
        static::assertInternalType('int', $productId);
        static::assertGreaterThan(0, $productId);

        $data = (new UpdateProductCommand($productId))
            ->setCode("code_full_BB")
            ->setGreenTax(0.2)
            ->setIsBrandNew(false)
            ->setName("Full product2")
            ->setSupplierReference('supplierref_full2')
            ->setStatus(ProductStatus::DISABLED())
            ->setMainCategoryId(4)
            ->setFreeAttributes(
                [
                    'freeAttr1' => 'freeAttr1Value2',
                    'freeAttr2' => 43,
                    'freeAttr3' => ['freeAttr3Value2', 43],
                ]
            )
            ->setHasFreeShipping(false)
            ->setWeight(0.3)
            ->setIsDownloadable(false)
            ->setMainImage(
                (new ProductImageUpload())->setName('image3.png')
                    ->setMimeType('image/png')
                    ->setBase64Data('iVBORw0KGgoAAAANSUhEUgAAACAAAAAgCAYAAABzenr0AAAFkElEQVRYw72XS2wbVRSGvzszthPHcWYmzYOEvJo0IUlbmqZvWkDiISoVCVUUVapUIVZsWCAhgYQEQqxgBaqExIIVFQtAqCCxQYK2qKQtSUlTtSUNbp2kNImdxLHrOHHGj2Hha9dO3DZ9cSRr5Lln7jn3v/899z+CVVhTSxuADmwCtshnG7BGuswAPuA80A8MAeExv++uc4u7BBZAPbAPeE0G1u/wnQ2EZSLfAj8Bk2N+n33PCTS1tLmB/cDbwEZA494sCQwCnwHHxvy+hVUlIOGuBz4EDgFuHsxiwDfAR8CN5duiFgneCnwhIXfy4OYEeoBO4LRumOFIOJQbVJY5Pw4cAfYWGXsQU+ScRyS6KxFoamkrAz4BXn3IwfO3uw3w6IZ5PBIOJXIJSLYfBN57SLDfKYku4JpumBci4VBupQ2S7W4evbllrAYAVa7+sPwp/D9WBYzqhtmv6oZpAh8DzS6hsMep0+uoYDadIGanbk/tci8Nz7yI0dHNQnCK1FL8tr5ltfU0v/AypZVVLAQnsVNJBfAAP2pAL/AkwC6HzgeetZQIlU6tjE9jo1h2uuhGtj63j+ZXDoIQONweRn44CvbKgqc6nXQcOEzt1qdIJSxSS3GmBvqQMXsVYCtQDlAqFJxCQchkmtXSoisyFQe73FUIVUUoCjVbdlKim0V9PfWNmJ0bQAgUTUNxOLJD5cBWRRYJAXA+EWUitQSAoTjY7qgoOmmP5uX54XFKY4s5iM2O7qK+1T3bcXq8ACwEA8z5hvOB7FGA9uybqbTF2UQkN/q008ArCq8ADcFup07DRJBG/3imymgaNb0781cHgKPMw5r1PSAyFX96qJ/47HS+S7sClGX/pbE5boWYl+Rbp7rp1jwFk9apLjY5ytESSZ64MIyayvianRvw1DUU+Fasbcfb2JK5meKLBAf/xE4XcKpsxbG7nIxxKTkPQIlQ2O3UUfPurB0OnWolU6vMv6/A1CQALq+eWW0WX6FQs3kHqqsEgIj/H8LXRorW6Fj+i0U7xUlrjhR2LmCt6soltMupo8iE/pwZ5+r5s9mI1PTuxFGWQcxlmFR2bcyIBDtNYKCP5OKKGzmmACvSOm2FuSHJWKs66dUyJGpT3XRpZTLRNL8vhZjoP4UVvQmAt7EFvbUDgMqujbhrHgMgHppl5uJgMY6OKFI0FBzgQB4ZVQTPOg1Khcoep0G5JOWVZIzLyRg3x0eZG7mU8XWVUN2zDdXpoqZnO4qa8Z25OEgsMFlMPQ0qUsNF80fS2PxmzRKVZFzv8LDF4WWbowIhvzxphYjaSdIJi6n+PtLJZKbGbuhlzfpN6O1dAKSsJQIDfdipFVU1CvQrwDkpIgtsOI+MXqHxemkda2Vhmk5bnJEIAcxcGmR+4joApVXVrNt/CFeFkYlyfZSw70ox+IeAcwowJwVkqpCMaU5YoRwZuzUPLpE5NP2JCNdTt2r/UiTM9FA/2DZCUfE2tSKEANsmMHAaaz66PHhKxpxTpGI9VgyFU9YcI8lC5s7bKX5ZmiWZTxvbZqLvBIszwQLfheAkgb/OLKdYdvXHxvw+WwXQDTMqj+NLQK6cLdhpgmmLNs1NqVCZsxMcjU/yax4yWUtEo1jzN/HUNaCoGrHABCPff50jaH5ewPvAH5Fw6FaFkTL8c+CNfF0ggCrFSa3iImInuZGKF64+/5YUApdRSYlhEg/NEg+Hlt+QaeBL4J2sTBfLVHG9dNj7CMRJGvgZeHPM75soKssj4VBUN8w+YJ0UkOIhB39rzO/797Z9gUwirBvmSanduh6CSI0BXwHvLg9eNIE8JE7IhrMRqL6PLUkCA5JwR8b8vtD9Nqd1sjk9AGxeRXM6J8v7d7I5nbqv5rRIy1YhdVy2PW+VyAAEgKvyfA/IZ2Q17fl/LFcBJAQMmYcAAAAASUVORK5CYII=')
            )
            ->setAdditionalImages(
                [
                    new Uri('https://www.google.com/favicon.ico'),
                    (new ProductImageUpload())->setName('image2.png')
                    ->setMimeType('image/png')
                    ->setBase64Data('iVBORw0KGgoAAAANSUhEUgAAACAAAAAgCAYAAABzenr0AAAFkElEQVRYw72XS2wbVRSGvzszthPHcWYmzYOEvJo0IUlbmqZvWkDiISoVCVUUVapUIVZsWCAhgYQEQqxgBaqExIIVFQtAqCCxQYK2qKQtSUlTtSUNbp2kNImdxLHrOHHGj2Hha9dO3DZ9cSRr5Lln7jn3v/899z+CVVhTSxuADmwCtshnG7BGuswAPuA80A8MAeExv++uc4u7BBZAPbAPeE0G1u/wnQ2EZSLfAj8Bk2N+n33PCTS1tLmB/cDbwEZA494sCQwCnwHHxvy+hVUlIOGuBz4EDgFuHsxiwDfAR8CN5duiFgneCnwhIXfy4OYEeoBO4LRumOFIOJQbVJY5Pw4cAfYWGXsQU+ScRyS6KxFoamkrAz4BXn3IwfO3uw3w6IZ5PBIOJXIJSLYfBN57SLDfKYku4JpumBci4VBupQ2S7W4evbllrAYAVa7+sPwp/D9WBYzqhtmv6oZpAh8DzS6hsMep0+uoYDadIGanbk/tci8Nz7yI0dHNQnCK1FL8tr5ltfU0v/AypZVVLAQnsVNJBfAAP2pAL/AkwC6HzgeetZQIlU6tjE9jo1h2uuhGtj63j+ZXDoIQONweRn44CvbKgqc6nXQcOEzt1qdIJSxSS3GmBvqQMXsVYCtQDlAqFJxCQchkmtXSoisyFQe73FUIVUUoCjVbdlKim0V9PfWNmJ0bQAgUTUNxOLJD5cBWRRYJAXA+EWUitQSAoTjY7qgoOmmP5uX54XFKY4s5iM2O7qK+1T3bcXq8ACwEA8z5hvOB7FGA9uybqbTF2UQkN/q008ArCq8ADcFup07DRJBG/3imymgaNb0781cHgKPMw5r1PSAyFX96qJ/47HS+S7sClGX/pbE5boWYl+Rbp7rp1jwFk9apLjY5ytESSZ64MIyayvianRvw1DUU+Fasbcfb2JK5meKLBAf/xE4XcKpsxbG7nIxxKTkPQIlQ2O3UUfPurB0OnWolU6vMv6/A1CQALq+eWW0WX6FQs3kHqqsEgIj/H8LXRorW6Fj+i0U7xUlrjhR2LmCt6soltMupo8iE/pwZ5+r5s9mI1PTuxFGWQcxlmFR2bcyIBDtNYKCP5OKKGzmmACvSOm2FuSHJWKs66dUyJGpT3XRpZTLRNL8vhZjoP4UVvQmAt7EFvbUDgMqujbhrHgMgHppl5uJgMY6OKFI0FBzgQB4ZVQTPOg1Khcoep0G5JOWVZIzLyRg3x0eZG7mU8XWVUN2zDdXpoqZnO4qa8Z25OEgsMFlMPQ0qUsNF80fS2PxmzRKVZFzv8LDF4WWbowIhvzxphYjaSdIJi6n+PtLJZKbGbuhlzfpN6O1dAKSsJQIDfdipFVU1CvQrwDkpIgtsOI+MXqHxemkda2Vhmk5bnJEIAcxcGmR+4joApVXVrNt/CFeFkYlyfZSw70ox+IeAcwowJwVkqpCMaU5YoRwZuzUPLpE5NP2JCNdTt2r/UiTM9FA/2DZCUfE2tSKEANsmMHAaaz66PHhKxpxTpGI9VgyFU9YcI8lC5s7bKX5ZmiWZTxvbZqLvBIszwQLfheAkgb/OLKdYdvXHxvw+WwXQDTMqj+NLQK6cLdhpgmmLNs1NqVCZsxMcjU/yax4yWUtEo1jzN/HUNaCoGrHABCPff50jaH5ewPvAH5Fw6FaFkTL8c+CNfF0ggCrFSa3iImInuZGKF64+/5YUApdRSYlhEg/NEg+Hlt+QaeBL4J2sTBfLVHG9dNj7CMRJGvgZeHPM75soKssj4VBUN8w+YJ0UkOIhB39rzO/797Z9gUwirBvmSanduh6CSI0BXwHvLg9eNIE8JE7IhrMRqL6PLUkCA5JwR8b8vtD9Nqd1sjk9AGxeRXM6J8v7d7I5nbqv5rRIy1YhdVy2PW+VyAAEgKvyfA/IZ2Q17fl/LFcBJAQMmYcAAAAASUVORK5CYII='),
                ]
            )
            ->setFullDescription("super full description 2")
            ->setShortDescription("super short description 2")
            ->setTaxIds([2, 3])
            ->setDeclinations(
                [
                    (new ProductDeclinationUpsertData([1 => 1, 2 => 5, 3 => 7]))
                    ->setCode('code_full_declA')
                    ->setPrice(3.6)
                    ->setQuantity(13),
                    (new ProductDeclinationUpsertData([1 => 1, 2 => 6, 3 => 9]))
                    ->setCrossedOutPrice(1000.2)
                    ->setQuantity(4)
                    ->setPriceTiers(
                        [
                            [
                                'lowerLimit' => 0,
                                'price' => 99.99,
                            ],
                            [
                                'lowerLimit' => 120,
                                'price' => 89.99,
                            ],
                        ]
                    ),
                ]
            )
            ->setMaxPriceAdjustment(10)
            ->setAttachments([new ProductAttachmentUpload('favicon2', 'https://www.google.com/favicon.ico')])
            ->setProductTemplateType('product');

        $newProductId = $productService->updateProduct($data);
        static::assertSame($productId, $newProductId);

        $product = $productService->getProductById($productId);
        static::assertInstanceOf(Product::class, $product);

        static::assertSame($productId, $product->getId());
        static::assertSame(4, $product->getMainCategoryId());
        static::assertSame("code_full_BB", $product->getCode());
        static::assertSame("Full product2", $product->getName());
        static::assertSame('supplierref_full2', $product->getSupplierReference());
        static::assertSame("super full description 2", $product->getFullDescription());
        static::assertSame("super short description 2", $product->getShortDescription());
        static::assertFalse($product->isBrandNew());
        static::assertFalse($product->hasFreeShipping());
        static::assertSame([2, 3], $product->getTaxIds());
        static::assertSame(
            [
                'freeAttr1' => ['freeAttr1Value2'],
                'freeAttr2' => [43],
                'freeAttr3' => ['freeAttr3Value2', 43],
            ],
            $product->getFreeAttributes()
        );
        static::assertSame(0.2, $product->getGreenTax());
        static::assertSame(0.3, $product->getWeight());
        static::assertTrue(ProductStatus::DISABLED()->equals($product->getStatus()));
        static::assertTrue(ProductApprovalStatus::PENDING()->equals($product->getApprovalStatus()));
        static::assertFalse($product->isDownloadable());
        static::assertRegExp('#/images/detailed/0/[^.]+.png#', (string) $product->getMainImage());
        $additionalImages = $product->getAdditionalImages();
        static::assertCount(2, $additionalImages);
        [$additionalImages1, $additionalImages2] = array_values($additionalImages);
        static::assertRegExp('#/images/detailed/0/[^.]+.png#', (string) $additionalImages1);
        static::assertRegExp('#/images/detailed/0/[^.]+.ico#', (string) $additionalImages2);
        static::assertNotEquals((string) $additionalImages1, (string) $additionalImages2);

        $attachments = $product->getAttachments();
        static::assertContainsOnly(ProductAttachment::class, $attachments);
        static::assertCount(1, $attachments);
        static::assertSame('favicon', $attachments[0]->getLabel());
        static::assertNotEmpty($attachments[0]->getId());

        static::assertSame('product', $product->getProductTemplateType());

        // Checking declinations
        $declinations = $product->getDeclinations();
        static::assertContainsOnly(ProductDeclination::class, $declinations);
        static::assertCount(4, $declinations);

        static::assertSame([1 => 1, 2 => 5, 3 => 7], $declinations[0]->getOptionsVariants());
        static::assertSame('code_full_declA', $declinations[0]->getCode());
        static::assertNull($declinations[0]->getAffiliateLink());
        static::assertNull($declinations[0]->getCrossedOutPrice());
        static::assertSame(13, $declinations[0]->getQuantity());
        static::assertSame(3.6, $declinations[0]->getPrice());
        static::assertSame(0, $declinations[0]->getPriceTiers()[0]->getLowerLimit());
        static::assertSame(3.6, $declinations[0]->getPriceTiers()[0]->getPrice());

        // empty declination generated automatically to complete the matrix
        static::assertSame([1 => 1, 2 => 5, 3 => 9], $declinations[1]->getOptionsVariants());
        static::assertSame(0.0, $declinations[1]->getPrice());
        static::assertSame(0, $declinations[1]->getPriceTiers()[0]->getLowerLimit());
        static::assertSame(0.0, $declinations[1]->getPriceTiers()[0]->getPrice());
        static::assertSame(0, $declinations[1]->getQuantity());
        static::assertNull($declinations[1]->getCrossedOutPrice());
        static::assertNull($declinations[1]->getAffiliateLink());
        static::assertNull($declinations[1]->getCode());

        // empty declination generated automatically to complete the matrix
        static::assertSame([1 => 1, 2 => 6, 3 => 7], $declinations[2]->getOptionsVariants());
        static::assertSame(0.0, $declinations[2]->getPrice());
        static::assertSame(0, $declinations[2]->getPriceTiers()[0]->getLowerLimit());
        static::assertSame(0.0, $declinations[2]->getPriceTiers()[0]->getPrice());
        static::assertSame(0, $declinations[2]->getQuantity());
        static::assertNull($declinations[2]->getCrossedOutPrice());
        static::assertNull($declinations[2]->getAffiliateLink());
        static::assertNull($declinations[2]->getCode());

        static::assertSame([1 => 1, 2 => 6, 3 => 9], $declinations[3]->getOptionsVariants());
        static::assertSame(99.99, $declinations[3]->getPrice());
        static::assertSame(0, $declinations[3]->getPriceTiers()[0]->getLowerLimit());
        static::assertSame(99.99, $declinations[3]->getPriceTiers()[0]->getPrice());
        static::assertSame(120, $declinations[3]->getPriceTiers()[1]->getLowerLimit());
        static::assertSame(89.99, $declinations[3]->getPriceTiers()[1]->getPrice());
        static::assertSame(4, $declinations[3]->getQuantity());
        static::assertSame(1000.2, $declinations[3]->getCrossedOutPrice());
        static::assertNull($declinations[3]->getAffiliateLink());
        static::assertNull($declinations[3]->getCode());
    }

    public function testGetProductShipping(): void
    {
        $shipping = $this->buildProductService()->getShipping(5, 38);

        static::assertInstanceOf(Shipping::class, $shipping);
        static::assertSame(20., $shipping->getCarriagePaidThreshold());
    }

    public function testGetProductShippings(): void
    {
        $shippings = $this->buildProductService()->getShippings(5);

        foreach ($shippings as $shipping) {
            static::assertInstanceOf(Shipping::class, $shipping);

            if ($shipping->getId() === 38 || $shipping->getId() === 1) {
                static::assertSame(20., $shipping->getCarriagePaidThreshold());
            } else {
                static::assertNull($shipping->getCarriagePaidThreshold());
            }
        }
    }

    public function testPutProductShipping(): void
    {
        $this->loadAnnotations();

        $command = new UpdateShippingCommand();
        $command->setStatus("D")
                ->setRates(
                    [
                        [
                            'amount' => 0,
                            'value'  => 100,
                        ],
                        [
                            'amount' => 1,
                            'value'  => 50,
                        ],
                    ]
                )
                ->setSpecificRate(false)
                ->setProductId(5);

        $productService = $this->buildProductService();

        $productService->putShipping(1, $command);

        $shipping = $productService->getShipping(5, 1);

        static::assertInstanceOf(Shipping::class, $shipping);
        static::assertSame(100.0, $shipping->getRates()[0]['value']);
    }

    public function testUpdateShippingCommandConstraints(): void
    {
        $this->loadAnnotations();

        $command = new UpdateShippingCommand();
        $command->setStatus("Status qui n'existe pas")
            ->setRates(
                [
                    [
                        'amount' => 0,
                        'value'  => 100,
                    ],
                    [
                        'amount' => 1,
                        'value'  => 50,
                    ],
                ]
            )
            ->setSpecificRate(false)
            ->setProductId(-1);

        $productService = $this->buildProductService();

        $this->expectException(SomeParametersAreInvalid::class);
        $productService->putShipping(1, $command);
    }

    public function testAddVideo(): void
    {
        $data = (new CreateProductCommand())
            ->setCode('code_full_product')
            ->setGreenTax(0.1)
            ->setIsBrandNew(true)
            ->setName("Full product")
            ->setSupplierReference('supplierref_full')
            ->setStatus(ProductStatus::ENABLED())
            ->setMainCategoryId(4)
            ->setFreeAttributes(
                [
                    'freeAttr1' => 'freeAttr1Value',
                    'freeAttr2' => 42,
                    'freeAttr3' => ['freeAttr3Value', 42],
                ]
            )
            ->setHasFreeShipping(true)
            ->setWeight(0.2)
            ->setIsDownloadable(true)
            ->setMainImage(new Uri('https://sandbox.wizaplace.com/api/v1/doc/favicon.png'))
            ->setAdditionalImages(
                [
                    (new ProductImageUpload())->setName('image1.png')
                    ->setMimeType('image/png')
                    ->setBase64Data('iVBORw0KGgoAAAANSUhEUgAAACAAAAAgCAYAAABzenr0AAAFkElEQVRYw72XS2wbVRSGvzszthPHcWYmzYOEvJo0IUlbmqZvWkDiISoVCVUUVapUIVZsWCAhgYQEQqxgBaqExIIVFQtAqCCxQYK2qKQtSUlTtSUNbp2kNImdxLHrOHHGj2Hha9dO3DZ9cSRr5Lln7jn3v/899z+CVVhTSxuADmwCtshnG7BGuswAPuA80A8MAeExv++uc4u7BBZAPbAPeE0G1u/wnQ2EZSLfAj8Bk2N+n33PCTS1tLmB/cDbwEZA494sCQwCnwHHxvy+hVUlIOGuBz4EDgFuHsxiwDfAR8CN5duiFgneCnwhIXfy4OYEeoBO4LRumOFIOJQbVJY5Pw4cAfYWGXsQU+ScRyS6KxFoamkrAz4BXn3IwfO3uw3w6IZ5PBIOJXIJSLYfBN57SLDfKYku4JpumBci4VBupQ2S7W4evbllrAYAVa7+sPwp/D9WBYzqhtmv6oZpAh8DzS6hsMep0+uoYDadIGanbk/tci8Nz7yI0dHNQnCK1FL8tr5ltfU0v/AypZVVLAQnsVNJBfAAP2pAL/AkwC6HzgeetZQIlU6tjE9jo1h2uuhGtj63j+ZXDoIQONweRn44CvbKgqc6nXQcOEzt1qdIJSxSS3GmBvqQMXsVYCtQDlAqFJxCQchkmtXSoisyFQe73FUIVUUoCjVbdlKim0V9PfWNmJ0bQAgUTUNxOLJD5cBWRRYJAXA+EWUitQSAoTjY7qgoOmmP5uX54XFKY4s5iM2O7qK+1T3bcXq8ACwEA8z5hvOB7FGA9uybqbTF2UQkN/q008ArCq8ADcFup07DRJBG/3imymgaNb0781cHgKPMw5r1PSAyFX96qJ/47HS+S7sClGX/pbE5boWYl+Rbp7rp1jwFk9apLjY5ytESSZ64MIyayvianRvw1DUU+Fasbcfb2JK5meKLBAf/xE4XcKpsxbG7nIxxKTkPQIlQ2O3UUfPurB0OnWolU6vMv6/A1CQALq+eWW0WX6FQs3kHqqsEgIj/H8LXRorW6Fj+i0U7xUlrjhR2LmCt6soltMupo8iE/pwZ5+r5s9mI1PTuxFGWQcxlmFR2bcyIBDtNYKCP5OKKGzmmACvSOm2FuSHJWKs66dUyJGpT3XRpZTLRNL8vhZjoP4UVvQmAt7EFvbUDgMqujbhrHgMgHppl5uJgMY6OKFI0FBzgQB4ZVQTPOg1Khcoep0G5JOWVZIzLyRg3x0eZG7mU8XWVUN2zDdXpoqZnO4qa8Z25OEgsMFlMPQ0qUsNF80fS2PxmzRKVZFzv8LDF4WWbowIhvzxphYjaSdIJi6n+PtLJZKbGbuhlzfpN6O1dAKSsJQIDfdipFVU1CvQrwDkpIgtsOI+MXqHxemkda2Vhmk5bnJEIAcxcGmR+4joApVXVrNt/CFeFkYlyfZSw70ox+IeAcwowJwVkqpCMaU5YoRwZuzUPLpE5NP2JCNdTt2r/UiTM9FA/2DZCUfE2tSKEANsmMHAaaz66PHhKxpxTpGI9VgyFU9YcI8lC5s7bKX5ZmiWZTxvbZqLvBIszwQLfheAkgb/OLKdYdvXHxvw+WwXQDTMqj+NLQK6cLdhpgmmLNs1NqVCZsxMcjU/yax4yWUtEo1jzN/HUNaCoGrHABCPff50jaH5ewPvAH5Fw6FaFkTL8c+CNfF0ggCrFSa3iImInuZGKF64+/5YUApdRSYlhEg/NEg+Hlt+QaeBL4J2sTBfLVHG9dNj7CMRJGvgZeHPM75soKssj4VBUN8w+YJ0UkOIhB39rzO/797Z9gUwirBvmSanduh6CSI0BXwHvLg9eNIE8JE7IhrMRqL6PLUkCA5JwR8b8vtD9Nqd1sjk9AGxeRXM6J8v7d7I5nbqv5rRIy1YhdVy2PW+VyAAEgKvyfA/IZ2Q17fl/LFcBJAQMmYcAAAAASUVORK5CYII='),
                    new Uri('https://sandbox.wizaplace.com/api/v1/doc/favicon.png'),
                ]
            )
            ->setFullDescription("super full description")
            ->setShortDescription("super short description")
            ->setTaxIds([1, 2])
            ->setDeclinations(
                [
                    (new ProductDeclinationUpsertData([1 => 1, 2 => 5, 3 => 7]))
                    ->setCode('code_full_declA')
                    ->setPrice(3.5)
                    ->setQuantity(12)
                    ->setInfiniteStock(true),
                    (new ProductDeclinationUpsertData([1 => 1, 2 => 6, 3 => 9]))
                    ->setPrice(100.0)
                    ->setCrossedOutPrice(1000.0)
                    ->setQuantity(3)
                    ->setInfiniteStock(true),
                ]
            );
        $productService = $this->buildProductService('vendor@wizaplace.com');
        $productId = $productService->createProduct($data);

        $video = $productService->addVideo($productId, 'http://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ForBiggerEscapes.mp4');

        static::assertTrue(\is_array($video));
        static::assertRegExp(
            '~^[a-zA-Z0-9]{8}(-[a-zA-Z0-9]{4}){4}[a-zA-Z0-9]{8}$~',
            $video['id']
        );
    }

    public function testDeleteVideo(): void
    {
        $service = $this->buildProductService();
        $product = $service->getProductById(1);
        $service->addVideo($product->getId(), 'http://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ForBiggerEscapes.mp4');

        $video = $service->deleteVideo($product->getId());

        static::assertEquals(204, $video->getStatusCode());
    }

    public function testGetVideo(): void
    {
        static::assertEquals(
            '//s3-eu-west-1.amazonaws.com/videos/414375b2-61cb-4260-b82b-4a2636cb5673/480.mp4',
            $this->buildProductService()->getProductById(3)->getVideo()
        );
    }

    public function testUpdateProductFromEan(): void
    {
        $ean = "My_EAN";

        $service = $this->buildProductService("vendor@wizaplace.com");

        $product1Id = $service->createProduct(
            (new CreateProductCommand())
                ->setCode("code_1")
                ->setGreenTax(0.1)
                ->setIsBrandNew(true)
                ->setName("Full product")
                ->setSupplierReference('supplierref_full')
                ->setStatus(ProductStatus::ENABLED())
                ->setMainCategoryId(4)
                ->setFreeAttributes(
                    [
                        'freeAttr1' => 'freeAttr1Value',
                        'freeAttr2' => 42,
                        'freeAttr3' => ['freeAttr3Value', 42],
                    ]
                )
                ->setHasFreeShipping(true)
                ->setWeight(0.2)
                ->setIsDownloadable(true)
                ->setMainImage(new Uri('https://sandbox.wizaplace.com/api/v1/doc/favicon.png'))
                ->setAdditionalImages(
                    [
                        (new ProductImageUpload())->setName('image1.png')
                        ->setMimeType('image/png')
                        ->setBase64Data('iVBORw0KGgoAAAANSUhEUgAAACAAAAAgCAYAAABzenr0AAAFkElEQVRYw72XS2wbVRSGvzszthPHcWYmzYOEvJo0IUlbmqZvWkDiISoVCVUUVapUIVZsWCAhgYQEQqxgBaqExIIVFQtAqCCxQYK2qKQtSUlTtSUNbp2kNImdxLHrOHHGj2Hha9dO3DZ9cSRr5Lln7jn3v/899z+CVVhTSxuADmwCtshnG7BGuswAPuA80A8MAeExv++uc4u7BBZAPbAPeE0G1u/wnQ2EZSLfAj8Bk2N+n33PCTS1tLmB/cDbwEZA494sCQwCnwHHxvy+hVUlIOGuBz4EDgFuHsxiwDfAR8CN5duiFgneCnwhIXfy4OYEeoBO4LRumOFIOJQbVJY5Pw4cAfYWGXsQU+ScRyS6KxFoamkrAz4BXn3IwfO3uw3w6IZ5PBIOJXIJSLYfBN57SLDfKYku4JpumBci4VBupQ2S7W4evbllrAYAVa7+sPwp/D9WBYzqhtmv6oZpAh8DzS6hsMep0+uoYDadIGanbk/tci8Nz7yI0dHNQnCK1FL8tr5ltfU0v/AypZVVLAQnsVNJBfAAP2pAL/AkwC6HzgeetZQIlU6tjE9jo1h2uuhGtj63j+ZXDoIQONweRn44CvbKgqc6nXQcOEzt1qdIJSxSS3GmBvqQMXsVYCtQDlAqFJxCQchkmtXSoisyFQe73FUIVUUoCjVbdlKim0V9PfWNmJ0bQAgUTUNxOLJD5cBWRRYJAXA+EWUitQSAoTjY7qgoOmmP5uX54XFKY4s5iM2O7qK+1T3bcXq8ACwEA8z5hvOB7FGA9uybqbTF2UQkN/q008ArCq8ADcFup07DRJBG/3imymgaNb0781cHgKPMw5r1PSAyFX96qJ/47HS+S7sClGX/pbE5boWYl+Rbp7rp1jwFk9apLjY5ytESSZ64MIyayvianRvw1DUU+Fasbcfb2JK5meKLBAf/xE4XcKpsxbG7nIxxKTkPQIlQ2O3UUfPurB0OnWolU6vMv6/A1CQALq+eWW0WX6FQs3kHqqsEgIj/H8LXRorW6Fj+i0U7xUlrjhR2LmCt6soltMupo8iE/pwZ5+r5s9mI1PTuxFGWQcxlmFR2bcyIBDtNYKCP5OKKGzmmACvSOm2FuSHJWKs66dUyJGpT3XRpZTLRNL8vhZjoP4UVvQmAt7EFvbUDgMqujbhrHgMgHppl5uJgMY6OKFI0FBzgQB4ZVQTPOg1Khcoep0G5JOWVZIzLyRg3x0eZG7mU8XWVUN2zDdXpoqZnO4qa8Z25OEgsMFlMPQ0qUsNF80fS2PxmzRKVZFzv8LDF4WWbowIhvzxphYjaSdIJi6n+PtLJZKbGbuhlzfpN6O1dAKSsJQIDfdipFVU1CvQrwDkpIgtsOI+MXqHxemkda2Vhmk5bnJEIAcxcGmR+4joApVXVrNt/CFeFkYlyfZSw70ox+IeAcwowJwVkqpCMaU5YoRwZuzUPLpE5NP2JCNdTt2r/UiTM9FA/2DZCUfE2tSKEANsmMHAaaz66PHhKxpxTpGI9VgyFU9YcI8lC5s7bKX5ZmiWZTxvbZqLvBIszwQLfheAkgb/OLKdYdvXHxvw+WwXQDTMqj+NLQK6cLdhpgmmLNs1NqVCZsxMcjU/yax4yWUtEo1jzN/HUNaCoGrHABCPff50jaH5ewPvAH5Fw6FaFkTL8c+CNfF0ggCrFSa3iImInuZGKF64+/5YUApdRSYlhEg/NEg+Hlt+QaeBL4J2sTBfLVHG9dNj7CMRJGvgZeHPM75soKssj4VBUN8w+YJ0UkOIhB39rzO/797Z9gUwirBvmSanduh6CSI0BXwHvLg9eNIE8JE7IhrMRqL6PLUkCA5JwR8b8vtD9Nqd1sjk9AGxeRXM6J8v7d7I5nbqv5rRIy1YhdVy2PW+VyAAEgKvyfA/IZ2Q17fl/LFcBJAQMmYcAAAAASUVORK5CYII='),
                        new Uri('https://sandbox.wizaplace.com/api/v1/doc/favicon.png'),
                    ]
                )
                ->setFullDescription("super full description")
                ->setShortDescription("super short description")
                ->setTaxIds([1, 2])
                ->setDeclinations(
                    [
                        (new ProductDeclinationUpsertData([1 => 1, 2 => 5, 3 => 7]))
                        ->setCode($ean)
                        ->setPrice(3.5)
                        ->setQuantity(10)
                        ->setInfiniteStock(true),
                    ]
                )
        );
        $product2Id = $service->createProduct(
            (new CreateProductCommand())
                ->setCode("code_2")
                ->setGreenTax(0.1)
                ->setIsBrandNew(true)
                ->setName("Full product")
                ->setSupplierReference('supplierref_full')
                ->setStatus(ProductStatus::ENABLED())
                ->setMainCategoryId(4)
                ->setFreeAttributes(
                    [
                        'freeAttr1' => 'freeAttr1Value',
                        'freeAttr2' => 42,
                        'freeAttr3' => ['freeAttr3Value', 42],
                    ]
                )
                ->setHasFreeShipping(true)
                ->setWeight(0.2)
                ->setIsDownloadable(true)
                ->setMainImage(new Uri('https://sandbox.wizaplace.com/api/v1/doc/favicon.png'))
                ->setAdditionalImages(
                    [
                        (new ProductImageUpload())->setName('image1.png')
                        ->setMimeType('image/png')
                        ->setBase64Data('iVBORw0KGgoAAAANSUhEUgAAACAAAAAgCAYAAABzenr0AAAFkElEQVRYw72XS2wbVRSGvzszthPHcWYmzYOEvJo0IUlbmqZvWkDiISoVCVUUVapUIVZsWCAhgYQEQqxgBaqExIIVFQtAqCCxQYK2qKQtSUlTtSUNbp2kNImdxLHrOHHGj2Hha9dO3DZ9cSRr5Lln7jn3v/899z+CVVhTSxuADmwCtshnG7BGuswAPuA80A8MAeExv++uc4u7BBZAPbAPeE0G1u/wnQ2EZSLfAj8Bk2N+n33PCTS1tLmB/cDbwEZA494sCQwCnwHHxvy+hVUlIOGuBz4EDgFuHsxiwDfAR8CN5duiFgneCnwhIXfy4OYEeoBO4LRumOFIOJQbVJY5Pw4cAfYWGXsQU+ScRyS6KxFoamkrAz4BXn3IwfO3uw3w6IZ5PBIOJXIJSLYfBN57SLDfKYku4JpumBci4VBupQ2S7W4evbllrAYAVa7+sPwp/D9WBYzqhtmv6oZpAh8DzS6hsMep0+uoYDadIGanbk/tci8Nz7yI0dHNQnCK1FL8tr5ltfU0v/AypZVVLAQnsVNJBfAAP2pAL/AkwC6HzgeetZQIlU6tjE9jo1h2uuhGtj63j+ZXDoIQONweRn44CvbKgqc6nXQcOEzt1qdIJSxSS3GmBvqQMXsVYCtQDlAqFJxCQchkmtXSoisyFQe73FUIVUUoCjVbdlKim0V9PfWNmJ0bQAgUTUNxOLJD5cBWRRYJAXA+EWUitQSAoTjY7qgoOmmP5uX54XFKY4s5iM2O7qK+1T3bcXq8ACwEA8z5hvOB7FGA9uybqbTF2UQkN/q008ArCq8ADcFup07DRJBG/3imymgaNb0781cHgKPMw5r1PSAyFX96qJ/47HS+S7sClGX/pbE5boWYl+Rbp7rp1jwFk9apLjY5ytESSZ64MIyayvianRvw1DUU+Fasbcfb2JK5meKLBAf/xE4XcKpsxbG7nIxxKTkPQIlQ2O3UUfPurB0OnWolU6vMv6/A1CQALq+eWW0WX6FQs3kHqqsEgIj/H8LXRorW6Fj+i0U7xUlrjhR2LmCt6soltMupo8iE/pwZ5+r5s9mI1PTuxFGWQcxlmFR2bcyIBDtNYKCP5OKKGzmmACvSOm2FuSHJWKs66dUyJGpT3XRpZTLRNL8vhZjoP4UVvQmAt7EFvbUDgMqujbhrHgMgHppl5uJgMY6OKFI0FBzgQB4ZVQTPOg1Khcoep0G5JOWVZIzLyRg3x0eZG7mU8XWVUN2zDdXpoqZnO4qa8Z25OEgsMFlMPQ0qUsNF80fS2PxmzRKVZFzv8LDF4WWbowIhvzxphYjaSdIJi6n+PtLJZKbGbuhlzfpN6O1dAKSsJQIDfdipFVU1CvQrwDkpIgtsOI+MXqHxemkda2Vhmk5bnJEIAcxcGmR+4joApVXVrNt/CFeFkYlyfZSw70ox+IeAcwowJwVkqpCMaU5YoRwZuzUPLpE5NP2JCNdTt2r/UiTM9FA/2DZCUfE2tSKEANsmMHAaaz66PHhKxpxTpGI9VgyFU9YcI8lC5s7bKX5ZmiWZTxvbZqLvBIszwQLfheAkgb/OLKdYdvXHxvw+WwXQDTMqj+NLQK6cLdhpgmmLNs1NqVCZsxMcjU/yax4yWUtEo1jzN/HUNaCoGrHABCPff50jaH5ewPvAH5Fw6FaFkTL8c+CNfF0ggCrFSa3iImInuZGKF64+/5YUApdRSYlhEg/NEg+Hlt+QaeBL4J2sTBfLVHG9dNj7CMRJGvgZeHPM75soKssj4VBUN8w+YJ0UkOIhB39rzO/797Z9gUwirBvmSanduh6CSI0BXwHvLg9eNIE8JE7IhrMRqL6PLUkCA5JwR8b8vtD9Nqd1sjk9AGxeRXM6J8v7d7I5nbqv5rRIy1YhdVy2PW+VyAAEgKvyfA/IZ2Q17fl/LFcBJAQMmYcAAAAASUVORK5CYII='),
                        new Uri('https://sandbox.wizaplace.com/api/v1/doc/favicon.png'),
                    ]
                )
                ->setFullDescription("super full description")
                ->setShortDescription("super short description")
                ->setTaxIds([1, 2])
                ->setDeclinations(
                    [
                        (new ProductDeclinationUpsertData([1 => 1, 2 => 5, 3 => 7]))
                        ->setCode($ean)
                        ->setPrice(3.5)
                        ->setQuantity(15)
                        ->setInfiniteStock(true),
                    ]
                )
        );

        $response = $service->updateStock($ean, 5);
        static::assertSame("2 entities updated.", $response);

        $product1 = $service->getProductById($product1Id);
        static::assertSame(5, $product1->getDeclinations()[0]->getQuantity());

        $product2 = $service->getProductById($product2Id);
        static::assertSame(5, $product2->getDeclinations()[0]->getQuantity());
    }

    public function testUpdateProductFromEanWithCompanyIds(): void
    {
        $ean = "My_EAN";

        $serviceVendor1 = $this->buildProductService("vendor@wizaplace.com");
        $serviceVendor2 = $this->buildProductService("vendor@world-company.com");
        $serviceAdmin = $this->buildProductService("admin@wizaplace.com", "Windows.98");

        $product1Id = $serviceVendor1->createProduct(
            (new CreateProductCommand())
                ->setCode("code_1")
                ->setGreenTax(0.1)
                ->setIsBrandNew(true)
                ->setName("Full product")
                ->setSupplierReference('supplierref_full')
                ->setStatus(ProductStatus::ENABLED())
                ->setMainCategoryId(4)
                ->setFreeAttributes(
                    [
                        'freeAttr1' => 'freeAttr1Value',
                        'freeAttr2' => 42,
                        'freeAttr3' => ['freeAttr3Value', 42],
                    ]
                )
                ->setHasFreeShipping(true)
                ->setWeight(0.2)
                ->setIsDownloadable(true)
                ->setMainImage(new Uri('https://sandbox.wizaplace.com/api/v1/doc/favicon.png'))
                ->setAdditionalImages(
                    [
                        $this->getSampleImage(),
                        new Uri('https://sandbox.wizaplace.com/api/v1/doc/favicon.png'),
                    ]
                )
                ->setFullDescription("super full description")
                ->setShortDescription("super short description")
                ->setTaxIds([1])
                ->setDeclinations(
                    [
                        (new ProductDeclinationUpsertData([5 => 40]))
                            ->setCode($ean)
                            ->setPrice(10)
                            ->setQuantity(10)
                            ->setInfiniteStock(true),
                    ]
                )
        );

        $product2Id = $serviceVendor2->createProduct(
            (new CreateProductCommand())
                ->setCode("code_2")
                ->setGreenTax(0.1)
                ->setIsBrandNew(true)
                ->setName("Full product")
                ->setSupplierReference('supplierref_full')
                ->setStatus(ProductStatus::ENABLED())
                ->setMainCategoryId(4)
                ->setFreeAttributes(
                    [
                        'freeAttr1' => 'freeAttr1Value',
                        'freeAttr2' => 42,
                        'freeAttr3' => ['freeAttr3Value', 42],
                    ]
                )
                ->setHasFreeShipping(true)
                ->setWeight(0.2)
                ->setIsDownloadable(true)
                ->setMainImage(new Uri('https://sandbox.wizaplace.com/api/v1/doc/favicon.png'))
                ->setAdditionalImages(
                    [
                        $this->getSampleImage(),
                        new Uri('https://sandbox.wizaplace.com/api/v1/doc/favicon.png'),
                    ]
                )
                ->setFullDescription("super full description")
                ->setShortDescription("super short description")
                ->setTaxIds([1])
                ->setDeclinations(
                    [
                        (new ProductDeclinationUpsertData([5 => 40]))
                            ->setCode($ean)
                            ->setPrice(10)
                            ->setQuantity(10)
                            ->setInfiniteStock(true),
                    ]
                )
        );

        //Update stock for company_ids = [3]
        $response = $serviceAdmin->updateStock($ean, 5, [3]);
        static::assertSame("1 entities updated.", $response);

        //Amount not changed for product with companyId = 2
        $product1 = $serviceAdmin->getProductById($product1Id);
        static::assertSame(2, $product1->getCompanyId());
        static::assertSame(10, $product1->getDeclinations()[0]->getQuantity());

        //Amount changed for product with companyId = 3
        $product2 = $serviceAdmin->getProductById($product2Id);
        static::assertSame(3, $product2->getCompanyId());
        static::assertSame(5, $product2->getDeclinations()[0]->getQuantity());

        //Update stock with empty company_ids
        $response = $serviceAdmin->updateStock($ean, 15, []);
        static::assertSame("2 entities updated.", $response);

        //Amount changed for product with companyId = 2
        $product1 = $serviceAdmin->getProductById($product1Id);
        static::assertSame(2, $product1->getCompanyId());
        static::assertSame(15, $product1->getDeclinations()[0]->getQuantity());

        //Amount changed for product with companyId = 3
        $product2 = $serviceAdmin->getProductById($product2Id);
        static::assertSame(3, $product2->getCompanyId());
        static::assertSame(15, $product2->getDeclinations()[0]->getQuantity());
    }

    public function testUpdateProductFromEanWithCompanyIdsByVendor(): void
    {
        $serviceVendor = $this->buildProductService("vendor@wizaplace.com");

        static::expectExceptionMessage('You are not allowed to perfom this action, please check all you fields.');
        static::expectExceptionCode(403);
        $serviceVendor->updateStock('test', 5, [3]);
    }

    public function testUploadAttachments(): void
    {
        $service = $this->buildProductService();

        $uuids = $service->addAttachments(
            1,
            [
                $this->mockUploadedFile("minimal.pdf"),
                $this->mockUploadedFile("video.avi"),
            ],
            [
                'http://wizaplace.test/tests/data/misc/logo-URL.jpg',
                'http://wizaplace.test/tests/data/misc/boitier_test.jpg',
            ]
        );

        static::assertCount(4, $uuids);
        foreach ($uuids as $uuid) {
            static::assertRegExp("/[0-9a-fA-F]{8}\-[0-9a-fA-F]{4}\-[0-9a-fA-F]{4}\-[0-9a-fA-F]{4}\-[0-9a-fA-F]{12}/", $uuid);
        }
    }

    public function testUploadAndRemoveAttachments(): void
    {
        $service = $this->buildProductService();

        $uuids = $service->addAttachments(
            1,
            [
                $this->mockUploadedFile("minimal.pdf"),
                $this->mockUploadedFile("video.avi"),
            ],
            [
                'http://wizaplace.test/tests/data/misc/logo-URL.jpg',
                'http://wizaplace.test/tests/data/misc/boitier_test.jpg',
            ]
        );

        static::assertCount(4, $uuids);
        foreach ($uuids as $uuid) {
            static::assertRegExp("/[0-9a-fA-F]{8}\-[0-9a-fA-F]{4}\-[0-9a-fA-F]{4}\-[0-9a-fA-F]{4}\-[0-9a-fA-F]{12}/", $uuid);

            $attachment = $service->removeAttachment(1, $uuid);
            static::assertEquals(204, $attachment->getStatusCode());
        }
    }

    public function testFindProductByApprovalStatus()
    {
        $filter = (new ProductListFilter())->byApprovalStatus(new ProductApprovalStatus('Y'));
        $products = $this->buildProductService()->listProducts($filter);
        $products = $products->getProducts();

        foreach ($products as $product) {
            static::assertInstanceOf(ProductSummary::class, $product);
            static::assertTrue(ProductApprovalStatus::APPROVED()->equals($product->getApprovalStatus()));
        }
    }

    public function testFindProductByupdatedBefore()
    {
        $filter = (new ProductListFilter())->byUpdatedBefore('09-07-2019');
        $products = $this->buildProductService()->listProducts($filter);
        $products = $products->getProducts();

        $dateTimeRef = new \DateTime('09-07-2019');
        foreach ($products as $product) {
            static::assertLessThan($dateTimeRef->getTimestamp(), $product->getLastUpdateAt()->getTimestamp());
        }
    }

    public function testFindProductByupdatedAfter()
    {
        $filter = (new ProductListFilter())->byUpdatedAfter('01-01-2010');
        $products = $this->buildProductService()->listProducts($filter);
        $products = $products->getProducts();

        $dateTimeRef = new \DateTime('09-07-2019');
        foreach ($products as $product) {
            static::assertGreaterThan($product->getLastUpdateAt()->getTimestamp(), $dateTimeRef->getTimestamp());
        }
    }

    public function testGetProductWithPriceTiers(): void
    {
        $product = $this->buildProductService()->getProductById(2);

        static::assertInstanceOf(Product::class, $product);
        static::assertSame(2, $product->getId());
        static::assertSame(0, $product->getDeclinations()[0]->getPriceTiers()[0]->getLowerLimit());
        static::assertSame(15.5, $product->getDeclinations()[0]->getPriceTiers()[0]->getPrice());
    }

    // Subscription Feature flag is supposed to be activated
    public function testCreateAndUpdateProductWithSubscription(): void
    {
        $service = $this->buildProductService("vendor@wizaplace.com", "Windows.98");

        $id = $service->createProduct(
            (new CreateProductCommand())
                ->setCode("product_with_sub_update1")
                ->setSupplierReference('product_with_sub_ref')
                ->setName("Product with subscription")
                ->setMainCategoryId(1)
                ->setGreenTax(0.)
                ->setTaxIds([1])
                ->setDeclinations(
                    [
                        (new ProductDeclinationUpsertData([]))
                        ->setCode('product_with_sub')
                        ->setPrice(3.5)
                        ->setQuantity(12)
                        ->setInfiniteStock(false),
                    ]
                )
                ->setStatus(ProductStatus::ENABLED())
                ->setIsBrandNew(true)
                ->setWeight(0.)
                ->setFullDescription("Product with subscription full description")
                ->setShortDescription("Product with subscription short description")
                ->setIsSubscription(true)
                ->setIsRenewable(true)
        );

        static::assertTrue(\is_int($id));

        $product = $service->getProductById($id);

        static::assertTrue($product->isSubscription());
        static::assertTrue($product->isRenewable());

        $service->updateProduct(
            (new UpdateProductCommand($id))
                ->setIsSubscription(false)
                ->setIsRenewable(false)
        );
        $product = $service->getProductById($id);

        static::assertFalse($product->isSubscription());
        static::assertFalse($product->isRenewable());
    }

    public function testCreateProductServiceTemplate(): void
    {
        $service = $this->buildProductService('vendor@wizaplace.com');

        $productId = $service->createProduct(
            (new CreateProductCommand())
                ->setName("Service 1")
                ->setCode("REFSUP32202")
                ->setFullDescription("en ligne service ")
                ->setShortDescription("en ligne service")
                ->setGreenTax(0)
                ->setStatus(ProductStatus::ENABLED())
                ->setMainCategoryId(4)
                ->setIsBrandNew(true)
                ->setTaxIds([1])
                ->setWeight(1.0)
                ->setInfiniteStock(true)
                ->setSupplierReference("REFSEPTMA3222")
                ->setProductTemplateType('service')
                ->setDeclinations(
                    [
                        (new ProductDeclinationUpsertData([1 => 1, 2 => 5, 3 => 7]))
                            ->setCode('code_full_declD')
                            ->setPrice(3.5)
                            ->setInfiniteStock(true),
                        (new ProductDeclinationUpsertData([1 => 1, 2 => 6, 3 => 9]))
                            ->setPrice(100.0)
                            ->setCrossedOutPrice(1000.0)
                            ->setQuantity(1)
                    ]
                )
        );

        static::assertInternalType('int', $productId);
        static::assertGreaterThan(0, $productId);
    }

    public function testGetAttachments(): void
    {
        $data = (new CreateProductCommand())
            ->setCode("code_full_A_11")
            ->setGreenTax(0.1)
            ->setIsBrandNew(true)
            ->setName("Full product")
            ->setSupplierReference('supplierref_full')
            ->setStatus(ProductStatus::ENABLED())
            ->setMainCategoryId(4)
            ->setFreeAttributes(
                [
                    'freeAttr1' => 'freeAttr1Value',
                    'freeAttr2' => 42,
                    'freeAttr3' => ['freeAttr3Value', 42],
                ]
            )
            ->setHasFreeShipping(true)
            ->setWeight(0.2)
            ->setIsDownloadable(true)
            ->setMainImage(new Uri('https://www.google.com/favicon.ico'))
            ->setAdditionalImages(
                [
                    (new ProductImageUpload())->setName('image1.png')
                        ->setMimeType('image/png')
                        ->setBase64Data('iVBORw0KGgoAAAANSUhEUgAAACAAAAAgCAYAAABzenr0AAAFkElEQVRYw72XS2wbVRSGvzszthPHcWYmzYOEvJo0IUlbmqZvWkDiISoVCVUUVapUIVZsWCAhgYQEQqxgBaqExIIVFQtAqCCxQYK2qKQtSUlTtSUNbp2kNImdxLHrOHHGj2Hha9dO3DZ9cSRr5Lln7jn3v/899z+CVVhTSxuADmwCtshnG7BGuswAPuA80A8MAeExv++uc4u7BBZAPbAPeE0G1u/wnQ2EZSLfAj8Bk2N+n33PCTS1tLmB/cDbwEZA494sCQwCnwHHxvy+hVUlIOGuBz4EDgFuHsxiwDfAR8CN5duiFgneCnwhIXfy4OYEeoBO4LRumOFIOJQbVJY5Pw4cAfYWGXsQU+ScRyS6KxFoamkrAz4BXn3IwfO3uw3w6IZ5PBIOJXIJSLYfBN57SLDfKYku4JpumBci4VBupQ2S7W4evbllrAYAVa7+sPwp/D9WBYzqhtmv6oZpAh8DzS6hsMep0+uoYDadIGanbk/tci8Nz7yI0dHNQnCK1FL8tr5ltfU0v/AypZVVLAQnsVNJBfAAP2pAL/AkwC6HzgeetZQIlU6tjE9jo1h2uuhGtj63j+ZXDoIQONweRn44CvbKgqc6nXQcOEzt1qdIJSxSS3GmBvqQMXsVYCtQDlAqFJxCQchkmtXSoisyFQe73FUIVUUoCjVbdlKim0V9PfWNmJ0bQAgUTUNxOLJD5cBWRRYJAXA+EWUitQSAoTjY7qgoOmmP5uX54XFKY4s5iM2O7qK+1T3bcXq8ACwEA8z5hvOB7FGA9uybqbTF2UQkN/q008ArCq8ADcFup07DRJBG/3imymgaNb0781cHgKPMw5r1PSAyFX96qJ/47HS+S7sClGX/pbE5boWYl+Rbp7rp1jwFk9apLjY5ytESSZ64MIyayvianRvw1DUU+Fasbcfb2JK5meKLBAf/xE4XcKpsxbG7nIxxKTkPQIlQ2O3UUfPurB0OnWolU6vMv6/A1CQALq+eWW0WX6FQs3kHqqsEgIj/H8LXRorW6Fj+i0U7xUlrjhR2LmCt6soltMupo8iE/pwZ5+r5s9mI1PTuxFGWQcxlmFR2bcyIBDtNYKCP5OKKGzmmACvSOm2FuSHJWKs66dUyJGpT3XRpZTLRNL8vhZjoP4UVvQmAt7EFvbUDgMqujbhrHgMgHppl5uJgMY6OKFI0FBzgQB4ZVQTPOg1Khcoep0G5JOWVZIzLyRg3x0eZG7mU8XWVUN2zDdXpoqZnO4qa8Z25OEgsMFlMPQ0qUsNF80fS2PxmzRKVZFzv8LDF4WWbowIhvzxphYjaSdIJi6n+PtLJZKbGbuhlzfpN6O1dAKSsJQIDfdipFVU1CvQrwDkpIgtsOI+MXqHxemkda2Vhmk5bnJEIAcxcGmR+4joApVXVrNt/CFeFkYlyfZSw70ox+IeAcwowJwVkqpCMaU5YoRwZuzUPLpE5NP2JCNdTt2r/UiTM9FA/2DZCUfE2tSKEANsmMHAaaz66PHhKxpxTpGI9VgyFU9YcI8lC5s7bKX5ZmiWZTxvbZqLvBIszwQLfheAkgb/OLKdYdvXHxvw+WwXQDTMqj+NLQK6cLdhpgmmLNs1NqVCZsxMcjU/yax4yWUtEo1jzN/HUNaCoGrHABCPff50jaH5ewPvAH5Fw6FaFkTL8c+CNfF0ggCrFSa3iImInuZGKF64+/5YUApdRSYlhEg/NEg+Hlt+QaeBL4J2sTBfLVHG9dNj7CMRJGvgZeHPM75soKssj4VBUN8w+YJ0UkOIhB39rzO/797Z9gUwirBvmSanduh6CSI0BXwHvLg9eNIE8JE7IhrMRqL6PLUkCA5JwR8b8vtD9Nqd1sjk9AGxeRXM6J8v7d7I5nbqv5rRIy1YhdVy2PW+VyAAEgKvyfA/IZ2Q17fl/LFcBJAQMmYcAAAAASUVORK5CYII='),
                    new Uri('https://www.google.com/favicon.ico'),
                ]
            )
            ->setFullDescription("super full description")
            ->setShortDescription("super short description")
            ->setTaxIds([1, 2])
            ->setDeclinations(
                [
                    (new ProductDeclinationUpsertData([2 => 5, 3 => 7]))
                        ->setCode('code_full_declA_1')
                        ->setPrice(3.5)
                        ->setQuantity(12)
                        ->setSupplierReference('SUPP_REF_02')
                        ->setPriceTiers(
                            [
                                [
                                    'lowerLimit' => 0,
                                    'price' => 2.7,
                                ],
                                [
                                    'lowerLimit' => 50,
                                    'price' => 2.5,
                                ],
                            ]
                        ),
                    (new ProductDeclinationUpsertData([2 => 6, 3 => 9]))
                        ->setPrice(100.0)
                        ->setCrossedOutPrice(1000.0)
                        ->setQuantity(3),
                ]
            )
            ->setAttachments([new ProductAttachmentUpload('favicon', 'https://www.google.com/favicon.ico')])
            ->setProductTemplateType('product');

        $productService = $this->buildProductService('vendor@wizaplace.com');
        $productId = $productService->createProduct($data);
        $product = $productService->getProductById($productId);
        $attachments = $product->getAttachments();
        static::assertContainsOnly(ProductAttachment::class, $attachments);
        static::assertCount(1, $attachments);
        static::assertSame('favicon', $attachments[0]->getLabel());
        static::assertSame('https://www.google.com/favicon.ico', $attachments[0]->getOriginalUrl());
        static::assertSame('/var/attachments/40/favicon.ico', $attachments[0]->getPublicUrl());
        static::assertNotEmpty($attachments[0]->getId());
    }

    public function testGetSeoInformation(): void
    {
        $data = $this->postProductData();

        $productService = $this->buildProductService('vendor@wizaplace.com', 'Windows.98');
        $productId = $productService->createProduct($data);

        $dataUpdate = (new UpdateProductCommand($productId))
            ->setSlug('slug')
            ->setSeoTitle('Seo Title')
            ->setSeoDescription('Seo Description')
            ->setSeoKeywords('Seo Keywords');

        $productService = $this->buildProductService('admin@wizaplace.com', 'Windows.98');

        $newProductId = $productService->updateProduct($dataUpdate);
        static::assertSame($productId, $newProductId);

        $product = $productService->getProductById($productId);
        static::assertSame('slug', $product->getSlug());
        static::assertSame('Seo Title', $product->getSeoTitle());
        static::assertSame('Seo Description', $product->getSeoDescription());
        static::assertSame('Seo Keywords', $product->getSeoKeywords());
    }

    private function buildProductService($userEmail = 'admin@wizaplace.com', $userPassword = 'password'): ProductService
    {
        $apiClient = $this->buildApiClient();
        $apiClient->authenticate($userEmail, $userPassword);

        return new ProductService($apiClient);
    }

    private function loadAnnotations(): void
    {
        /** @var ClassLoader $loader */
        $loader = require __DIR__ . '/../../../vendor/autoload.php';
        AnnotationRegistry::registerLoader([$loader, 'loadClass']);
    }

    private function postProductData(): CreateProductCommand
    {
        return (new CreateProductCommand())
            ->setCode("code_full_A_111")
            ->setGreenTax(0.1)
            ->setIsBrandNew(true)
            ->setName("Full product")
            ->setSupplierReference('supplierref_full')
            ->setStatus(ProductStatus::ENABLED())
            ->setMainCategoryId(4)
            ->setFullDescription("super full description")
            ->setShortDescription("super short description")
            ->setTaxIds([1, 2])
            ->setProductTemplateType('product')
            ->setWeight(0.2)
            ->setDeclinations(
                [
                    (new ProductDeclinationUpsertData([2 => 5, 3 => 7]))
                        ->setCode('code_full_declA_1')
                        ->setPrice(3.5)
                        ->setQuantity(12)
                        ->setSupplierReference('SUPP_REF_02')
                        ->setPriceTiers(
                            [
                                [
                                    'lowerLimit' => 0,
                                    'price' => 2.7,
                                ],
                                [
                                    'lowerLimit' => 50,
                                    'price' => 2.5,
                                ],
                            ]
                        ),
                    (new ProductDeclinationUpsertData([2 => 6, 3 => 9]))
                        ->setPrice(100.0)
                        ->setCrossedOutPrice(1000.0)
                        ->setQuantity(3),
                ]
            );
    }

    public function testListProductWithRelatedProducts(): void
    {
        $filter = (new ProductListFilter())->byIds([1]);

        $products = $this->buildProductService()
            ->listProducts($filter)
            ->getProducts();

        static::assertContainsOnly(ProductSummary::class, $products);
        static::assertCount(1, $products);

        $relatedProducts = $products[0]->getRelated();
        static::assertContainsOnly(RelatedProduct::class, $relatedProducts);
        static::assertCount(9, $relatedProducts);

        $relatedProduct = $relatedProducts[0];
        static::assertSame(2, $relatedProduct->getProductId());
    }

    public function testCreateComplexProductWithAdmin(): void
    {
        $availibilityDate = new \DateTimeImmutable('@1519224245');
        $data = (new CreateProductCommand())
            ->setCode("code_full_D")
            ->setGreenTax(0.1)
            ->setCompanyId(3)
            ->setInfiniteStock(true)
            ->setIsBrandNew(true)
            ->setName("Full product")
            ->setSupplierReference('supplierref_full')
            ->setStatus(ProductStatus::ENABLED())
            ->setMainCategoryId(4)
            ->setFreeAttributes(
                [
                    'freeAttr1' => 'freeAttr1Value',
                    'freeAttr2' => 42,
                    'freeAttr3' => ['freeAttr3Value', 42],
                ]
            )
            ->setHasFreeShipping(true)
            ->setWeight(0.2)
            ->setIsDownloadable(true)
            ->setMainImage(new Uri('https://sandbox.wizaplace.com/assets/bundles/app/images/favicon.png'))
            ->setAdditionalImages(
                [
                    (new ProductImageUpload())->setName('image1.png')
                        ->setMimeType('image/png')
                        ->setBase64Data('iVBORw0KGgoAAAANSUhEUgAAACAAAAAgCAYAAABzenr0AAAFkElEQVRYw72XS2wbVRSGvzszthPHcWYmzYOEvJo0IUlbmqZvWkDiISoVCVUUVapUIVZsWCAhgYQEQqxgBaqExIIVFQtAqCCxQYK2qKQtSUlTtSUNbp2kNImdxLHrOHHGj2Hha9dO3DZ9cSRr5Lln7jn3v/899z+CVVhTSxuADmwCtshnG7BGuswAPuA80A8MAeExv++uc4u7BBZAPbAPeE0G1u/wnQ2EZSLfAj8Bk2N+n33PCTS1tLmB/cDbwEZA494sCQwCnwHHxvy+hVUlIOGuBz4EDgFuHsxiwDfAR8CN5duiFgneCnwhIXfy4OYEeoBO4LRumOFIOJQbVJY5Pw4cAfYWGXsQU+ScRyS6KxFoamkrAz4BXn3IwfO3uw3w6IZ5PBIOJXIJSLYfBN57SLDfKYku4JpumBci4VBupQ2S7W4evbllrAYAVa7+sPwp/D9WBYzqhtmv6oZpAh8DzS6hsMep0+uoYDadIGanbk/tci8Nz7yI0dHNQnCK1FL8tr5ltfU0v/AypZVVLAQnsVNJBfAAP2pAL/AkwC6HzgeetZQIlU6tjE9jo1h2uuhGtj63j+ZXDoIQONweRn44CvbKgqc6nXQcOEzt1qdIJSxSS3GmBvqQMXsVYCtQDlAqFJxCQchkmtXSoisyFQe73FUIVUUoCjVbdlKim0V9PfWNmJ0bQAgUTUNxOLJD5cBWRRYJAXA+EWUitQSAoTjY7qgoOmmP5uX54XFKY4s5iM2O7qK+1T3bcXq8ACwEA8z5hvOB7FGA9uybqbTF2UQkN/q008ArCq8ADcFup07DRJBG/3imymgaNb0781cHgKPMw5r1PSAyFX96qJ/47HS+S7sClGX/pbE5boWYl+Rbp7rp1jwFk9apLjY5ytESSZ64MIyayvianRvw1DUU+Fasbcfb2JK5meKLBAf/xE4XcKpsxbG7nIxxKTkPQIlQ2O3UUfPurB0OnWolU6vMv6/A1CQALq+eWW0WX6FQs3kHqqsEgIj/H8LXRorW6Fj+i0U7xUlrjhR2LmCt6soltMupo8iE/pwZ5+r5s9mI1PTuxFGWQcxlmFR2bcyIBDtNYKCP5OKKGzmmACvSOm2FuSHJWKs66dUyJGpT3XRpZTLRNL8vhZjoP4UVvQmAt7EFvbUDgMqujbhrHgMgHppl5uJgMY6OKFI0FBzgQB4ZVQTPOg1Khcoep0G5JOWVZIzLyRg3x0eZG7mU8XWVUN2zDdXpoqZnO4qa8Z25OEgsMFlMPQ0qUsNF80fS2PxmzRKVZFzv8LDF4WWbowIhvzxphYjaSdIJi6n+PtLJZKbGbuhlzfpN6O1dAKSsJQIDfdipFVU1CvQrwDkpIgtsOI+MXqHxemkda2Vhmk5bnJEIAcxcGmR+4joApVXVrNt/CFeFkYlyfZSw70ox+IeAcwowJwVkqpCMaU5YoRwZuzUPLpE5NP2JCNdTt2r/UiTM9FA/2DZCUfE2tSKEANsmMHAaaz66PHhKxpxTpGI9VgyFU9YcI8lC5s7bKX5ZmiWZTxvbZqLvBIszwQLfheAkgb/OLKdYdvXHxvw+WwXQDTMqj+NLQK6cLdhpgmmLNs1NqVCZsxMcjU/yax4yWUtEo1jzN/HUNaCoGrHABCPff50jaH5ewPvAH5Fw6FaFkTL8c+CNfF0ggCrFSa3iImInuZGKF64+/5YUApdRSYlhEg/NEg+Hlt+QaeBL4J2sTBfLVHG9dNj7CMRJGvgZeHPM75soKssj4VBUN8w+YJ0UkOIhB39rzO/797Z9gUwirBvmSanduh6CSI0BXwHvLg9eNIE8JE7IhrMRqL6PLUkCA5JwR8b8vtD9Nqd1sjk9AGxeRXM6J8v7d7I5nbqv5rRIy1YhdVy2PW+VyAAEgKvyfA/IZ2Q17fl/LFcBJAQMmYcAAAAASUVORK5CYII='),
                    new Uri('https://sandbox.wizaplace.com/assets/bundles/app/images/favicon.png'),
                ]
            )
            ->setFullDescription("super full description")
            ->setShortDescription("super short description")
            ->setTaxIds([1])
            ->setDeclinations(
                [
                    (new ProductDeclinationUpsertData([1 => 1, 2 => 5, 3 => 7]))
                        ->setCode('code_full_declA')
                        ->setPrice(3.5)
                        ->setQuantity(12)
                        ->setInfiniteStock(true)
                        ->setPriceTiers(
                            [
                                [
                                    'lowerLimit' => 0,
                                    'price' => 18.99,
                                ],
                                [
                                    'lowerLimit' => 15,
                                    'price' => 15.99,
                                ],
                            ]
                        ),
                    (new ProductDeclinationUpsertData([1 => 1, 2 => 6, 3 => 9]))
                        ->setPrice(100.0)
                        ->setCrossedOutPrice(1000.0)
                        ->setQuantity(3)
                        ->setInfiniteStock(true),
                ]
            )
            ->setGeolocation(
                (new ProductGeolocationUpsertData(/* latitude */ 45.778848, /* longitude */ 4.800039))
                    ->setLabel('Wizacha')
                    ->setZipcode('69009')
            )
            ->setAvailabilityDate($availibilityDate)
            ->setAttachments([new ProductAttachmentUpload('favicon', 'https://sandbox.wizaplace.com/assets/bundles/app/images/favicon.png')])
            ->setProductTemplateType('product');

        $productService = $this->buildProductService();
        $productId = $productService->createProduct($data);
        static::assertInternalType('int', $productId);
        static::assertGreaterThan(0, $productId);

        $product = $productService->getProductById($productId);
        static::assertInstanceOf(Product::class, $product);

        static::assertSame($productId, $product->getId());
        static::assertSame(4, $product->getMainCategoryId());
        static::assertSame("code_full_D", $product->getCode());
        static::assertSame(3, $product->getCompanyId());
    }

    public function testCreateAndUpdateProductWithQuotesData(): void
    {
        $productService = $this
            ->buildProductService('admin@wizaplace.com', ApiTestCase::VALID_PASSWORD);
        $id = $productService->createProduct(
            (new CreateProductCommand())
                ->setCode("product_with_quotes_data_2")
                ->setSupplierReference('product_with_quotes_data_ref')
                ->setName("Product with quotes data")
                ->setMainCategoryId(1)
                ->setGreenTax(0.)
                ->setCompanyId(3)
                ->setTaxIds([1])
                ->setDeclinations(
                    [
                        (new ProductDeclinationUpsertData([]))
                            ->setCode('product_with_quotes_data')
                            ->setPrice(3.5)
                            ->setQuantity(12)
                            ->setInfiniteStock(false),
                    ]
                )
                ->setStatus(ProductStatus::ENABLED())
                ->setIsBrandNew(true)
                ->setWeight(0.)
                ->setFullDescription("Product with quotes data full description")
                ->setShortDescription("Product with quotes data short description")
                ->setQuoteRequestsMinQuantity(10)
                ->setIsExclusiveToQuoteRequests(true)
        );

        static::assertTrue(\is_int($id));

        $product = $productService->getProductById($id);

        static::assertSame(10, $product->getQuoteRequestsMinQuantity());
        static::assertTrue($product->isExclusiveToQuoteRequests());

        $productService->updateProduct(
            (new UpdateProductCommand($id))
                ->setQuoteRequestsMinQuantity(5)
                ->setIsExclusiveToQuoteRequests(false)
        );
        $product = $productService->getProductById($id);

        static::assertSame(5, $product->getQuoteRequestsMinQuantity());
        static::assertFalse($product->isExclusiveToQuoteRequests());
    }

    private function getSampleImage(): ProductImageUpload
    {
        return (new ProductImageUpload())->setName('image1.png')
            ->setMimeType('image/png')
            ->setBase64Data('iVBORw0KGgoAAAANSUhEUgAAACAAAAAgCAYAAABzenr0AAAFkElEQVRYw72XS2wbVRSGvzszthPHcWYmzYOEvJo0IUlbmqZvWkDiISoVCVUUVapUIVZsWCAhgYQEQqxgBaqExIIVFQtAqCCxQYK2qKQtSUlTtSUNbp2kNImdxLHrOHHGj2Hha9dO3DZ9cSRr5Lln7jn3v/899z+CVVhTSxuADmwCtshnG7BGuswAPuA80A8MAeExv++uc4u7BBZAPbAPeE0G1u/wnQ2EZSLfAj8Bk2N+n33PCTS1tLmB/cDbwEZA494sCQwCnwHHxvy+hVUlIOGuBz4EDgFuHsxiwDfAR8CN5duiFgneCnwhIXfy4OYEeoBO4LRumOFIOJQbVJY5Pw4cAfYWGXsQU+ScRyS6KxFoamkrAz4BXn3IwfO3uw3w6IZ5PBIOJXIJSLYfBN57SLDfKYku4JpumBci4VBupQ2S7W4evbllrAYAVa7+sPwp/D9WBYzqhtmv6oZpAh8DzS6hsMep0+uoYDadIGanbk/tci8Nz7yI0dHNQnCK1FL8tr5ltfU0v/AypZVVLAQnsVNJBfAAP2pAL/AkwC6HzgeetZQIlU6tjE9jo1h2uuhGtj63j+ZXDoIQONweRn44CvbKgqc6nXQcOEzt1qdIJSxSS3GmBvqQMXsVYCtQDlAqFJxCQchkmtXSoisyFQe73FUIVUUoCjVbdlKim0V9PfWNmJ0bQAgUTUNxOLJD5cBWRRYJAXA+EWUitQSAoTjY7qgoOmmP5uX54XFKY4s5iM2O7qK+1T3bcXq8ACwEA8z5hvOB7FGA9uybqbTF2UQkN/q008ArCq8ADcFup07DRJBG/3imymgaNb0781cHgKPMw5r1PSAyFX96qJ/47HS+S7sClGX/pbE5boWYl+Rbp7rp1jwFk9apLjY5ytESSZ64MIyayvianRvw1DUU+Fasbcfb2JK5meKLBAf/xE4XcKpsxbG7nIxxKTkPQIlQ2O3UUfPurB0OnWolU6vMv6/A1CQALq+eWW0WX6FQs3kHqqsEgIj/H8LXRorW6Fj+i0U7xUlrjhR2LmCt6soltMupo8iE/pwZ5+r5s9mI1PTuxFGWQcxlmFR2bcyIBDtNYKCP5OKKGzmmACvSOm2FuSHJWKs66dUyJGpT3XRpZTLRNL8vhZjoP4UVvQmAt7EFvbUDgMqujbhrHgMgHppl5uJgMY6OKFI0FBzgQB4ZVQTPOg1Khcoep0G5JOWVZIzLyRg3x0eZG7mU8XWVUN2zDdXpoqZnO4qa8Z25OEgsMFlMPQ0qUsNF80fS2PxmzRKVZFzv8LDF4WWbowIhvzxphYjaSdIJi6n+PtLJZKbGbuhlzfpN6O1dAKSsJQIDfdipFVU1CvQrwDkpIgtsOI+MXqHxemkda2Vhmk5bnJEIAcxcGmR+4joApVXVrNt/CFeFkYlyfZSw70ox+IeAcwowJwVkqpCMaU5YoRwZuzUPLpE5NP2JCNdTt2r/UiTM9FA/2DZCUfE2tSKEANsmMHAaaz66PHhKxpxTpGI9VgyFU9YcI8lC5s7bKX5ZmiWZTxvbZqLvBIszwQLfheAkgb/OLKdYdvXHxvw+WwXQDTMqj+NLQK6cLdhpgmmLNs1NqVCZsxMcjU/yax4yWUtEo1jzN/HUNaCoGrHABCPff50jaH5ewPvAH5Fw6FaFkTL8c+CNfF0ggCrFSa3iImInuZGKF64+/5YUApdRSYlhEg/NEg+Hlt+QaeBL4J2sTBfLVHG9dNj7CMRJGvgZeHPM75soKssj4VBUN8w+YJ0UkOIhB39rzO/797Z9gUwirBvmSanduh6CSI0BXwHvLg9eNIE8JE7IhrMRqL6PLUkCA5JwR8b8vtD9Nqd1sjk9AGxeRXM6J8v7d7I5nbqv5rRIy1YhdVy2PW+VyAAEgKvyfA/IZ2Q17fl/LFcBJAQMmYcAAAAASUVORK5CYII=');
    }
}
