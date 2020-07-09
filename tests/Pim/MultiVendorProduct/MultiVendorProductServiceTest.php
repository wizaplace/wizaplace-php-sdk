<?php

/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Tests\Pim\MultiVendorProduct;

use Wizaplace\SDK\Exception\SomeParametersAreInvalid;
use Wizaplace\SDK\File\File;
use Wizaplace\SDK\Image\Image;
use Wizaplace\SDK\Pagination;
use Wizaplace\SDK\Pim\MultiVendorProduct\MultiVendorProduct;
use Wizaplace\SDK\Pim\MultiVendorProduct\MultiVendorProductFile;
use Wizaplace\SDK\Pim\MultiVendorProduct\MultiVendorProductFilter;
use Wizaplace\SDK\Pim\MultiVendorProduct\MultiVendorProductList;
use Wizaplace\SDK\Pim\MultiVendorProduct\MultiVendorProductService;
use Wizaplace\SDK\Pim\MultiVendorProduct\MultiVendorProductStatus;
use Wizaplace\SDK\Pim\MultiVendorProduct\MultiVendorProductVideo;
use Wizaplace\SDK\Tests\ApiTestCase;
use Wizaplace\SDK\Tests\File\Mock;

final class MultiVendorProductServiceTest extends ApiTestCase
{
    private $createdMvpId;

    private const MVP_ID = 'a6e53f40-f4c5-3d56-af1d-cc83fd695feb';

    public function testGetMultiVendorProductById()
    {
        $service = $this->buildMultiVendorProductService();

        $mvp = $service->getMultiVendorProductById('a6e53f40-f4c5-3d56-af1d-cc83fd695feb');

        $this->assertInstanceOf(MultiVendorProduct::class, $mvp);
        $this->assertSame('a6e53f40-f4c5-3d56-af1d-cc83fd695feb', $mvp->getId());
        $this->assertSame('Test MVP', $mvp->getName());
        $this->assertSame('product', $mvp->getProductTemplateType());
        $this->assertSame('20230495447', $mvp->getCode());
        $this->assertSame('TEST-MVP', $mvp->getSupplierReference());
        $this->assertSame('hic-veritatis-eaque-error-consectetur-est-cumque-reiciendis-aspernatur', $mvp->getSlug());
        $this->assertSame('Et odio nobis aut est et. Sapiente quia dicta reprehenderit quaerat. Laboriosam magnam enim sunt atque.', $mvp->getShortDescription());
        $this->assertSame('Aliquam enim blanditiis dolorem voluptate ex. Minima in blanditiis quisquam. Impedit et dolorem non perferendis. Est qui quibusdam exercitationem consequatur doloribus. Velit impedit quo ut temporibus. Qui et molestiae facilis nisi necessitatibus repudiandae eos. Vel omnis sit ut recusandae quis enim suscipit. Nam qui velit consequatur ad rerum natus. Officiis accusantium veniam pariatur ut ad dignissimos. Ut maxime quas a cupiditate impedit in aut. Odio saepe et doloremque. Quidem non ipsam et corrupti.', $mvp->getDescription());
        $this->assertSame('', $mvp->getSeoTitle());
        $this->assertSame('', $mvp->getSeoDescription());
        $this->assertSame('', $mvp->getSeoKeywords());
        $this->assertTrue(MultiVendorProductStatus::ENABLED()->equals($mvp->getStatus()));
        $this->assertSame(6, $mvp->getCategoryId());
        $this->assertSame(
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
            $mvp->getFreeAttributes()
        );
        $this->assertSame(
            [
                3 => 'Commentaire #12M%M_°09£*/.?',
                1 => [
                    0 => 2,
                    1 => 3,
                ],
                7 => 'Complément d\'adresse pour la stepo',
                5 => [
                    0 => 9,
                ],
                6 => 17,
                9 => 20,
                2 => 5,
            ],
            $mvp->getAttributes()
        );
        $this->assertSame([], $mvp->getImageIds());
    }

