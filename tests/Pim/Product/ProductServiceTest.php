<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types=1);

namespace Wizaplace\SDK\Tests\Pim\Product;

use GuzzleHttp\Psr7\Uri;
use Psr\Http\Message\UriInterface;
use Wizaplace\SDK\Exception\NotFound;
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
use Wizaplace\SDK\Pim\Product\ProductListFilter;
use Wizaplace\SDK\Pim\Product\ProductService;
use Wizaplace\SDK\Pim\Product\ProductStatus;
use Wizaplace\SDK\Pim\Product\ProductSummary;
use Wizaplace\SDK\Pim\Product\UpdateProductCommand;
use Wizaplace\SDK\Tests\ApiTestCase;

final class ProductServiceTest extends ApiTestCase
{
    public function testGetProductById()
    {
        $product = $this->buildProductService()->getProductById(8);

        $this->assertInstanceOf(Product::class, $product);
        $this->assertSame(8, $product->getId());
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
        $this->assertSame(6, $product->getMainCategoryId());
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
        $this->assertInstanceOf(\DateTimeImmutable::class, $product->getAvailibilityDate());
        $this->assertGreaterThan(130000000, $product->getAvailibilityDate()->getTimestamp());
        $this->assertContainsOnly(UriInterface::class, $product->getAdditionalImages());
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
        $this->assertCount(8, $product->getDeclinations());

        $declination = $product->getDeclinations()[0];
        $this->assertSame(10, $declination->getQuantity());
        $this->assertSame([1 => 1, 2 => 5], $declination->getOptionsVariants());
        $this->assertSame(15.5, $declination->getPrice());
        $this->assertNull($declination->getCrossedOutPrice());
        $this->assertSame('color_white_connectivity_wireles', $declination->getCode());
        $this->assertNull($declination->getAffiliateLink());
    }

    public function testGetProductWithAttachments()
    {
        $product = $this->buildProductService()->getProductById(10);

        $this->assertSame(10, $product->getId());
        $attachments = $product->getAttachments();
        $this->assertContainsOnly(ProductAttachment::class, $attachments);
        $this->assertCount(2, $attachments);

        $attachment = $attachments[0];
        $this->assertNotEmpty($attachment->getId());
        $this->assertSame('Manuel de montage', $attachment->getLabel());
    }

