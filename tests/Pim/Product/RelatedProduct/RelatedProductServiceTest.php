<?php

/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Tests\Pim\Product\RelatedProduct;

use Wizaplace\SDK\Exception\AccessDenied;
use Wizaplace\SDK\Exception\Conflict;
use Wizaplace\SDK\Exception\NotFound;
use Wizaplace\SDK\Exception\SomeParametersAreInvalid;
use Wizaplace\SDK\Pim\Product\ProductService;
use Wizaplace\SDK\Pim\Product\RelatedProduct\RelatedProduct;
use Wizaplace\SDK\Pim\Product\RelatedProduct\RelatedProductService;
use Wizaplace\SDK\Pim\Product\RelatedProduct\RelatedProductsType;
use Wizaplace\SDK\Tests\ApiTestCase;

class RelatedProductServiceTest extends ApiTestCase
{
    public function testAddRelatedProductSuccess(): void
    {
        $relatedProductService = $this->buildRelatedProductService();

        $relatedProduct = new RelatedProduct(
            [
                'productId' => 6,
                'type' => RelatedProductsType::RECOMMENDED,
                'description' => 'Recommended product.',
                'extra' => '123_456',
            ]
        );

        // Create a related product success
        $result = $relatedProductService->addRelatedProduct(8, $relatedProduct);

        $this->assertEquals($relatedProduct, $result);
    }

    public function testAddRelatedProductThrowsAccessDenied(): void
    {
        $this->expectException(AccessDenied::class);

        $relatedProductService = $this->buildRelatedProductService('user@wizaplace.com');

        $relatedProduct = new RelatedProduct(
            [
                'productId' => 6,
                'type' => RelatedProductsType::RECOMMENDED,
                'description' => 'Recommended product.',
                'extra' => '123_456',
            ]
        );

        // Create a related product throws an exception
        $relatedProductService->addRelatedProduct(8, $relatedProduct);
    }

    public function testAddRelatedProductThrowsConflict(): void
    {
        $this->expectException(Conflict::class);

        $relatedProductService = $this->buildRelatedProductService();

        $relatedProduct = new RelatedProduct(
            [
                'productId' => 6,
                'type' => RelatedProductsType::RECOMMENDED,
                'description' => 'Recommended product.',
                'extra' => '123_456',
            ]
        );

        // Create a related product
        $relatedProductService->addRelatedProduct(7, $relatedProduct);

        // Create the same related product. It throws an exception.
        $relatedProductService->addRelatedProduct(7, $relatedProduct);
    }

    public function testAddRelatedProductThrowsNotFound(): void
    {
        $this->expectException(NotFound::class);

        $relatedProductService = $this->buildRelatedProductService();

        $relatedProduct = new RelatedProduct(
            [
                'productId' => 6,
                'type' => RelatedProductsType::RECOMMENDED,
                'description' => 'Recommended product.',
                'extra' => '123_456',
            ]
        );

        // Create a related product throws an exception
        $relatedProductService->addRelatedProduct(999999, $relatedProduct);
    }

    public function dataProviderSomeParametersAreInvalid(): array
    {
        return [
            'from_product equals to to_product' => [1, 1, RelatedProductsType::RECOMMENDED],
            'invalid type' => [1, 2, 'invalid'],
            'valid type but not available' => [1, 2, RelatedProductsType::OTHER],
        ];
    }

    /** @dataProvider dataProviderSomeParametersAreInvalid */
    public function testAddRelatedProductThrowsSomeParametersAreInvalid(
        int $fromProductId,
        int $toProductId,
        string $type
    ): void {
        $this->expectException(SomeParametersAreInvalid::class);

        $relatedProductService = $this->buildRelatedProductService();

        $relatedProduct = new RelatedProduct(
            [
                'productId' => $toProductId,
                'type' => $type,
                'description' => 'Recommended product',
                'extra' => '123_123',
            ]
        );

        // Create a related product will throw an exception
        $relatedProductService->addRelatedProduct($fromProductId, $relatedProduct);
    }

    private function createRelatedProducts(
        RelatedProductService $relatedProductService,
        int $fromProductId
    ): void {
        $relatedProduct = new RelatedProduct(
            [
                'productId' => 6,
                'type' => RelatedProductsType::RECOMMENDED,
                'description' => 'Recommended product.',
                'extra' => '123_456',
            ]
        );

        $relatedProductService->addRelatedProduct($fromProductId, $relatedProduct);

        $relatedProduct = new RelatedProduct(
            [
                'productId' => 7,
                'type' => RelatedProductsType::RECOMMENDED,
                'description' => 'Recommended product.',
                'extra' => '123_456',
            ]
        );

        $relatedProductService->addRelatedProduct($fromProductId, $relatedProduct);

        $relatedProduct = new RelatedProduct(
            [
                'productId' => 7,
                'type' => RelatedProductsType::SHIPPING,
                'description' => 'Shipping product.',
                'extra' => '123_456',
            ]
        );

        $relatedProductService->addRelatedProduct($fromProductId, $relatedProduct);

        $relatedProduct = new RelatedProduct(
            [
                'productId' => 7,
                'type' => RelatedProductsType::OTHER,
                'description' => 'Other product.',
                'extra' => '123_456',
            ]
        );

        $relatedProductService->addRelatedProduct($fromProductId, $relatedProduct);
    }