    public function testGetListMultiVendorProductByFilter()
    {
        $service = $this->buildMultiVendorProductService();

        $filter = (new MultiVendorProductFilter())
            ->setIds([self::MVP_ID, 'df8f3982-f9f0-49ec-a489-2903b4e3dace'])
            ->setCodes(['Fire_trotti', 'EAN'])
            ->setSupplierReferences(['REF_TROTTI'])
        ;
        $mvpList = $service->getListMultiVendorProduct($filter, 1, 2);

        $arrayOptions['requestedNbResult'] = 5;
        $arrayOptions['requestedNbPages'] = 3;
        $arrayOptions['requestedNbProducts'] = 2;

        $this->assertMultiVendorProductFilter($mvpList, $arrayOptions);

        foreach ($mvpList->getMultiVendorProducts() as $mvp) {
            static::assertInstanceOf(MultiVendorProduct::class, $mvp);
        }
    }

    public function testGetListMultiVendorProductByDates(): void
    {
        $service = $this->buildMultiVendorProductService();

        // Test avec 2 dates OK
        $filter = (new MultiVendorProductFilter())
            ->setUpdatedBefore((new \DateTime('2030-08-06'))->format(\DateTime::RFC3339))
            ->setUpdatedAfter((new \DateTime('1989-08-06'))->format(\DateTime::RFC3339));

        $mvpList = $service->getListMultiVendorProduct($filter, 1, 1);

        $arrayOptions['requestedNbResult'] = 1;
        $arrayOptions['requestedNbPages'] = 1;
        $arrayOptions['requestedNbProducts'] = 1;


        foreach ($mvpList->getMultiVendorProducts() as $mvp) {
            static::assertInstanceOf(MultiVendorProduct::class, $mvp);
        }
    }

    public function testGetListMultiVendorProductByWrongDates(): void
    {
        $service = $this->buildMultiVendorProductService();

        // Test avec deux dates non OK
        $filter = (new MultiVendorProductFilter())
            ->setUpdatedBefore((new \DateTime('1989-08-06'))->format(\DateTime::RFC3339))
            ->setUpdatedAfter((new \DateTime('2030-08-06'))->format(\DateTime::RFC3339));

        $mvpList = $service->getListMultiVendorProduct($filter, 1, 1);

        $arrayOptions['requestedNbResult'] = 0;
        $arrayOptions['requestedNbPages'] = 0;
        $arrayOptions['requestedNbProducts'] = 0;

        $this->assertMultiVendorProductFilter($mvpList, $arrayOptions);
    }

    public function testGetListMultiVendorProductByUpdateDateIsBefore(): void
    {
        $service = $this->buildMultiVendorProductService();

        // Test avec date > ok
        $filter = (new MultiVendorProductFilter())
            ->setUpdatedBefore((new \DateTime('2030-08-06'))->format(\DateTime::RFC3339));

        $mvpList = $service->getListMultiVendorProduct($filter, 1, 1);

        $arrayOptions['requestedNbResult'] = 1;
        $arrayOptions['requestedNbPages'] = 1;
        $arrayOptions['requestedNbProducts'] = 1;

        $this->assertMultiVendorProductFilter($mvpList, $arrayOptions);
        static::assertInstanceOf(MultiVendorProduct::class, $mvpList->getMultiVendorProducts()[0]);
    }

    public function testGetListMultiVendorProductByUpdateDateIsAfter(): void
    {
        $service = $this->buildMultiVendorProductService();

        // Test avec date < ok
        $filter = (new MultiVendorProductFilter())
            ->setUpdatedAfter((new \DateTime('1989-08-06'))->format(\DateTime::RFC3339));

        $mvpList = $service->getListMultiVendorProduct($filter, 1, 1);
        $arrayOptions['requestedNbResult'] = 1;
        $arrayOptions['requestedNbPages'] = 1;
        $arrayOptions['requestedNbProducts'] = 1;

        $this->assertMultiVendorProductFilter($mvpList, $arrayOptions);
        static::assertInstanceOf(MultiVendorProduct::class, $mvpList->getMultiVendorProducts()[0]);
    }

    public function testGetListMultiVendorProductByCategories(): void
    {
        $service = $this->buildMultiVendorProductService();

        $filter = (new MultiVendorProductFilter())
            ->setCategoryId(array('5', '6', '7'));
        $mvpList = $service->getListMultiVendorProduct($filter, 1, 1);

        static::assertInstanceOf(MultiVendorProduct::class, $mvpList->getMultiVendorProducts()[0]);
    }