    public function testGetProductWithGeolocation()
    {
        $product = $this->buildProductService()->getProductById(9);

        $this->assertSame(9, $product->getId());
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

    public function testDeleteProduct()
    {
        $service = $this->buildProductService();
        $this->assertNotNull($service->getProductById(1));
        $service->deleteProduct(1);

        $this->expectException(NotFound::class);
        $service->getProductById(1);
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
                1,
            ],
            'disabled, in a specific category or its subcategories' => [
                ProductStatus::DISABLED(),
                [3],
                true,
                [],
                null,
                0,
            ],
            'enabled, with a specific product code' => [
                ProductStatus::ENABLED(),
                null,
                false,
                null,
                '20230495445',
                1,
            ],
            'disabled, with a specific product code which is enabled' => [
                ProductStatus::DISABLED(),
                null,
                false,
                [],
                '20230495445',
                0,
            ],
        ];
    }

    public function testCreateComplexProduct(): void
    {
        $availibilityDate = new \DateTimeImmutable('@1519224245');
        $data = (new CreateProductCommand())
            ->setCode("code_full")
            ->setGreenTax(0.1)
            ->setInfiniteStock(true)
            ->setIsBrandNew(true)
            ->setName("Full product")
            ->setSupplierReference('supplierref_full')
            ->setStatus(ProductStatus::ENABLED())
            ->setMainCategoryId(4)
            ->setFreeAttributes([
                'freeAttr1' => 'freeAttr1Value',
                'freeAttr2' => 42,
                'freeAttr3' => ['freeAttr3Value', 42],
            ])
            ->setHasFreeShipping(true)
            ->setWeight(0.2)
            ->setIsDownloadable(true)
            ->setMainImage(new Uri('https://sandbox.wizaplace.com/api/v1/doc/favicon.png'))
            ->setAdditionalImages([
                (new ProductImageUpload())->setName('image1.png')
                    ->setMimeType('image/png')
                    ->setBase64Data('iVBORw0KGgoAAAANSUhEUgAAACAAAAAgCAYAAABzenr0AAAFkElEQVRYw72XS2wbVRSGvzszthPHcWYmzYOEvJo0IUlbmqZvWkDiISoVCVUUVapUIVZsWCAhgYQEQqxgBaqExIIVFQtAqCCxQYK2qKQtSUlTtSUNbp2kNImdxLHrOHHGj2Hha9dO3DZ9cSRr5Lln7jn3v/899z+CVVhTSxuADmwCtshnG7BGuswAPuA80A8MAeExv++uc4u7BBZAPbAPeE0G1u/wnQ2EZSLfAj8Bk2N+n33PCTS1tLmB/cDbwEZA494sCQwCnwHHxvy+hVUlIOGuBz4EDgFuHsxiwDfAR8CN5duiFgneCnwhIXfy4OYEeoBO4LRumOFIOJQbVJY5Pw4cAfYWGXsQU+ScRyS6KxFoamkrAz4BXn3IwfO3uw3w6IZ5PBIOJXIJSLYfBN57SLDfKYku4JpumBci4VBupQ2S7W4evbllrAYAVa7+sPwp/D9WBYzqhtmv6oZpAh8DzS6hsMep0+uoYDadIGanbk/tci8Nz7yI0dHNQnCK1FL8tr5ltfU0v/AypZVVLAQnsVNJBfAAP2pAL/AkwC6HzgeetZQIlU6tjE9jo1h2uuhGtj63j+ZXDoIQONweRn44CvbKgqc6nXQcOEzt1qdIJSxSS3GmBvqQMXsVYCtQDlAqFJxCQchkmtXSoisyFQe73FUIVUUoCjVbdlKim0V9PfWNmJ0bQAgUTUNxOLJD5cBWRRYJAXA+EWUitQSAoTjY7qgoOmmP5uX54XFKY4s5iM2O7qK+1T3bcXq8ACwEA8z5hvOB7FGA9uybqbTF2UQkN/q008ArCq8ADcFup07DRJBG/3imymgaNb0781cHgKPMw5r1PSAyFX96qJ/47HS+S7sClGX/pbE5boWYl+Rbp7rp1jwFk9apLjY5ytESSZ64MIyayvianRvw1DUU+Fasbcfb2JK5meKLBAf/xE4XcKpsxbG7nIxxKTkPQIlQ2O3UUfPurB0OnWolU6vMv6/A1CQALq+eWW0WX6FQs3kHqqsEgIj/H8LXRorW6Fj+i0U7xUlrjhR2LmCt6soltMupo8iE/pwZ5+r5s9mI1PTuxFGWQcxlmFR2bcyIBDtNYKCP5OKKGzmmACvSOm2FuSHJWKs66dUyJGpT3XRpZTLRNL8vhZjoP4UVvQmAt7EFvbUDgMqujbhrHgMgHppl5uJgMY6OKFI0FBzgQB4ZVQTPOg1Khcoep0G5JOWVZIzLyRg3x0eZG7mU8XWVUN2zDdXpoqZnO4qa8Z25OEgsMFlMPQ0qUsNF80fS2PxmzRKVZFzv8LDF4WWbowIhvzxphYjaSdIJi6n+PtLJZKbGbuhlzfpN6O1dAKSsJQIDfdipFVU1CvQrwDkpIgtsOI+MXqHxemkda2Vhmk5bnJEIAcxcGmR+4joApVXVrNt/CFeFkYlyfZSw70ox+IeAcwowJwVkqpCMaU5YoRwZuzUPLpE5NP2JCNdTt2r/UiTM9FA/2DZCUfE2tSKEANsmMHAaaz66PHhKxpxTpGI9VgyFU9YcI8lC5s7bKX5ZmiWZTxvbZqLvBIszwQLfheAkgb/OLKdYdvXHxvw+WwXQDTMqj+NLQK6cLdhpgmmLNs1NqVCZsxMcjU/yax4yWUtEo1jzN/HUNaCoGrHABCPff50jaH5ewPvAH5Fw6FaFkTL8c+CNfF0ggCrFSa3iImInuZGKF64+/5YUApdRSYlhEg/NEg+Hlt+QaeBL4J2sTBfLVHG9dNj7CMRJGvgZeHPM75soKssj4VBUN8w+YJ0UkOIhB39rzO/797Z9gUwirBvmSanduh6CSI0BXwHvLg9eNIE8JE7IhrMRqL6PLUkCA5JwR8b8vtD9Nqd1sjk9AGxeRXM6J8v7d7I5nbqv5rRIy1YhdVy2PW+VyAAEgKvyfA/IZ2Q17fl/LFcBJAQMmYcAAAAASUVORK5CYII='),
                new Uri('https://sandbox.wizaplace.com/api/v1/doc/favicon.png'),
            ])
            ->setFullDescription("super full description")
            ->setShortDescription("super short description")
            ->setTaxIds([1, 2])
            ->setDeclinations([
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
            ])
            ->setGeolocation(
                (new ProductGeolocationUpsertData(/* latitude */ 45.778848, /* longitude */ 4.800039))
                    ->setLabel('Wizacha')
                    ->setZipcode('69009')
            )
            ->setAvailabilityDate($availibilityDate)
        ->setAttachments([new ProductAttachmentUpload('favicon', 'https://sandbox.wizaplace.com/api/v1/doc/favicon.png')]);
        $productService = $this->buildProductService('vendor@wizaplace.com');
        $productId = $productService->createProduct($data);
        $this->assertInternalType('int', $productId);
        $this->assertGreaterThan(0, $productId);

        $product = $productService->getProductById($productId);
        $this->assertInstanceOf(Product::class, $product);

        $this->assertSame($productId, $product->getId());
        $this->assertSame(4, $product->getMainCategoryId());
        $this->assertSame("code_full", $product->getCode());
        $this->assertSame("Full product", $product->getName());
        $this->assertSame('supplierref_full', $product->getSupplierReference());
        $this->assertSame("super full description", $product->getFullDescription());
        $this->assertSame("super short description", $product->getShortDescription());
        $this->assertTrue($product->isBrandNew());
        $this->assertTrue($product->hasFreeShipping());
        $this->assertSame([1, 2], $product->getTaxIds());
        $this->assertSame([
            'freeAttr1' => ['freeAttr1Value'],
            'freeAttr2' => [42],
            'freeAttr3' => ['freeAttr3Value', 42],
        ], $product->getFreeAttributes());
        $this->assertSame(0.1, $product->getGreenTax());
        $this->assertTrue($product->hasInfiniteStock());
        $this->assertSame(0.2, $product->getWeight());
        $this->assertTrue(ProductStatus::ENABLED()->equals($product->getStatus()));
        $this->assertTrue(ProductApprovalStatus::PENDING()->equals($product->getApprovalStatus()));
        $this->assertTrue($product->isDownloadable());
        $this->assertRegExp('#/images/detailed/0/[^.]+.png#', (string) $product->getMainImage());
        $additionalImages = $product->getAdditionalImages();
        $this->assertCount(2, $additionalImages);
        $this->assertRegExp('#/images/detailed/0/[^.]+.png#', (string) $additionalImages[12]);
        $this->assertRegExp('#/images/detailed/0/[^.]+.png#', (string) $additionalImages[13]);
        $this->assertNotEquals((string) $additionalImages[12], (string) $additionalImages[13]);

        $attachments = $product->getAttachments();
        $this->assertContainsOnly(ProductAttachment::class, $attachments);
        $this->assertCount(1, $attachments);
        $this->assertSame('favicon', $attachments[0]->getLabel());
        $this->assertNotEmpty($attachments[0]->getId());

        $geolocation = $product->getGeolocation();
        $this->assertInstanceOf(ProductGeolocation::class, $geolocation);
        $this->assertSame('Wizacha', $geolocation->getLabel());
        $this->assertSame('69009', $geolocation->getZipcode());
        $this->assertSame(45.778848, $geolocation->getLatitude());
        $this->assertSame(4.800039, $geolocation->getLongitude());

        $this->assertSame($availibilityDate->getTimestamp(), $product->getAvailibilityDate()->getTimestamp());

        // Checking declinations
        $declinations = $product->getDeclinations();
        $this->assertContainsOnly(ProductDeclination::class, $declinations);
        $this->assertCount(4, $declinations);

        $this->assertSame([1 => 1, 2 => 5, 3 => 7], $declinations[0]->getOptionsVariants());
        $this->assertSame('code_full_declA', $declinations[0]->getCode());
        $this->assertNull($declinations[0]->getAffiliateLink());
        $this->assertNull($declinations[0]->getCrossedOutPrice());
        $this->assertSame(12, $declinations[0]->getQuantity());
        $this->assertSame(3.5, $declinations[0]->getPrice());

        // empty declination generated automatically to complete the matrix
        $this->assertSame([1 => 1, 2 => 5, 3 => 9], $declinations[1]->getOptionsVariants());
        $this->assertSame(0.0, $declinations[1]->getPrice());
        $this->assertSame(0, $declinations[1]->getQuantity());
        $this->assertNull($declinations[1]->getCrossedOutPrice());
        $this->assertNull($declinations[1]->getAffiliateLink());
        $this->assertNull($declinations[1]->getCode());

        // empty declination generated automatically to complete the matrix
        $this->assertSame([1 => 1, 2 => 6, 3 => 7], $declinations[2]->getOptionsVariants());
        $this->assertSame(0.0, $declinations[2]->getPrice());
        $this->assertSame(0, $declinations[2]->getQuantity());
        $this->assertNull($declinations[2]->getCrossedOutPrice());
        $this->assertNull($declinations[2]->getAffiliateLink());
        $this->assertNull($declinations[2]->getCode());

        $this->assertSame([1 => 1, 2 => 6, 3 => 9], $declinations[3]->getOptionsVariants());
        $this->assertSame(100.0, $declinations[3]->getPrice());
        $this->assertSame(3, $declinations[3]->getQuantity());
        $this->assertSame(1000.0, $declinations[3]->getCrossedOutPrice());
        $this->assertNull($declinations[3]->getAffiliateLink());
        $this->assertNull($declinations[3]->getCode());
    }

    public function testPartialProductUpdate(): void
    {
        $data = (new CreateProductCommand())
            ->setCode("code_full")
            ->setGreenTax(0.1)
            ->setIsBrandNew(true)
            ->setName("Full product")
            ->setSupplierReference('supplierref_full')
            ->setStatus(ProductStatus::ENABLED())
            ->setMainCategoryId(4)
            ->setFreeAttributes([
                'freeAttr1' => 'freeAttr1Value',
                'freeAttr2' => 42,
                'freeAttr3' => ['freeAttr3Value', 42],
            ])
            ->setHasFreeShipping(true)
            ->setWeight(0.2)
            ->setIsDownloadable(true)
            ->setMainImage(new Uri('https://sandbox.wizaplace.com/api/v1/doc/favicon.png'))
            ->setAdditionalImages([
                (new ProductImageUpload())->setName('image1.png')
                    ->setMimeType('image/png')
                    ->setBase64Data('iVBORw0KGgoAAAANSUhEUgAAACAAAAAgCAYAAABzenr0AAAFkElEQVRYw72XS2wbVRSGvzszthPHcWYmzYOEvJo0IUlbmqZvWkDiISoVCVUUVapUIVZsWCAhgYQEQqxgBaqExIIVFQtAqCCxQYK2qKQtSUlTtSUNbp2kNImdxLHrOHHGj2Hha9dO3DZ9cSRr5Lln7jn3v/899z+CVVhTSxuADmwCtshnG7BGuswAPuA80A8MAeExv++uc4u7BBZAPbAPeE0G1u/wnQ2EZSLfAj8Bk2N+n33PCTS1tLmB/cDbwEZA494sCQwCnwHHxvy+hVUlIOGuBz4EDgFuHsxiwDfAR8CN5duiFgneCnwhIXfy4OYEeoBO4LRumOFIOJQbVJY5Pw4cAfYWGXsQU+ScRyS6KxFoamkrAz4BXn3IwfO3uw3w6IZ5PBIOJXIJSLYfBN57SLDfKYku4JpumBci4VBupQ2S7W4evbllrAYAVa7+sPwp/D9WBYzqhtmv6oZpAh8DzS6hsMep0+uoYDadIGanbk/tci8Nz7yI0dHNQnCK1FL8tr5ltfU0v/AypZVVLAQnsVNJBfAAP2pAL/AkwC6HzgeetZQIlU6tjE9jo1h2uuhGtj63j+ZXDoIQONweRn44CvbKgqc6nXQcOEzt1qdIJSxSS3GmBvqQMXsVYCtQDlAqFJxCQchkmtXSoisyFQe73FUIVUUoCjVbdlKim0V9PfWNmJ0bQAgUTUNxOLJD5cBWRRYJAXA+EWUitQSAoTjY7qgoOmmP5uX54XFKY4s5iM2O7qK+1T3bcXq8ACwEA8z5hvOB7FGA9uybqbTF2UQkN/q008ArCq8ADcFup07DRJBG/3imymgaNb0781cHgKPMw5r1PSAyFX96qJ/47HS+S7sClGX/pbE5boWYl+Rbp7rp1jwFk9apLjY5ytESSZ64MIyayvianRvw1DUU+Fasbcfb2JK5meKLBAf/xE4XcKpsxbG7nIxxKTkPQIlQ2O3UUfPurB0OnWolU6vMv6/A1CQALq+eWW0WX6FQs3kHqqsEgIj/H8LXRorW6Fj+i0U7xUlrjhR2LmCt6soltMupo8iE/pwZ5+r5s9mI1PTuxFGWQcxlmFR2bcyIBDtNYKCP5OKKGzmmACvSOm2FuSHJWKs66dUyJGpT3XRpZTLRNL8vhZjoP4UVvQmAt7EFvbUDgMqujbhrHgMgHppl5uJgMY6OKFI0FBzgQB4ZVQTPOg1Khcoep0G5JOWVZIzLyRg3x0eZG7mU8XWVUN2zDdXpoqZnO4qa8Z25OEgsMFlMPQ0qUsNF80fS2PxmzRKVZFzv8LDF4WWbowIhvzxphYjaSdIJi6n+PtLJZKbGbuhlzfpN6O1dAKSsJQIDfdipFVU1CvQrwDkpIgtsOI+MXqHxemkda2Vhmk5bnJEIAcxcGmR+4joApVXVrNt/CFeFkYlyfZSw70ox+IeAcwowJwVkqpCMaU5YoRwZuzUPLpE5NP2JCNdTt2r/UiTM9FA/2DZCUfE2tSKEANsmMHAaaz66PHhKxpxTpGI9VgyFU9YcI8lC5s7bKX5ZmiWZTxvbZqLvBIszwQLfheAkgb/OLKdYdvXHxvw+WwXQDTMqj+NLQK6cLdhpgmmLNs1NqVCZsxMcjU/yax4yWUtEo1jzN/HUNaCoGrHABCPff50jaH5ewPvAH5Fw6FaFkTL8c+CNfF0ggCrFSa3iImInuZGKF64+/5YUApdRSYlhEg/NEg+Hlt+QaeBL4J2sTBfLVHG9dNj7CMRJGvgZeHPM75soKssj4VBUN8w+YJ0UkOIhB39rzO/797Z9gUwirBvmSanduh6CSI0BXwHvLg9eNIE8JE7IhrMRqL6PLUkCA5JwR8b8vtD9Nqd1sjk9AGxeRXM6J8v7d7I5nbqv5rRIy1YhdVy2PW+VyAAEgKvyfA/IZ2Q17fl/LFcBJAQMmYcAAAAASUVORK5CYII='),
                new Uri('https://sandbox.wizaplace.com/api/v1/doc/favicon.png'),
            ])
            ->setFullDescription("super full description")
            ->setShortDescription("super short description")
            ->setTaxIds([1, 2])
            ->setDeclinations([
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
            ])
            ->setAttachments([new ProductAttachmentUpload('favicon', 'https://sandbox.wizaplace.com/api/v1/doc/favicon.png')]);
        $productService = $this->buildProductService('vendor@wizaplace.com');
        $productId = $productService->createProduct($data);
        $this->assertInternalType('int', $productId);
        $this->assertGreaterThan(0, $productId);

        $productService->updateProduct((new UpdateProductCommand($productId))->setName('Full product 2'));

        $product = $productService->getProductById($productId);
        $this->assertInstanceOf(Product::class, $product);

        $this->assertSame($productId, $product->getId());
        $this->assertSame(4, $product->getMainCategoryId());
        $this->assertSame("code_full", $product->getCode());
        $this->assertSame("Full product 2", $product->getName());
        $this->assertSame('supplierref_full', $product->getSupplierReference());
        $this->assertSame("super full description", $product->getFullDescription());
        $this->assertSame("super short description", $product->getShortDescription());
        $this->assertTrue($product->isBrandNew());
        $this->assertTrue($product->hasFreeShipping());
        $this->assertSame([1, 2], $product->getTaxIds());
        $this->assertSame([
            'freeAttr1' => ['freeAttr1Value'],
            'freeAttr2' => [42],
            'freeAttr3' => ['freeAttr3Value', 42],
        ], $product->getFreeAttributes());
        $this->assertSame(0.1, $product->getGreenTax());
        $this->assertSame(0.2, $product->getWeight());
        $this->assertTrue(ProductStatus::ENABLED()->equals($product->getStatus()));
        $this->assertTrue(ProductApprovalStatus::PENDING()->equals($product->getApprovalStatus()));
        $this->assertTrue($product->isDownloadable());
        $this->assertRegExp('#/images/detailed/0/[^.]+.png#', (string) $product->getMainImage());
        $additionalImages = $product->getAdditionalImages();
        $this->assertCount(2, $additionalImages);
        $this->assertRegExp('#/images/detailed/0/[^.]+.png#', (string) $additionalImages[12]);
        $this->assertRegExp('#/images/detailed/0/[^.]+.png#', (string) $additionalImages[13]);
        $this->assertNotEquals((string) $additionalImages[12], (string) $additionalImages[13]);

        $attachments = $product->getAttachments();
        $this->assertContainsOnly(ProductAttachment::class, $attachments);
        $this->assertCount(1, $attachments);
        $this->assertSame('favicon', $attachments[0]->getLabel());
        $this->assertNotEmpty($attachments[0]->getId());

        // Checking declinations
        $declinations = $product->getDeclinations();
        $this->assertContainsOnly(ProductDeclination::class, $declinations);
        $this->assertCount(4, $declinations);

        $this->assertSame([1 => 1, 2 => 5, 3 => 7], $declinations[0]->getOptionsVariants());
        $this->assertSame('code_full_declA', $declinations[0]->getCode());
        $this->assertNull($declinations[0]->getAffiliateLink());
        $this->assertNull($declinations[0]->getCrossedOutPrice());
        $this->assertSame(12, $declinations[0]->getQuantity());
        $this->assertSame(3.5, $declinations[0]->getPrice());

        // empty declination generated automatically to complete the matrix
        $this->assertSame([1 => 1, 2 => 5, 3 => 9], $declinations[1]->getOptionsVariants());
        $this->assertSame(0.0, $declinations[1]->getPrice());
        $this->assertSame(0, $declinations[1]->getQuantity());
        $this->assertNull($declinations[1]->getCrossedOutPrice());
        $this->assertNull($declinations[1]->getAffiliateLink());
        $this->assertNull($declinations[1]->getCode());

        // empty declination generated automatically to complete the matrix
        $this->assertSame([1 => 1, 2 => 6, 3 => 7], $declinations[2]->getOptionsVariants());
        $this->assertSame(0.0, $declinations[2]->getPrice());
        $this->assertSame(0, $declinations[2]->getQuantity());
        $this->assertNull($declinations[2]->getCrossedOutPrice());
        $this->assertNull($declinations[2]->getAffiliateLink());
        $this->assertNull($declinations[2]->getCode());

        $this->assertSame([1 => 1, 2 => 6, 3 => 9], $declinations[3]->getOptionsVariants());
        $this->assertSame(100.0, $declinations[3]->getPrice());
        $this->assertSame(3, $declinations[3]->getQuantity());
        $this->assertSame(1000.0, $declinations[3]->getCrossedOutPrice());
        $this->assertNull($declinations[3]->getAffiliateLink());
        $this->assertNull($declinations[3]->getCode());
    }

    public function testUpdateComplexProduct(): void
    {
        $data = (new CreateProductCommand())
            ->setCode("code_full")
            ->setGreenTax(0.1)
            ->setIsBrandNew(true)
            ->setName("Full product")
            ->setSupplierReference('supplierref_full')
            ->setStatus(ProductStatus::ENABLED())
            ->setMainCategoryId(4)
            ->setFreeAttributes([
                'freeAttr1' => 'freeAttr1Value',
                'freeAttr2' => 42,
                'freeAttr3' => ['freeAttr3Value', 42],
            ])
            ->setHasFreeShipping(true)
            ->setWeight(0.2)
            ->setIsDownloadable(true)
            ->setMainImage(new Uri('https://sandbox.wizaplace.com/api/v1/doc/favicon.png'))
            ->setAdditionalImages([
                (new ProductImageUpload())->setName('image1.png')
                    ->setMimeType('image/png')
                    ->setBase64Data('iVBORw0KGgoAAAANSUhEUgAAACAAAAAgCAYAAABzenr0AAAFkElEQVRYw72XS2wbVRSGvzszthPHcWYmzYOEvJo0IUlbmqZvWkDiISoVCVUUVapUIVZsWCAhgYQEQqxgBaqExIIVFQtAqCCxQYK2qKQtSUlTtSUNbp2kNImdxLHrOHHGj2Hha9dO3DZ9cSRr5Lln7jn3v/899z+CVVhTSxuADmwCtshnG7BGuswAPuA80A8MAeExv++uc4u7BBZAPbAPeE0G1u/wnQ2EZSLfAj8Bk2N+n33PCTS1tLmB/cDbwEZA494sCQwCnwHHxvy+hVUlIOGuBz4EDgFuHsxiwDfAR8CN5duiFgneCnwhIXfy4OYEeoBO4LRumOFIOJQbVJY5Pw4cAfYWGXsQU+ScRyS6KxFoamkrAz4BXn3IwfO3uw3w6IZ5PBIOJXIJSLYfBN57SLDfKYku4JpumBci4VBupQ2S7W4evbllrAYAVa7+sPwp/D9WBYzqhtmv6oZpAh8DzS6hsMep0+uoYDadIGanbk/tci8Nz7yI0dHNQnCK1FL8tr5ltfU0v/AypZVVLAQnsVNJBfAAP2pAL/AkwC6HzgeetZQIlU6tjE9jo1h2uuhGtj63j+ZXDoIQONweRn44CvbKgqc6nXQcOEzt1qdIJSxSS3GmBvqQMXsVYCtQDlAqFJxCQchkmtXSoisyFQe73FUIVUUoCjVbdlKim0V9PfWNmJ0bQAgUTUNxOLJD5cBWRRYJAXA+EWUitQSAoTjY7qgoOmmP5uX54XFKY4s5iM2O7qK+1T3bcXq8ACwEA8z5hvOB7FGA9uybqbTF2UQkN/q008ArCq8ADcFup07DRJBG/3imymgaNb0781cHgKPMw5r1PSAyFX96qJ/47HS+S7sClGX/pbE5boWYl+Rbp7rp1jwFk9apLjY5ytESSZ64MIyayvianRvw1DUU+Fasbcfb2JK5meKLBAf/xE4XcKpsxbG7nIxxKTkPQIlQ2O3UUfPurB0OnWolU6vMv6/A1CQALq+eWW0WX6FQs3kHqqsEgIj/H8LXRorW6Fj+i0U7xUlrjhR2LmCt6soltMupo8iE/pwZ5+r5s9mI1PTuxFGWQcxlmFR2bcyIBDtNYKCP5OKKGzmmACvSOm2FuSHJWKs66dUyJGpT3XRpZTLRNL8vhZjoP4UVvQmAt7EFvbUDgMqujbhrHgMgHppl5uJgMY6OKFI0FBzgQB4ZVQTPOg1Khcoep0G5JOWVZIzLyRg3x0eZG7mU8XWVUN2zDdXpoqZnO4qa8Z25OEgsMFlMPQ0qUsNF80fS2PxmzRKVZFzv8LDF4WWbowIhvzxphYjaSdIJi6n+PtLJZKbGbuhlzfpN6O1dAKSsJQIDfdipFVU1CvQrwDkpIgtsOI+MXqHxemkda2Vhmk5bnJEIAcxcGmR+4joApVXVrNt/CFeFkYlyfZSw70ox+IeAcwowJwVkqpCMaU5YoRwZuzUPLpE5NP2JCNdTt2r/UiTM9FA/2DZCUfE2tSKEANsmMHAaaz66PHhKxpxTpGI9VgyFU9YcI8lC5s7bKX5ZmiWZTxvbZqLvBIszwQLfheAkgb/OLKdYdvXHxvw+WwXQDTMqj+NLQK6cLdhpgmmLNs1NqVCZsxMcjU/yax4yWUtEo1jzN/HUNaCoGrHABCPff50jaH5ewPvAH5Fw6FaFkTL8c+CNfF0ggCrFSa3iImInuZGKF64+/5YUApdRSYlhEg/NEg+Hlt+QaeBL4J2sTBfLVHG9dNj7CMRJGvgZeHPM75soKssj4VBUN8w+YJ0UkOIhB39rzO/797Z9gUwirBvmSanduh6CSI0BXwHvLg9eNIE8JE7IhrMRqL6PLUkCA5JwR8b8vtD9Nqd1sjk9AGxeRXM6J8v7d7I5nbqv5rRIy1YhdVy2PW+VyAAEgKvyfA/IZ2Q17fl/LFcBJAQMmYcAAAAASUVORK5CYII='),
                new Uri('https://sandbox.wizaplace.com/api/v1/doc/favicon.png'),
            ])
            ->setFullDescription("super full description")
            ->setShortDescription("super short description")
            ->setTaxIds([1, 2])
            ->setDeclinations([
                (new ProductDeclinationUpsertData([1 => 1, 2 => 5, 3 => 7]))
                    ->setCode('code_full_declA')
                    ->setPrice(3.5)
                    ->setQuantity(12),
                (new ProductDeclinationUpsertData([1 => 1, 2 => 6, 3 => 9]))
                    ->setPrice(100.0)
                    ->setCrossedOutPrice(1000.0)
                    ->setQuantity(3),
            ])
        ->setAttachments([new ProductAttachmentUpload('favicon', 'https://sandbox.wizaplace.com/api/v1/doc/favicon.png')]);
        $productService = $this->buildProductService('vendor@wizaplace.com');
        $productId = $productService->createProduct($data);
        $this->assertInternalType('int', $productId);
        $this->assertGreaterThan(0, $productId);

        $data = (new UpdateProductCommand($productId))
            ->setCode("code_full2")
            ->setGreenTax(0.2)
            ->setIsBrandNew(false)
            ->setName("Full product2")
            ->setSupplierReference('supplierref_full2')
            ->setStatus(ProductStatus::DISABLED())
            ->setMainCategoryId(4)
            ->setFreeAttributes([
                'freeAttr1' => 'freeAttr1Value2',
                'freeAttr2' => 43,
                'freeAttr3' => ['freeAttr3Value2', 43],
            ])
            ->setHasFreeShipping(false)
            ->setWeight(0.3)
            ->setIsDownloadable(false)
            ->setMainImage(
                (new ProductImageUpload())->setName('image3.png')
                    ->setMimeType('image/png')
                    ->setBase64Data('iVBORw0KGgoAAAANSUhEUgAAACAAAAAgCAYAAABzenr0AAAFkElEQVRYw72XS2wbVRSGvzszthPHcWYmzYOEvJo0IUlbmqZvWkDiISoVCVUUVapUIVZsWCAhgYQEQqxgBaqExIIVFQtAqCCxQYK2qKQtSUlTtSUNbp2kNImdxLHrOHHGj2Hha9dO3DZ9cSRr5Lln7jn3v/899z+CVVhTSxuADmwCtshnG7BGuswAPuA80A8MAeExv++uc4u7BBZAPbAPeE0G1u/wnQ2EZSLfAj8Bk2N+n33PCTS1tLmB/cDbwEZA494sCQwCnwHHxvy+hVUlIOGuBz4EDgFuHsxiwDfAR8CN5duiFgneCnwhIXfy4OYEeoBO4LRumOFIOJQbVJY5Pw4cAfYWGXsQU+ScRyS6KxFoamkrAz4BXn3IwfO3uw3w6IZ5PBIOJXIJSLYfBN57SLDfKYku4JpumBci4VBupQ2S7W4evbllrAYAVa7+sPwp/D9WBYzqhtmv6oZpAh8DzS6hsMep0+uoYDadIGanbk/tci8Nz7yI0dHNQnCK1FL8tr5ltfU0v/AypZVVLAQnsVNJBfAAP2pAL/AkwC6HzgeetZQIlU6tjE9jo1h2uuhGtj63j+ZXDoIQONweRn44CvbKgqc6nXQcOEzt1qdIJSxSS3GmBvqQMXsVYCtQDlAqFJxCQchkmtXSoisyFQe73FUIVUUoCjVbdlKim0V9PfWNmJ0bQAgUTUNxOLJD5cBWRRYJAXA+EWUitQSAoTjY7qgoOmmP5uX54XFKY4s5iM2O7qK+1T3bcXq8ACwEA8z5hvOB7FGA9uybqbTF2UQkN/q008ArCq8ADcFup07DRJBG/3imymgaNb0781cHgKPMw5r1PSAyFX96qJ/47HS+S7sClGX/pbE5boWYl+Rbp7rp1jwFk9apLjY5ytESSZ64MIyayvianRvw1DUU+Fasbcfb2JK5meKLBAf/xE4XcKpsxbG7nIxxKTkPQIlQ2O3UUfPurB0OnWolU6vMv6/A1CQALq+eWW0WX6FQs3kHqqsEgIj/H8LXRorW6Fj+i0U7xUlrjhR2LmCt6soltMupo8iE/pwZ5+r5s9mI1PTuxFGWQcxlmFR2bcyIBDtNYKCP5OKKGzmmACvSOm2FuSHJWKs66dUyJGpT3XRpZTLRNL8vhZjoP4UVvQmAt7EFvbUDgMqujbhrHgMgHppl5uJgMY6OKFI0FBzgQB4ZVQTPOg1Khcoep0G5JOWVZIzLyRg3x0eZG7mU8XWVUN2zDdXpoqZnO4qa8Z25OEgsMFlMPQ0qUsNF80fS2PxmzRKVZFzv8LDF4WWbowIhvzxphYjaSdIJi6n+PtLJZKbGbuhlzfpN6O1dAKSsJQIDfdipFVU1CvQrwDkpIgtsOI+MXqHxemkda2Vhmk5bnJEIAcxcGmR+4joApVXVrNt/CFeFkYlyfZSw70ox+IeAcwowJwVkqpCMaU5YoRwZuzUPLpE5NP2JCNdTt2r/UiTM9FA/2DZCUfE2tSKEANsmMHAaaz66PHhKxpxTpGI9VgyFU9YcI8lC5s7bKX5ZmiWZTxvbZqLvBIszwQLfheAkgb/OLKdYdvXHxvw+WwXQDTMqj+NLQK6cLdhpgmmLNs1NqVCZsxMcjU/yax4yWUtEo1jzN/HUNaCoGrHABCPff50jaH5ewPvAH5Fw6FaFkTL8c+CNfF0ggCrFSa3iImInuZGKF64+/5YUApdRSYlhEg/NEg+Hlt+QaeBL4J2sTBfLVHG9dNj7CMRJGvgZeHPM75soKssj4VBUN8w+YJ0UkOIhB39rzO/797Z9gUwirBvmSanduh6CSI0BXwHvLg9eNIE8JE7IhrMRqL6PLUkCA5JwR8b8vtD9Nqd1sjk9AGxeRXM6J8v7d7I5nbqv5rRIy1YhdVy2PW+VyAAEgKvyfA/IZ2Q17fl/LFcBJAQMmYcAAAAASUVORK5CYII=')
            )
            ->setAdditionalImages([
                new Uri('https://sandbox.wizaplace.com/api/v1/doc/favicon.png'),
                (new ProductImageUpload())->setName('image2.png')
                    ->setMimeType('image/png')
                    ->setBase64Data('iVBORw0KGgoAAAANSUhEUgAAACAAAAAgCAYAAABzenr0AAAFkElEQVRYw72XS2wbVRSGvzszthPHcWYmzYOEvJo0IUlbmqZvWkDiISoVCVUUVapUIVZsWCAhgYQEQqxgBaqExIIVFQtAqCCxQYK2qKQtSUlTtSUNbp2kNImdxLHrOHHGj2Hha9dO3DZ9cSRr5Lln7jn3v/899z+CVVhTSxuADmwCtshnG7BGuswAPuA80A8MAeExv++uc4u7BBZAPbAPeE0G1u/wnQ2EZSLfAj8Bk2N+n33PCTS1tLmB/cDbwEZA494sCQwCnwHHxvy+hVUlIOGuBz4EDgFuHsxiwDfAR8CN5duiFgneCnwhIXfy4OYEeoBO4LRumOFIOJQbVJY5Pw4cAfYWGXsQU+ScRyS6KxFoamkrAz4BXn3IwfO3uw3w6IZ5PBIOJXIJSLYfBN57SLDfKYku4JpumBci4VBupQ2S7W4evbllrAYAVa7+sPwp/D9WBYzqhtmv6oZpAh8DzS6hsMep0+uoYDadIGanbk/tci8Nz7yI0dHNQnCK1FL8tr5ltfU0v/AypZVVLAQnsVNJBfAAP2pAL/AkwC6HzgeetZQIlU6tjE9jo1h2uuhGtj63j+ZXDoIQONweRn44CvbKgqc6nXQcOEzt1qdIJSxSS3GmBvqQMXsVYCtQDlAqFJxCQchkmtXSoisyFQe73FUIVUUoCjVbdlKim0V9PfWNmJ0bQAgUTUNxOLJD5cBWRRYJAXA+EWUitQSAoTjY7qgoOmmP5uX54XFKY4s5iM2O7qK+1T3bcXq8ACwEA8z5hvOB7FGA9uybqbTF2UQkN/q008ArCq8ADcFup07DRJBG/3imymgaNb0781cHgKPMw5r1PSAyFX96qJ/47HS+S7sClGX/pbE5boWYl+Rbp7rp1jwFk9apLjY5ytESSZ64MIyayvianRvw1DUU+Fasbcfb2JK5meKLBAf/xE4XcKpsxbG7nIxxKTkPQIlQ2O3UUfPurB0OnWolU6vMv6/A1CQALq+eWW0WX6FQs3kHqqsEgIj/H8LXRorW6Fj+i0U7xUlrjhR2LmCt6soltMupo8iE/pwZ5+r5s9mI1PTuxFGWQcxlmFR2bcyIBDtNYKCP5OKKGzmmACvSOm2FuSHJWKs66dUyJGpT3XRpZTLRNL8vhZjoP4UVvQmAt7EFvbUDgMqujbhrHgMgHppl5uJgMY6OKFI0FBzgQB4ZVQTPOg1Khcoep0G5JOWVZIzLyRg3x0eZG7mU8XWVUN2zDdXpoqZnO4qa8Z25OEgsMFlMPQ0qUsNF80fS2PxmzRKVZFzv8LDF4WWbowIhvzxphYjaSdIJi6n+PtLJZKbGbuhlzfpN6O1dAKSsJQIDfdipFVU1CvQrwDkpIgtsOI+MXqHxemkda2Vhmk5bnJEIAcxcGmR+4joApVXVrNt/CFeFkYlyfZSw70ox+IeAcwowJwVkqpCMaU5YoRwZuzUPLpE5NP2JCNdTt2r/UiTM9FA/2DZCUfE2tSKEANsmMHAaaz66PHhKxpxTpGI9VgyFU9YcI8lC5s7bKX5ZmiWZTxvbZqLvBIszwQLfheAkgb/OLKdYdvXHxvw+WwXQDTMqj+NLQK6cLdhpgmmLNs1NqVCZsxMcjU/yax4yWUtEo1jzN/HUNaCoGrHABCPff50jaH5ewPvAH5Fw6FaFkTL8c+CNfF0ggCrFSa3iImInuZGKF64+/5YUApdRSYlhEg/NEg+Hlt+QaeBL4J2sTBfLVHG9dNj7CMRJGvgZeHPM75soKssj4VBUN8w+YJ0UkOIhB39rzO/797Z9gUwirBvmSanduh6CSI0BXwHvLg9eNIE8JE7IhrMRqL6PLUkCA5JwR8b8vtD9Nqd1sjk9AGxeRXM6J8v7d7I5nbqv5rRIy1YhdVy2PW+VyAAEgKvyfA/IZ2Q17fl/LFcBJAQMmYcAAAAASUVORK5CYII='),
            ])
            ->setFullDescription("super full description 2")
            ->setShortDescription("super short description 2")
            ->setTaxIds([2, 3])
            ->setDeclinations([
                (new ProductDeclinationUpsertData([1 => 1, 2 => 5, 3 => 7]))
                    ->setCode('code_full_declA')
                    ->setPrice(3.6)
                    ->setQuantity(13),
                (new ProductDeclinationUpsertData([1 => 1, 2 => 6, 3 => 9]))
                    ->setPrice(100.1)
                    ->setCrossedOutPrice(1000.2)
                    ->setQuantity(4),
            ])
            ->setAttachments([new ProductAttachmentUpload('favicon2', 'https://sandbox.wizaplace.com/api/v1/doc/favicon.png')]);
        $newProductId = $productService->updateProduct($data);
        $this->assertSame($productId, $newProductId);

        $product = $productService->getProductById($productId);
        $this->assertInstanceOf(Product::class, $product);

        $this->assertSame($productId, $product->getId());
        $this->assertSame(4, $product->getMainCategoryId());
        $this->assertSame("code_full2", $product->getCode());
        $this->assertSame("Full product2", $product->getName());
        $this->assertSame('supplierref_full2', $product->getSupplierReference());
        $this->assertSame("super full description 2", $product->getFullDescription());
        $this->assertSame("super short description 2", $product->getShortDescription());
        $this->assertFalse($product->isBrandNew());
        $this->assertFalse($product->hasFreeShipping());
        $this->assertSame([2, 3], $product->getTaxIds());
        $this->assertSame([
            'freeAttr1' => ['freeAttr1Value2'],
            'freeAttr2' => [43],
            'freeAttr3' => ['freeAttr3Value2', 43],
        ], $product->getFreeAttributes());
        $this->assertSame(0.2, $product->getGreenTax());
        $this->assertSame(0.3, $product->getWeight());
        $this->assertTrue(ProductStatus::DISABLED()->equals($product->getStatus()));
        $this->assertTrue(ProductApprovalStatus::PENDING()->equals($product->getApprovalStatus()));
        $this->assertFalse($product->isDownloadable());
        $this->assertRegExp('#/images/detailed/0/[^.]+.png#', (string) $product->getMainImage());
        $additionalImages = $product->getAdditionalImages();
        $this->assertCount(2, $additionalImages);
        $this->assertRegExp('#/images/detailed/0/[^.]+.png#', (string) $additionalImages[15]);
        $this->assertRegExp('#/images/detailed/0/[^.]+.png#', (string) $additionalImages[16]);
        $this->assertNotEquals((string) $additionalImages[15], (string) $additionalImages[16]);

        $attachments = $product->getAttachments();
        $this->assertContainsOnly(ProductAttachment::class, $attachments);
        $this->assertCount(1, $attachments);
        $this->assertSame('favicon', $attachments[0]->getLabel());
        $this->assertNotEmpty($attachments[0]->getId());

        // Checking declinations
        $declinations = $product->getDeclinations();
        $this->assertContainsOnly(ProductDeclination::class, $declinations);
        $this->assertCount(4, $declinations);

        $this->assertSame([1 => 1, 2 => 5, 3 => 7], $declinations[0]->getOptionsVariants());
        $this->assertSame('code_full_declA', $declinations[0]->getCode());
        $this->assertNull($declinations[0]->getAffiliateLink());
        $this->assertNull($declinations[0]->getCrossedOutPrice());
        $this->assertSame(13, $declinations[0]->getQuantity());
        $this->assertSame(3.6, $declinations[0]->getPrice());

        // empty declination generated automatically to complete the matrix
        $this->assertSame([1 => 1, 2 => 5, 3 => 9], $declinations[1]->getOptionsVariants());
        $this->assertSame(0.0, $declinations[1]->getPrice());
        $this->assertSame(0, $declinations[1]->getQuantity());
        $this->assertNull($declinations[1]->getCrossedOutPrice());
        $this->assertNull($declinations[1]->getAffiliateLink());
        $this->assertNull($declinations[1]->getCode());

        // empty declination generated automatically to complete the matrix
        $this->assertSame([1 => 1, 2 => 6, 3 => 7], $declinations[2]->getOptionsVariants());
        $this->assertSame(0.0, $declinations[2]->getPrice());
        $this->assertSame(0, $declinations[2]->getQuantity());
        $this->assertNull($declinations[2]->getCrossedOutPrice());
        $this->assertNull($declinations[2]->getAffiliateLink());
        $this->assertNull($declinations[2]->getCode());

        $this->assertSame([1 => 1, 2 => 6, 3 => 9], $declinations[3]->getOptionsVariants());
        $this->assertSame(100.1, $declinations[3]->getPrice());
        $this->assertSame(4, $declinations[3]->getQuantity());
        $this->assertSame(1000.2, $declinations[3]->getCrossedOutPrice());
        $this->assertNull($declinations[3]->getAffiliateLink());
        $this->assertNull($declinations[3]->getCode());
    }

    private function buildProductService($userEmail = 'admin@wizaplace.com', $userPassword = 'password'): ProductService
    {
        $apiClient = $this->buildApiClient();
        $apiClient->authenticate($userEmail, $userPassword);

        return new ProductService($apiClient);
    }
}