    public function testDeleteRelatedProductWithRelatedProductIdAndTypeSuccess(): void
    {
        $relatedProductService = $this->buildRelatedProductService();

        $fromProductId = 1;

        // Create multiple related products
        $this->createRelatedProducts($relatedProductService, $fromProductId);

        $productService = $this->buildProductService();
        $product = $productService->getProductById($fromProductId);

        $this->assertEquals(
            [
                new RelatedProduct(
                    [
                        'type' => RelatedProductsType::RECOMMENDED,
                        'productId' => 6,
                        'description' => 'Recommended product.',
                        'extra' => '123_456',
                    ]
                ),
                new RelatedProduct(
                    [
                        'type' => RelatedProductsType::RECOMMENDED,
                        'productId' => 7,
                        'description' => 'Recommended product.',
                        'extra' => '123_456',
                    ]
                ),
                new RelatedProduct(
                    [
                        'type' => RelatedProductsType::SHIPPING,
                        'productId' => 7,
                        'description' => 'Shipping product.',
                        'extra' => '123_456',
                    ]
                ),
                new RelatedProduct(
                    [
                        'type' => RelatedProductsType::OTHER,
                        'productId' => 7,
                        'description' => 'Other product.',
                        'extra' => '123_456',
                    ]
                ),
            ],
            $product->getRelated()
        );

        $relatedProductService->deleteRelatedProductWithRelatedProductIdAndType(
            $fromProductId,
            7,
            RelatedProductsType::RECOMMENDED
        );

        $product = $productService->getProductById($fromProductId);

        $this->assertEquals(
            [
                new RelatedProduct(
                    [
                        'type' => RelatedProductsType::RECOMMENDED,
                        'productId' => 6,
                        'description' => 'Recommended product.',
                        'extra' => '123_456',
                    ]
                ),
                new RelatedProduct(
                    [
                        'type' => RelatedProductsType::SHIPPING,
                        'productId' => 7,
                        'description' => 'Shipping product.',
                        'extra' => '123_456',
                    ]
                ),
                new RelatedProduct(
                    [
                        'type' => RelatedProductsType::OTHER,
                        'productId' => 7,
                        'description' => 'Other product.',
                        'extra' => '123_456',
                    ]
                ),
            ],
            $product->getRelated()
        );
    }

    public function testDeleteRelatedProductWithRelatedProductIdSuccess(): void
    {
        $relatedProductService = $this->buildRelatedProductService();

        $fromProductId = 1;

        // Create multiple related products
        $this->createRelatedProducts($relatedProductService, $fromProductId);

        $productService = $this->buildProductService();
        $product = $productService->getProductById($fromProductId);

        $this->assertEquals(
            [
                new RelatedProduct(
                    [
                        'type' => RelatedProductsType::RECOMMENDED,
                        'productId' => 6,
                        'description' => 'Recommended product.',
                        'extra' => '123_456',
                    ]
                ),
                new RelatedProduct(
                    [
                        'type' => RelatedProductsType::RECOMMENDED,
                        'productId' => 7,
                        'description' => 'Recommended product.',
                        'extra' => '123_456',
                    ]
                ),
                new RelatedProduct(
                    [
                        'type' => RelatedProductsType::SHIPPING,
                        'productId' => 7,
                        'description' => 'Shipping product.',
                        'extra' => '123_456',
                    ]
                ),
                new RelatedProduct(
                    [
                        'type' => RelatedProductsType::OTHER,
                        'productId' => 7,
                        'description' => 'Other product.',
                        'extra' => '123_456',
                    ]
                ),
            ],
            $product->getRelated()
        );

        $relatedProductService->deleteRelatedProductWithRelatedProductId(
            $fromProductId,
            7
        );

        $product = $productService->getProductById($fromProductId);

        $this->assertEquals(
            [
                new RelatedProduct(
                    [
                        'type' => RelatedProductsType::RECOMMENDED,
                        'productId' => 6,
                        'description' => 'Recommended product.',
                        'extra' => '123_456',
                    ]
                ),
            ],
            $product->getRelated()
        );
    }