    public function testCreateMultiVendorProductWithInvalidPartialProduct()
    {
        $service = $this->buildMultiVendorProductService();

        $newMvp = new MultiVendorProduct(
            [
                'supplierReference' => 'ThisProductIsNotValid',
            ]
        );

        $this->expectException(SomeParametersAreInvalid::class);
        $id = $service->createMultiVendorProduct($newMvp);
    }

    public function testCreateMultiVendorProductWithFullProduct()
    {
        $service = $this->buildMultiVendorProductService();

        $newMvp = new MultiVendorProduct(
            [
                'name' => 'New Test MVP',
                'code' => 'XXX-xxx-XXX',
                'supplierReference' => 'AZAZPMPKKD23',
                'productTemplateType' => 'product',
                'slug' => 'new-test-mvp',
                'shortDescription' => 'Nihil est qui quibusdam exercitationem',
                'description' => 'Nihil est qui quibusdam exercitationem consequatur doloribus sit velit. Ut temporibus est qui et molestiae facilis nisi',
                'seoTitle' => 'New Test MVP',
                'seoDescription' => 'New Test MVP Nihil est qui quibusdam exercitationem',
                'seoKeywords' => 'MVP, PRODUCT, TEST',
                'status' => 'A',
                'categoryId' => 3,
                'freeAttributes' => [
                    'Free attribute multiple' => [
                        'Num 1',
                        'Num 2',
                        51,
                    ],
                    'Free attribute simple' => [
                        'Bla',
                    ],
                ],
                'attributes' => [],
            ]
        );

        $id = $service->createMultiVendorProduct($newMvp);

        $this->assertNotNull($id);

        $mvp = $service->getMultiVendorProductById($id);

        $this->assertSame($id, $mvp->getId());
        $this->assertSame('New Test MVP', $mvp->getName());
        $this->assertSame('XXX-xxx-XXX', $mvp->getCode());
        $this->assertSame('AZAZPMPKKD23', $mvp->getSupplierReference());
        $this->assertSame('product', $mvp->getProductTemplateType());
        $this->assertSame('new-test-mvp', $mvp->getSlug());
        $this->assertSame('Nihil est qui quibusdam exercitationem', $mvp->getShortDescription());
        $this->assertSame('Nihil est qui quibusdam exercitationem consequatur doloribus sit velit. Ut temporibus est qui et molestiae facilis nisi', $mvp->getDescription());
        $this->assertSame('New Test MVP', $mvp->getSeoTitle());
        $this->assertSame('New Test MVP Nihil est qui quibusdam exercitationem', $mvp->getSeoDescription());
        $this->assertSame('MVP, PRODUCT, TEST', $mvp->getSeoKeywords());
        $this->assertTrue(MultiVendorProductStatus::ENABLED()->equals($mvp->getStatus()));
        $this->assertSame(3, $mvp->getCategoryId());
        $this->assertSame(
            [
                'Free attribute multiple' => [
                    'Num 1',
                    'Num 2',
                    51,
                ],
                'Free attribute simple' => [
                    'Bla',
                ],
            ],
            $mvp->getFreeAttributes()
        );
        $this->assertSame([], $mvp->getAttributes());
        $this->assertSame([], $mvp->getImageIds());
    }

