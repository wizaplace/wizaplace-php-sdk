<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types=1);

namespace Wizaplace\SDK\Tests\Pim\MultiVendorProduct;

use Wizaplace\SDK\Exception\SomeParametersAreInvalid;
use Wizaplace\SDK\Pim\MultiVendorProduct\MultiVendorProduct;
use Wizaplace\SDK\Pim\MultiVendorProduct\MultiVendorProductService;
use Wizaplace\SDK\Pim\MultiVendorProduct\MultiVendorProductStatus;
use Wizaplace\SDK\Tests\ApiTestCase;

final class MultiVendorProductServiceTest extends ApiTestCase
{
    private $createdMvpId;

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
        ], $mvp->getFreeAttributes());
        $this->assertSame([
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
        ], $mvp->getAttributes());
        $this->assertSame([], $mvp->getImageIds());
    }

    public function testCreateMultiVendorProductWithInvalidPartialProduct()
    {
        $service = $this->buildMultiVendorProductService();

        $newMvp = new MultiVendorProduct([
            'supplierReference' => 'ThisProductIsNotValid',
        ]);

        $this->expectException(SomeParametersAreInvalid::class);
        $id = $service->createMultiVendorProduct($newMvp);
    }

    public function testCreateMultiVendorProductWithFullProduct()
    {
        $service = $this->buildMultiVendorProductService();

        $newMvp = new MultiVendorProduct([
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
        ]);

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
        $this->assertSame([
            'Free attribute multiple' => [
                'Num 1',
                'Num 2',
                51,
            ],
            'Free attribute simple' => [
                'Bla',
            ],
        ], $mvp->getFreeAttributes());
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
        ], $updatedMvp->getFreeAttributes());
        $this->assertSame([
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
        ], $updatedMvp->getAttributes());
        $this->assertSame([], $updatedMvp->getImageIds());
    }

    public function testAddImageToMultiVendorProduct()
    {
        $service = $this->buildMultiVendorProductService();
        $uuid = '0adaf6bc-d362-34be-b72f-42d5aa3b4a4e';

        $image = file_get_contents(__DIR__."/../../fixtures/files/favicon.png");
        $multiVendorProduct = $service->addImageToMultiVendorProduct($uuid, $image);

        $this->assertEquals($uuid, $multiVendorProduct->getId());
    }

    private function buildMultiVendorProductService($userEmail = 'admin@wizaplace.com', $userPassword = 'password'): MultiVendorProductService
    {
        $apiClient = $this->buildApiClient();
        $apiClient->authenticate($userEmail, $userPassword);

        return new MultiVendorProductService($apiClient);
    }
}