    public function testDeleteRelatedProductWithTypeSuccess(): void
    {
        $relatedProductService = $this->buildRelatedProductService();

        $fromProductId = 1;

        // Create multiple related products
        $this->createRelatedProducts($relatedProductService, $fromProductId);

        $productService = $this->buildProductService();
        $product = $productService->getProductById($fromProductId);

        $this->assertEquals(
            [
                new RelatedProduct(
                    [
                        'type' => RelatedProductsType::RECOMMENDED,
                        'productId' => 6,
                        'description' => 'Recommended product.',
                        'extra' => '123_456',
                    ]
                ),
                new RelatedProduct(
                    [
                        'type' => RelatedProductsType::RECOMMENDED,
                        'productId' => 7,
                        'description' => 'Recommended product.',
                        'extra' => '123_456',
                    ]
                ),
                new RelatedProduct(
                    [
                        'type' => RelatedProductsType::SHIPPING,
                        'productId' => 7,
                        'description' => 'Shipping product.',
                        'extra' => '123_456',
                    ]
                ),
                new RelatedProduct(
                    [
                        'type' => RelatedProductsType::OTHER,
                        'productId' => 7,
                        'description' => 'Other product.',
                        'extra' => '123_456',
                    ]
                ),
            ],
            $product->getRelated()
        );

        $relatedProductService->deleteRelatedProductWithType(
            $fromProductId,
            RelatedProductsType::RECOMMENDED
        );

        $product = $productService->getProductById($fromProductId);

        $this->assertEquals(
            [
                new RelatedProduct(
                    [
                        'type' => RelatedProductsType::SHIPPING,
                        'productId' => 7,
                        'description' => 'Shipping product.',
                        'extra' => '123_456',
                    ]
                ),
                new RelatedProduct(
                    [
                        'type' => RelatedProductsType::OTHER,
                        'productId' => 7,
                        'description' => 'Other product.',
                        'extra' => '123_456',
                    ]
                ),
            ],
            $product->getRelated()
        );
    }

    public function testAllDeleteRelatedProductSuccess(): void
    {
        $relatedProductService = $this->buildRelatedProductService();

        $fromProductId = 1;

        // Create multiple related products
        $this->createRelatedProducts($relatedProductService, $fromProductId);

        $productService = $this->buildProductService();
        $product = $productService->getProductById($fromProductId);

        $this->assertEquals(
            [
                new RelatedProduct(
                    [
                        'type' => RelatedProductsType::RECOMMENDED,
                        'productId' => 6,
                        'description' => 'Recommended product.',
                        'extra' => '123_456',
                    ]
                ),
                new RelatedProduct(
                    [
                        'type' => RelatedProductsType::RECOMMENDED,
                        'productId' => 7,
                        'description' => 'Recommended product.',
                        'extra' => '123_456',
                    ]
                ),
                new RelatedProduct(
                    [
                        'type' => RelatedProductsType::SHIPPING,
                        'productId' => 7,
                        'description' => 'Shipping product.',
                        'extra' => '123_456',
                    ]
                ),
                new RelatedProduct(
                    [
                        'type' => RelatedProductsType::OTHER,
                        'productId' => 7,
                        'description' => 'Other product.',
                        'extra' => '123_456',
                    ]
                ),
            ],
            $product->getRelated()
        );

        $relatedProductService->deleteAllRelatedProduct($fromProductId);

        $product = $productService->getProductById($fromProductId);

        $this->assertEquals(
            [],
            $product->getRelated()
        );
    }

    public function testDeleteRelatedProductThrowsAccessDenied(): void
    {
        $this->expectException(AccessDenied::class);

        $relatedProductService = $this->buildRelatedProductService('user@wizaplace.com');

        $relatedProductService->deleteRelatedProductWithRelatedProductIdAndType(
            2,
            7,
            RelatedProductsType::RECOMMENDED
        );
    }

    public function testDeleteRelatedProductThrowsNotFound(): void
    {
        $this->expectException(NotFound::class);

        $relatedProductService = $this->buildRelatedProductService();

        $relatedProductService->deleteRelatedProductWithRelatedProductIdAndType(
            2,
            7,
            RelatedProductsType::RECOMMENDED
        );
    }

    public function dataProviderDeleteSomeParametersAreInvalid(): array
    {
        return [
            'from product not found' => [999999, 1, RelatedProductsType::RECOMMENDED],
            'to product not found' => [1, 999999, RelatedProductsType::RECOMMENDED],
            'invalid type' => [1, 2, 'invalid'],
            'valid type but not available' => [1, 2, RelatedProductsType::OTHER],
        ];
    }

    /** @dataProvider dataProviderDeleteSomeParametersAreInvalid */
    public function testDeleteRelatedProductThrowsSomeParametersAreInvalid(): void
    {
        $this->expectException(SomeParametersAreInvalid::class);

        $relatedProductService = $this->buildRelatedProductService();

        $relatedProductService->deleteRelatedProductWithRelatedProductIdAndType(
            999888,
            7,
            RelatedProductsType::RECOMMENDED
        );
    }

    private function buildRelatedProductService(
        $userEmail = 'admin@wizaplace.com',
        $userPassword = 'password'
    ): RelatedProductService {
        $apiClient = $this->buildApiClient();
        $apiClient->authenticate($userEmail, $userPassword);

        return new RelatedProductService($apiClient);
    }

    private function buildProductService(
        $userEmail = 'admin@wizaplace.com',
        $userPassword = 'password'
    ): ProductService {
        $apiClient = $this->buildApiClient();
        $apiClient->authenticate($userEmail, $userPassword);

        return new ProductService($apiClient);
    }
}