    public function testUpdateMultiVendorProduct()
    {
        $service = $this->buildMultiVendorProductService();

        $mvp = $service->getMultiVendorProductById('0adaf6bc-d362-34be-b72f-42d5aa3b4a4e');

        $mvp->setName('Plop plop');
        $mvp->setStatus(MultiVendorProductStatus::HIDDEN());
        $mvp->setSlug('plop-plop');

        $service->updateMultiVendorProduct($mvp);

        $updatedMvp = $service->getMultiVendorProductById('0adaf6bc-d362-34be-b72f-42d5aa3b4a4e');
        $this->assertSame('Plop plop', $updatedMvp->getName());
        $this->assertTrue(MultiVendorProductStatus::HIDDEN()->equals($updatedMvp->getStatus()));
        $this->assertSame('plop-plop', $updatedMvp->getSlug());
        $this->assertSame('product', $updatedMvp->getProductTemplateType());
        $this->assertSame('Consectetur est cumque reiciendis aspernatur incidunt voluptatem. Odio nobis aut est et.', $updatedMvp->getShortDescription());
        $this->assertSame('Sunt sunt et atque quae aperiam voluptas. Iure aliquam enim blanditiis dolorem. Ex eaque minima in blanditiis quisquam. Impedit et dolorem non perferendis. Nihil est qui quibusdam exercitationem consequatur doloribus sit velit. Ut temporibus est qui et molestiae facilis nisi. Repudiandae eos pariatur vel omnis sit ut recusandae. Enim suscipit cum nam qui velit. Ad rerum natus cupiditate laborum officiis. Pariatur ut ad dignissimos omnis. Maxime quas a cupiditate impedit.', $updatedMvp->getDescription());
        $this->assertSame('', $updatedMvp->getSeoTitle());
        $this->assertSame('', $updatedMvp->getSeoDescription());
        $this->assertSame('', $updatedMvp->getSeoKeywords());
        $this->assertSame(6, $updatedMvp->getCategoryId());
        $this->assertSame(
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
            $updatedMvp->getFreeAttributes()
        );
        $this->assertSame(
            [
                3 => 'Commentaire #12M%M_°09£*/.?',
                1 => [
                    0 => 2,
                    1 => 3,
                ],
                7 => 'Complément d\'adresse pour la stepo',
                5 => [
                    0 => 9,
                ],
                6 => 17,
                9 => 20,
                2 => 5,
            ],
            $updatedMvp->getAttributes()
        );
        $this->assertSame([], $updatedMvp->getImageIds());
    }

    public function testAddImageToMultiVendorProduct()
    {
        $service = $this->buildMultiVendorProductService();
        $uuid = '8f95c0ad-e1e2-4587-9f24-ba504d5b45f3';

        $image = $this->mockUploadedFile("favicon.png");

        $files = [
            new MultiVendorProductFile('file', $image->getStream(), $image->getClientFilename()),
        ];

        $multiVendorProduct = $service->addImageToMultiVendorProduct($uuid, $files);

        $this->assertInstanceOf(MultiVendorProduct::class, $multiVendorProduct);
        $this->assertEquals($uuid, $multiVendorProduct->getId());
    }

    public function testAddImageToMultiVendorProductWithAlt()
    {
        $service = $this->buildMultiVendorProductService();

        $newMvp = new MultiVendorProduct(
            [
                'name' => 'New Test MVP',
                'code' => 'XXX-xxx-XXX',
                'supplierReference' => 'AZAZPMPKKD23',
                'productTemplateType' => 'product',
                'slug' => 'new-test-mvp',
                'shortDescription' => 'Nihil est qui quibusdam exercitationem',
                'description' => 'Nihil est qui quibusdam exercitationem consequatur doloribus sit velit. Ut temporibus est qui et molestiae facilis nisi',
                'seoTitle' => 'New Test MVP',
                'seoDescription' => 'New Test MVP Nihil est qui quibusdam exercitationem',
                'seoKeywords' => 'MVP, PRODUCT, TEST',
                'status' => 'A',
                'categoryId' => 3,
                'freeAttributes' => [
                    'Free attribute multiple' => [
                        'Num 1',
                        'Num 2',
                        51,
                    ],
                    'Free attribute simple' => [
                        'Bla',
                    ],
                ],
                'attributes' => [],
            ]
        );

        $uuid = $service->createMultiVendorProduct($newMvp);

        $image = $this->mockUploadedFile("favicon.png");

        $files = [
            new MultiVendorProductFile('file', $image->getStream(), $image->getClientFilename()),
        ];

        $multiVendorProduct = $service->addImageToMultiVendorProduct($uuid, $files, 'alternative text mvp');

        static::assertInstanceOf(MultiVendorProduct::class, $multiVendorProduct);
        static::assertInstanceOf(Image::class, $multiVendorProduct->getImagesData()[0]);
        static::assertSame('alternative text mvp', $multiVendorProduct->getImagesData()[0]->getAlt());
    }

