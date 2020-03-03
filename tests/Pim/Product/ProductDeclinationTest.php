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
use Wizaplace\SDK\Pim\Product\ProductService;
use Wizaplace\SDK\Pim\Product\ProductStatus;
use Wizaplace\SDK\Pim\Product\ProductSummary;
use Wizaplace\SDK\Pim\Product\Shipping;
use Wizaplace\SDK\Pim\Product\UpdateProductCommand;
use Wizaplace\SDK\Pim\Product\UpdateShippingCommand;
use Wizaplace\SDK\Tests\ApiTestCase;

final class ProductDeclinationTest extends ApiTestCase
{
    public function testUpdateComplexProduct(): void
    {
        $data = (new CreateProductCommand())
            ->setCode("code_full_A_9")
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
        static::assertInternalType('int', $productId);
        static::assertGreaterThan(0, $productId);

        $data = (new UpdateProductCommand($productId))
            ->setCode("code_full_BB_9")
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
                    (new ProductDeclinationUpsertData([2 => 5, 3 => 7]))
                      ->setCode('code_full_declA')
                      ->setSupplierReference('SUPP_REF_03')
                      ->setPrice(3.6)
                      ->setQuantity(13),
                    (new ProductDeclinationUpsertData([2 => 6, 3 => 9]))
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

        $product = $productService->getProductById($newProductId);
        static::assertInstanceOf(Product::class, $product);

        static::assertSame($productId, $product->getId());
        static::assertSame(4, $product->getMainCategoryId());
        static::assertSame("code_full_BB_9", $product->getCode());
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

        static::assertSame([2 => 5, 3 => 7], $declinations[0]->getOptionsVariants());
        static::assertSame('code_full_declA', $declinations[0]->getCode());
        static::assertSame('SUPP_REF_03', $declinations[0]->getSupplierReference());
        static::assertNull($declinations[0]->getAffiliateLink());
        static::assertNull($declinations[0]->getCrossedOutPrice());
        static::assertSame(13, $declinations[0]->getQuantity());
        static::assertSame(3.6, $declinations[0]->getPrice());
        static::assertSame(0, $declinations[0]->getPriceTiers()[0]->getLowerLimit());
        static::assertSame(3.6, $declinations[0]->getPriceTiers()[0]->getPrice());

        // empty declination generated automatically to complete the matrix
        static::assertSame([2 => 5, 3 => 9], $declinations[1]->getOptionsVariants());
        static::assertSame(0.0, $declinations[1]->getPrice());
        static::assertSame(0, $declinations[1]->getPriceTiers()[0]->getLowerLimit());
        static::assertSame(0.0, $declinations[1]->getPriceTiers()[0]->getPrice());
        static::assertSame(0, $declinations[1]->getQuantity());
        static::assertNull($declinations[1]->getCrossedOutPrice());
        static::assertNull($declinations[1]->getAffiliateLink());
        static::assertNull($declinations[1]->getCode());

        // empty declination generated automatically to complete the matrix
        static::assertSame([2 => 6, 3 => 7], $declinations[2]->getOptionsVariants());
        static::assertSame(0.0, $declinations[2]->getPrice());
        static::assertSame(0, $declinations[2]->getPriceTiers()[0]->getLowerLimit());
        static::assertSame(0.0, $declinations[2]->getPriceTiers()[0]->getPrice());
        static::assertSame(0, $declinations[2]->getQuantity());
        static::assertNull($declinations[2]->getCrossedOutPrice());
        static::assertNull($declinations[2]->getAffiliateLink());
        static::assertNull($declinations[2]->getCode());

        static::assertSame([2 => 6, 3 => 9], $declinations[3]->getOptionsVariants());
        static::assertSame(99.99, $declinations[3]->getPrice());
        static::assertSame(0, $declinations[3]->getPriceTiers()[0]->getLowerLimit());
        static::assertSame(99.99, $declinations[3]->getPriceTiers()[0]->getPrice());
        static::assertSame(4, $declinations[3]->getQuantity());
        static::assertSame(1000.2, $declinations[3]->getCrossedOutPrice());
        static::assertNull($declinations[3]->getAffiliateLink());
        static::assertNull($declinations[3]->getCode());
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
}