    public function testGetMultiVendorProductByIdWithImagesAlt()
    {
        $service = $this->buildMultiVendorProductService();

        $newMvp = new MultiVendorProduct(
            [
                'name' => 'New Test MVP',
                'code' => 'XXX-xxx-XXX',
                'supplierReference' => 'AZAZPMPKKD23',
                'productTemplateType' => 'product',
                'slug' => 'new-test-mvp',
                'shortDescription' => 'Nihil est qui quibusdam exercitationem',
                'description' => 'Nihil est qui quibusdam exercitationem consequatur doloribus sit velit. Ut temporibus est qui et molestiae facilis nisi',
                'seoTitle' => 'New Test MVP',
                'seoDescription' => 'New Test MVP Nihil est qui quibusdam exercitationem',
                'seoKeywords' => 'MVP, PRODUCT, TEST',
                'status' => 'A',
                'categoryId' => 3,
                'freeAttributes' => [
                    'Free attribute multiple' => [
                        'Num 1',
                        'Num 2',
                        51,
                    ],
                    'Free attribute simple' => [
                        'Bla',
                    ],
                ],
                'attributes' => [],
            ]
        );

        $uuid = $service->createMultiVendorProduct($newMvp);

        $image = $this->mockUploadedFile("favicon.png");

        $files = [
            new MultiVendorProductFile('file', $image->getStream(), $image->getClientFilename()),
        ];

        $multiVendorProduct = $service->addImageToMultiVendorProduct($uuid, $files, 'alternative text mvp');

        $mvp = $service->getMultiVendorProductById($multiVendorProduct->getId());

        static::assertSame('alternative text mvp', $mvp->getImagesData()[0]->getAlt());
    }

    public function testAddVideoToMultiVendorProductWithHostedFile()
    {
        $service = $this->buildMultiVendorProductService();
        $file = 'http://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ForBiggerEscapes.mp4';
        $multiVendorProductVideo = $service->addHostedVideoToMultiVendorProduct(static::MVP_ID, $file);

        $this->assertInstanceOf(MultiVendorProductVideo::class, $multiVendorProductVideo);
        $this->assertRegExp(
            '/[0-9A-Fa-f]{8}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{12}/',
            $multiVendorProductVideo->getId()
        );
        $this->assertNotEmpty($multiVendorProductVideo->getThumb());
        $this->assertNotEmpty($multiVendorProductVideo->getPath());
    }

    public function testAddVideoToMultiVendorProductWithUploadedFile()
    {
        $service = $this->buildMultiVendorProductService();
        $video = $this->mockUploadedFile("video.avi");
        $file = new MultiVendorProductFile('file', $video->getStream(), $video->getClientFilename());

        $multiVendorProductVideo = $service->addUploadedVideoToMultiVendorProduct(static::MVP_ID, $file);

        $this->assertInstanceOf(MultiVendorProductVideo::class, $multiVendorProductVideo);
        $this->assertRegExp(
            '/[0-9A-Fa-f]{8}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{12}/',
            $multiVendorProductVideo->getId()
        );
        $this->assertNotEmpty($multiVendorProductVideo->getThumb());
        $this->assertNotEmpty($multiVendorProductVideo->getPath());
    }

    public function testDeleteVideoToMultiVendorProduct()
    {
        $service = $this->buildMultiVendorProductService();
        $file = 'http://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ForBiggerEscapes.mp4';
        $service->addHostedVideoToMultiVendorProduct(static::MVP_ID, $file);

        $service->deleteVideoToMultiVendorProduct(static::MVP_ID);
        $mvp = $service->getMultiVendorProductById(static::MVP_ID);

        $this->assertNull($mvp->getVideo()->getId());
    }

    private function buildMultiVendorProductService($userEmail = 'admin@wizaplace.com', $userPassword = 'password'): MultiVendorProductService
    {
        $apiClient = $this->buildApiClient();
        $apiClient->authenticate($userEmail, $userPassword);

        return new MultiVendorProductService($apiClient);
    }

    private function assertMultiVendorProductFilter(MultiVendorProductList $mvpList, array $arrayOptions): void
    {
        static::assertInstanceOf(Pagination::class, $mvpList->getPagination());
        static::assertSame($arrayOptions['requestedNbResult'], $mvpList->getPagination()->getNbResults());
        static::assertSame($arrayOptions['requestedNbPages'], $mvpList->getPagination()->getNbPages());
        static::assertSame(1, $mvpList->getPagination()->getPage());
        static::assertCount($arrayOptions['requestedNbProducts'], $mvpList->getMultiVendorProducts());
    }
}
