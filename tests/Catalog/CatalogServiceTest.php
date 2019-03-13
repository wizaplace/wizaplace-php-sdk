<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\SDK\Tests\Catalog;

use GuzzleHttp\Psr7\Response;
use Wizaplace\SDK\Catalog\Attribute;
use Wizaplace\SDK\Catalog\AttributeType;
use Wizaplace\SDK\Catalog\AttributeVariant;
use Wizaplace\SDK\Catalog\CatalogService;
use Wizaplace\SDK\Catalog\CatalogServiceInterface;
use Wizaplace\SDK\Catalog\Category;
use Wizaplace\SDK\Catalog\CompanyListItem;
use Wizaplace\SDK\Catalog\Condition;
use Wizaplace\SDK\Catalog\Declination;
use Wizaplace\SDK\Catalog\DeclinationId;
use Wizaplace\SDK\Catalog\Facet\Facet;
use Wizaplace\SDK\Catalog\Facet\ListFacet;
use Wizaplace\SDK\Catalog\Facet\ListFacetValue;
use Wizaplace\SDK\Catalog\Facet\NumericFacet;
use Wizaplace\SDK\Catalog\Option;
use Wizaplace\SDK\Catalog\Product;
use Wizaplace\SDK\Catalog\ProductAttachment;
use Wizaplace\SDK\Catalog\ProductAttribute;
use Wizaplace\SDK\Catalog\ProductAttributeValue;
use Wizaplace\SDK\Catalog\ProductFilter;
use Wizaplace\SDK\Catalog\ProductLocation;
use Wizaplace\SDK\Catalog\ProductReport;
use Wizaplace\SDK\Catalog\ProductSummary;
use Wizaplace\SDK\Catalog\ProductVideo;
use Wizaplace\SDK\Catalog\SearchProductAttribute;
use Wizaplace\SDK\Catalog\Shipping;
use Wizaplace\SDK\Division\Division;
use Wizaplace\SDK\Exception\CompanyNotFound;
use Wizaplace\SDK\Exception\NotFound;
use Wizaplace\SDK\Exception\ProductNotFound;
use Wizaplace\SDK\Exception\SomeParametersAreInvalid;
use Wizaplace\SDK\Image\Image;
use Wizaplace\SDK\Pim\Product\ProductStatus;
use Wizaplace\SDK\Tests\ApiTestCase;

/**
 * @see CatalogService
 */
final class CatalogServiceTest extends ApiTestCase
{
    public function testGetAllProducts()
    {
        $catalogService = $this->buildCatalogService();

        $products = iterator_to_array($catalogService->getAllProducts());

        $this->assertCount(30, $products);
        $this->assertInstanceOf(Product::class, $products[0]);


        // Chargement des produits Français
        $products = iterator_to_array($catalogService->getAllProducts('fr'));
        $this->assertCount(30, $products);

        /** @var Product $product */
        $product = $products[0];
        $this->assertInstanceOf(Product::class, $product);
        $this->assertSame("Test MVP", $product->getName());


        // Chargement des produits Anglais
        $products = iterator_to_array($catalogService->getAllProducts('en'));
        $this->assertCount(30, $products);

        /** @var Product $product */
        $product = $products[0];
        $this->assertInstanceOf(Product::class, $product);
        $this->assertSame("MVP Test", $product->getName());
    }

    public function testGetProductById()
    {
        $catalogService = $this->buildCatalogService();

        $product = $catalogService->getProductById('1');

        $this->assertSame('1', $product->getId());
        $this->assertSame('test-product-slug', $product->getSlug());
        $this->assertSame('Z11 Plus Boîtier PC en Acier ATX', $product->getName());
        $this->assertSame('', $product->getDescription());
        $this->assertCount(1, $product->getDeclinations());
        $this->assertCount(0, $product->getAttributes());
        $this->assertCount(1, $product->getCategoryPath());
        $this->assertSame('978020137962', $product->getCode());
        $this->assertSame(67.9, $product->getMinPrice());
        $this->assertFalse($product->hasInfiniteStock());

        $this->assertCount(3, $product->getShippings());

        /** @var Shipping $shipping */
        $shipping = current($product->getShippings());
        $this->assertSame('TNT Express', $shipping->getName());
        $this->assertNull($shipping->getImage());
        $this->assertSame(0.0, $shipping->getFirstRate());
        $this->assertSame(0.0, $shipping->getNextRate());
        $this->assertSame('24h', $shipping->getDeliveryTime());

        $this->assertSame('', $product->getShortDescription());
        $this->assertSame('INFO-001', $product->getSupplierReference());
        $this->assertTrue($product->isTransactional());
        $this->assertSame(0.0, $product->getGreenTax());
        $this->assertSame(1.23, $product->getWeight());
        $this->assertEquals(0, $product->getAverageRating());
        $this->assertNull($product->getGeolocation());
        $this->assertNull($product->getVideo());
        $this->assertCount(0, $product->getAttachments());
        $this->assertNull($catalogService->getBrand($product));
        $this->assertEmpty($product->getOtherOffers($product->getDeclinations()[0]));
        $this->assertTrue($product->getDeclinations()[0]->isAvailable());

        $companies = $product->getCompanies();
        $this->assertCount(1, $companies);
        $this->assertSame(3, $companies[0]->getId());
        $this->assertSame('The World Company Inc.', $companies[0]->getName());
        $this->assertSame('the-world-company-inc', $companies[0]->getSlug());
        $this->assertEquals(null, $companies[0]->getAverageRating());
        $this->assertNull($companies[0]->getImage());
        $this->assertTrue($companies[0]->isProfessional());

        $this->assertGreaterThanOrEqual(1400000000, $product->getCreatedAt()->getTimestamp());
        $this->assertGreaterThanOrEqual(0, $product->getCreatedAt()->diff($product->getUpdatedAt())->s);
        $this->assertInstanceOf(\DateTimeInterface::class, $product->getAvailableSince());
        $this->assertSame(1386316800, $product->getAvailableSince()->getTimestamp());

        $this->assertEmpty($product->getImages());

        foreach ($product->getOffers() as $offer) {
            $this->assertSame(1, $offer->getProductId());
            $this->assertSame(3, $offer->getCompanyId());
            $this->assertSame(67.9, $offer->getPrice());
            $this->assertSame([], $offer->getDivisions());
            $this->assertEquals(ProductStatus::ENABLED(), $offer->getStatus());
        }
    }

    public function testGetDeclinationByID()
    {
        $catalogService = $this->buildCatalogService();

        $declination = $catalogService->getDeclinationById('3_3_8');

        $this->assertSame('size_15', $declination->getCode());
        $this->assertTrue($declination->isAvailable());
        $this->assertSame('INFO-ECRAN-001', $declination->getSupplierReference());

        $this->assertSame(15.5, $declination->getPrice());
        $this->assertSame(15.5, $declination->getOriginalPrice());
        $this->assertNull($declination->getCrossedOutPrice());

        $this->assertSame(0.0, $declination->getGreenTax());
        $this->assertSame(10, $declination->getAmount());
        $this->assertFalse($declination->hasInfiniteStock());
        $this->assertNull($declination->getAffiliateLink());
        $this->assertEmpty($declination->getImages());
        $this->assertCount(1, $declination->getOptions());
        $this->assertTrue($declination->isBrandNew());

        $this->assertSame('The World Company Inc.', $declination->getCompany()->getName());
        $this->assertSame('the-world-company-inc.', $declination->getCompany()->getSlug());
        $this->assertTrue($declination->getCompany()->isProfessional());
        $this->assertNull($declination->getCompany()->getImage());

        $this->assertCount(3, $declination->getShippings());
    }

    public function testGetProducstByCode()
    {
        $catalogService = $this->buildCatalogService();

        $products = $catalogService->getProductsByCode('978020137962');

        $this->assertCount(1, $products);

        $product = $products[0];

        $this->assertSame('1', $product->getId());
        $this->assertSame('test-product-slug', $product->getSlug());
        $this->assertSame('Z11 Plus Boîtier PC en Acier ATX', $product->getName());
        $this->assertSame('', $product->getDescription());
        $this->assertCount(1, $product->getDeclinations());
        $this->assertCount(0, $product->getAttributes());
        $this->assertCount(1, $product->getCategoryPath());
        $this->assertSame('978020137962', $product->getCode());
        $this->assertSame(67.9, $product->getMinPrice());
        $this->assertCount(3, $product->getShippings());

        /** @var Shipping $shipping */
        $shipping = current($product->getShippings());
        $this->assertSame('TNT Express', $shipping->getName());
        $this->assertNull($shipping->getImage());
        $this->assertSame(0.0, $shipping->getFirstRate());
        $this->assertSame(0.0, $shipping->getNextRate());
        $this->assertSame('24h', $shipping->getDeliveryTime());

        $this->assertSame('', $product->getShortDescription());
        $this->assertSame('INFO-001', $product->getSupplierReference());
        $this->assertTrue($product->isTransactional());
        $this->assertSame(0.0, $product->getGreenTax());
        $this->assertSame(1.23, $product->getWeight());
        $this->assertEquals(3, $product->getAverageRating());
        $this->assertNull($product->getGeolocation());
        $this->assertNull($product->getVideo());
        $this->assertCount(0, $product->getAttachments());
        $this->assertNull($catalogService->getBrand($product));
        $this->assertEmpty($product->getOtherOffers($product->getDeclinations()[0]));
        $this->assertTrue($product->getDeclinations()[0]->isAvailable());

        $companies = $product->getCompanies();
        $this->assertCount(1, $companies);
        $this->assertSame(3, $companies[0]->getId());
        $this->assertSame('The World Company Inc.', $companies[0]->getName());
        $this->assertSame('the-world-company-inc', $companies[0]->getSlug());
        $this->assertEquals(5, $companies[0]->getAverageRating());
        $this->assertNull($companies[0]->getImage());
        $this->assertTrue($companies[0]->isProfessional());

        $this->assertGreaterThanOrEqual(1400000000, $product->getCreatedAt()->getTimestamp());
        $this->assertGreaterThanOrEqual(0, $product->getCreatedAt()->diff($product->getUpdatedAt())->s);
        $this->assertSame(1386316800, $product->getAvailableSince()->getTimestamp());
        $this->assertInstanceOf(\DateTimeInterface::class, $product->getAvailableSince());

        $this->assertEmpty($product->getImages());
    }

    public function testGetProducstBySupplierReference()
    {
        $catalogService = $this->buildCatalogService();

        $products = $catalogService->getProductsBySupplierReference('INFO-001');

        $this->assertCount(1, $products);

        $product = $products[0];

        $this->assertSame('1', $product->getId());
        $this->assertSame('test-product-slug', $product->getSlug());
        $this->assertSame('Z11 Plus Boîtier PC en Acier ATX', $product->getName());
        $this->assertSame('', $product->getDescription());
        $this->assertCount(1, $product->getDeclinations());
        $this->assertCount(0, $product->getAttributes());
        $this->assertCount(1, $product->getCategoryPath());
        $this->assertSame('978020137962', $product->getCode());
        $this->assertSame(67.9, $product->getMinPrice());
        $this->assertCount(3, $product->getShippings());

        /** @var Shipping $shipping */
        $shipping = current($product->getShippings());
        $this->assertSame('TNT Express', $shipping->getName());
        $this->assertNull($shipping->getImage());
        $this->assertSame(0.0, $shipping->getFirstRate());
        $this->assertSame(0.0, $shipping->getNextRate());
        $this->assertSame('24h', $shipping->getDeliveryTime());

        $this->assertSame('', $product->getShortDescription());
        $this->assertSame('INFO-001', $product->getSupplierReference());
        $this->assertTrue($product->isTransactional());
        $this->assertSame(0.0, $product->getGreenTax());
        $this->assertSame(1.23, $product->getWeight());
        $this->assertEquals(3, $product->getAverageRating());
        $this->assertNull($product->getGeolocation());
        $this->assertNull($product->getVideo());
        $this->assertCount(0, $product->getAttachments());
        $this->assertNull($catalogService->getBrand($product));
        $this->assertEmpty($product->getOtherOffers($product->getDeclinations()[0]));
        $this->assertTrue($product->getDeclinations()[0]->isAvailable());

        $companies = $product->getCompanies();
        $this->assertCount(1, $companies);
        $this->assertSame(3, $companies[0]->getId());
        $this->assertSame('The World Company Inc.', $companies[0]->getName());
        $this->assertSame('the-world-company-inc', $companies[0]->getSlug());
        $this->assertEquals(5, $companies[0]->getAverageRating());
        $this->assertNull($companies[0]->getImage());
        $this->assertTrue($companies[0]->isProfessional());

        $this->assertGreaterThanOrEqual(1400000000, $product->getCreatedAt()->getTimestamp());
        $this->assertGreaterThanOrEqual(0, $product->getCreatedAt()->diff($product->getUpdatedAt())->s);
        $this->assertInstanceOf(\DateTimeInterface::class, $product->getAvailableSince());
        $this->assertSame(1386316800, $product->getAvailableSince()->getTimestamp());

        $this->assertEmpty($product->getImages());
    }

    public function testGetProductByFilters(): void
    {
        $catalogService = $this->buildCatalogService();

        $filters = (new ProductFilter())
            ->setIds([1, 2, 3])
            ->setCodes(['6311386284347', '4991933817246'])
            ->setSupplierRefs(['TEST-ATTACHMENT', 'TEST-GEOLOC']);

        $products = $catalogService->getProductsByFilters($filters);

        $this->assertCount(7, $products);

        $expected = ['2', '3', '1', '9', '10', '22', '23'];

        foreach ($products as $key => $product) {
            $this->assertSame($expected[$key], $product->getId());
        }
    }

    public function testGetProductByFiltersWithCompany()
    {
        $catalogService = $this->buildCatalogService();

        // Filter with companyId
        $filters = (new ProductFilter())
            ->setIds([1, 2, 3])
            ->setCodes(['6311386284347', '4991933817246'])
            ->setSupplierRefs(['TEST-ATTACHMENT', 'TEST-GEOLOC'])
            ->setCompanyId(1);

        $products = $catalogService->getProductsByFilters($filters);

        $this->assertCount(2, $products);

        $expected = ['22', '23'];

        foreach ($products as $key => $product) {
            $this->assertSame($expected[$key], $product->getId());
        }
    }

    public function testGetProductByIdInAnotherLanguage(): void
    {
        $apiClient = $this->buildApiClient();
        $apiClient->setLanguage('en');
        $catalogService = new CatalogService($apiClient);

        $product = $catalogService->getProductById('1');

        self::assertSame('Z11 ATX Plus PC Steel case', $product->getName());
    }

    public function testGetMVPById()
    {
        $mvp = $this->buildCatalogService()->getProductById('0adaf6bc-d362-34be-b72f-42d5aa3b4a4e');
        $this->assertInstanceOf(Product::class, $mvp);

        $this->assertSame('0adaf6bc-d362-34be-b72f-42d5aa3b4a4e', $mvp->getId());
        $this->assertSame('Test MVP', $mvp->getName());
        $this->assertCount(2, $mvp->getCompanies());
        $this->assertCount(2, $mvp->getDeclinations());
        $otherOffers = $mvp->getOtherOffers($mvp->getDeclinations()[0]);
        $this->assertSame([$mvp->getDeclinations()[1]], $otherOffers);
    }

    public function testGetProductWithComplexAttributes()
    {
        $catalogService = $this->buildCatalogService();

        $product = $catalogService->getProductById('0adaf6bc-d362-34be-b72f-42d5aa3b4a4e');

        $this->assertSame('0adaf6bc-d362-34be-b72f-42d5aa3b4a4e', $product->getId());
        $this->assertSame('ipsum-omnis-non-asperiores-hic-veritatis', $product->getSlug());

        $expectedAttributes = [
            new ProductAttribute([
                'id' => 3,
                'name' => 'Commentaire',
                'values' => [
                    [
                        'id' => null,
                        'attributeId' => 3,
                        'name' => 'Commentaire #12M%M_°09£*/.?',
                        'slug' => '',
                        'image' => null,
                    ],
                ],
                'value' => 'Commentaire #12M%M_°09£*/.?',
                'valueIds' => [],
                'children' => [],
                'imageUrls' => [],
                'type' => AttributeType::FREE_TEXT()->getValue(),
            ]),
            new ProductAttribute([
                'id' => 1,
                'name' => 'Couleur',
                'values' => [
                    [
                        'id' => 2,
                        'attributeId' => 1,
                        'name' => 'Blanc',
                        'slug' => 'blanc',
                        'image' => null,
                    ],
                    [
                        'id' => 3,
                        'attributeId' => 1,
                        'name' => 'Rouge',
                        'slug' => 'rouge',
                        'image' => [
                            'id' => 5,
                        ],
                    ],
                ],
                'value' => [
                    'Blanc',
                    'Rouge',
                ],
                'valueIds' => [2, 3],
                'children' => [],
                'imageUrls' => [],
                'type' => AttributeType::CHECKBOX_MULTIPLE()->getValue(),
            ]),
            new ProductAttribute([
                'id' => 4,
                'name' => 'Groupe attributs',
                'values' => null,
                'value' => null,
                'valueIds' => [],
                'children' => [
                    [
                        'id' => 7,
                        'name' => 'Complément adresse',
                        'values' => [
                            [
                                'id' => null,
                                'attributeId' => 7,
                                'name' => 'Complément d\'adresse pour la stepo',
                                'slug' => '',
                                'image' => null,
                            ],
                        ],
                        'value' => 'Complément d\'adresse pour la stepo',
                        'valueIds' => [],
                        'children' => [],
                        'imageUrls' => [],
                        'type' => AttributeType::FREE_TEXT()->getValue(),
                    ],
                    [
                        'id' => 5,
                        'name' => 'Couleur secondaire',
                        'values' => [
                            [
                                'id' => 9,
                                'attributeId' => 5,
                                'name' => 'Orange',
                                'slug' => 'orange',
                                'image' => [
                                    'id' => 6,
                                ],
                            ],
                        ],
                        'value' => ['Orange'],
                        'valueIds' => [9],
                        'children' => [],
                        'imageUrls' => [],
                        'type' => AttributeType::CHECKBOX_MULTIPLE()->getValue(),
                    ],
                    [
                        'id' => 6,
                        'name' => 'Pointure',
                        'values' => [
                            [
                                'id' => 17,
                                'attributeId' => 6,
                                'name' => '44',
                                'slug' => '44',
                                'image' => [
                                    'id' => 7,
                                ],
                            ],
                        ],
                        'value' => ['44'],
                        'valueIds' => [17],
                        'children' => [],
                        'imageUrls' => [],
                        'type' => AttributeType::LIST_TEXT()->getValue(),
                    ],
                ],
                'imageUrls' => [],
                'type' => AttributeType::GROUP()->getValue(),
            ]),
            new ProductAttribute([
                'id' => 9,
                'name' => 'Marque',
                'values' => [
                    [
                        'id' => 20,
                        'attributeId' => 9,
                        'name' => 'Puma',
                        'slug' => 'puma',
                        'image' => null,
                    ],
                ],
                'value' => ['Puma'],
                'valueIds' => [20],
                'children' => [],
                'imageUrls' => [],
                'type' => AttributeType::LIST_BRAND()->getValue(),
            ]),
            new ProductAttribute([
                'id' => 2,
                'name' => 'Taille',
                'values' => [
                    [
                        'id' => 5,
                        'attributeId' => 2,
                        'name' => 'M',
                        'slug' => 'm',
                        'image' => null,
                    ],
                ],
                'value' => ['M'],
                'valueIds' => [5],
                'children' => [],
                'imageUrls' => [],
                'type' => AttributeType::LIST_TEXT()->getValue(),
            ]),
            new ProductAttribute([
                'id' => null,
                'name' => 'Free attribute multiple',
                'values' => [
                    [
                        'id' => null,
                        'attributeId' => null,
                        'name' => 'réponse - 1 #',
                        'slug' => '',
                        'image' => null,
                    ],
                    [
                        'id' => null,
                        'attributeId' => null,
                        'name' => 'réponse - 2 @',
                        'slug' => '',
                        'image' => null,
                    ],
                    [
                        'id' => null,
                        'attributeId' => null,
                        'name' => '4985',
                        'slug' => '',
                        'image' => null,
                    ],
                ],
                'value' => [
                    'réponse - 1 #',
                    'réponse - 2 @',
                    4985,
                ],
                'valueIds' => [],
                'children' => [],
                'imageUrls' => [],
                'type' => AttributeType::FREE()->getValue(),
            ]),
            new ProductAttribute([
                'id' => null,
                'name' => 'Free attribute simple',
                'values' => [
                    [
                        'id' => null,
                        'attributeId' => null,
                        'name' => 'valeur simple du free attribute #12M%M_°09£*/.?',
                        'slug' => '',
                        'image' => null,
                    ],
                ],
                'value' => ['valeur simple du free attribute #12M%M_°09£*/.?'],
                'valueIds' => [],
                'children' => [],
                'imageUrls' => [],
                'type' => AttributeType::FREE()->getValue(),
            ]),
            new ProductAttribute([
                'id' => null,
                'name' => 'Free attribute simple mais en tableau',
                'values' => [
                    [
                        'id' => null,
                        'attributeId' => null,
                        'name' => 'une bien belle valeur déjà encapsulée',
                        'slug' => '',
                        'image' => null,
                    ],
                ],
                'value' => ['une bien belle valeur déjà encapsulée'],
                'valueIds' => [],
                'children' => [],
                'imageUrls' => [],
                'type' => AttributeType::FREE()->getValue(),
            ]),
            new ProductAttribute([
                'id' => null,
                'name' => 'Free attribute integer ?',
                'values' => [
                    [
                        'id' => null,
                        'attributeId' => null,
                        'name' => '92254094',
                        'slug' => '',
                        'image' => null,
                    ],
                ],
                'value' => [92254094],
                'valueIds' => [],
                'children' => [],
                'imageUrls' => [],
                'type' => AttributeType::FREE()->getValue(),
            ]),
            new ProductAttribute([
                'id' => null,
                'name' => 'Free attribute integer mais en tableau',
                'values' => [
                    [
                        'id' => null,
                        'attributeId' => null,
                        'name' => 'la même histoire par ici',
                        'slug' => '',
                        'image' => null,
                    ],
                ],
                'value' => ['la même histoire par ici'],
                'valueIds' => [],
                'children' => [],
                'imageUrls' => [],
                'type' => AttributeType::FREE()->getValue(),
            ]),
        ];

        $actualAttributes = $product->getAttributes();
        $this->assertContainsOnly(ProductAttribute::class, $actualAttributes);
        $this->sortAttributesById($expectedAttributes);
        $this->sortAttributesById($actualAttributes);

        // Test some getters
        foreach ($actualAttributes as $attribute) {
            $id = $attribute->getId();
            if ($id !== null) {
                $this->assertInternalType('int', $id);
            }

            $this->assertInternalType('string', $attribute->getName());
            $this->assertInstanceOf(AttributeType::class, $attribute->getType());
            $this->assertContainsOnly(ProductAttribute::class, $attribute->getChildren());

            $values = $attribute->getValues();
            if ($values !== null) {
                $this->assertContainsOnly(ProductAttributeValue::class, $values);

                foreach ($values as $value) {
                    $id = $value->getId();
                    if ($id !== null) {
                        $this->assertInternalType('int', $id);
                    }

                    $id = $value->getAttributeId();
                    if ($id !== null) {
                        $this->assertInternalType('int', $id);
                    }

                    $this->assertInternalType('string', $value->getName());
                    $this->assertInternalType('string', $value->getSlug());
                    $image = $value->getImage();
                    if ($image !== null) {
                        $this->assertInstanceOf(Image::class, $image);
                    }
                }
            }
        }

        $this->assertEquals($expectedAttributes, $actualAttributes);

        $brand = $catalogService->getBrand($product);
        $this->assertInstanceOf(ProductAttributeValue::class, $brand);
        $this->assertGreaterThan(0, $brand->getId());
        $this->assertGreaterThan(0, $brand->getAttributeId());
        $this->assertSame('Puma', $brand->getName());
        $this->assertSame('puma', $brand->getSlug());
    }

    public function testGetNonExistingProductById()
    {
        $catalogService = $this->buildCatalogService();

        $this->expectException(ProductNotFound::class);
        $catalogService->getProductById('404');
    }

    public function testSearchOneProductByName()
    {
        $catalogService = $this->buildCatalogService();

        $result = $catalogService->search('Product with shippings');

        $products = $result->getProducts();
        $this->assertCount(1, $products);

        $product = $products[0];
        $this->assertSame('4', $product->getId());
        $this->assertSame('product-with-shippings', $product->getSlug());
        $this->assertTrue($product->isAvailable());
        $this->assertGreaterThan(1400000000, $product->getCreatedAt()->getTimestamp());
        $this->assertNull($product->getAverageRating());
        $this->assertSame('Product with shippings', $product->getName());
        $this->assertContainsOnly(Condition::class, $product->getConditions());
        $this->assertEquals([Condition::BRAND_NEW()], $product->getConditions());
        $this->assertTrue((new DeclinationId('4_0'))->equals($product->getMainDeclinationId()), "got ".$product->getMainDeclinationId());
        $this->assertSame(1, $product->getDeclinationCount());
        $this->assertSame('', $product->getSubtitle());
        $this->assertSame("La nouvelle génération de notre tablette Fire phare - désormais plus fine, plus légère, dotée d'une plus longue autonomie et d'un écran amélioré.", $product->getShortDescription());
        $this->assertGreaterThanOrEqual($product->getCreatedAt()->getTimestamp(), $product->getUpdatedAt()->getTimestamp());
        $this->assertNull($product->getAffiliateLink());
        $this->assertCount(1, $product->getCategoryPath());
        $this->assertNull($product->getMainImage());
        $this->assertSame(9.9, $product->getMinimumPrice());
        $this->assertNull($product->getCrossedOutPrice());
        $this->assertCount(0, $product->getAttributes());
        $this->assertSame(45.75845, $product->getGeolocation()->getLatitude());
        $this->assertSame(4.799044, $product->getGeolocation()->getLongitude());
        $this->assertSame('Lyon 5e', $product->getGeolocation()->getLabel());
        $this->assertSame('69005', $product->getGeolocation()->getZipcode());
        $companies = $product->getCompanies();
        $this->assertCount(1, $companies);
        $this->assertSame('The World Company Inc.', $companies[0]->getName());
        $this->assertSame('the-world-company-inc.', $companies[0]->getSlug());
        $this->assertSame(3, $companies[0]->getId());
        $this->assertTrue($companies[0]->isProfessional());
        $this->assertNull($companies[0]->getImage());
        $this->assertNull($companies[0]->getAverageRating());


        $pagination = $result->getPagination();
        $this->assertSame(1, $pagination->getNbPages());
        $this->assertSame(1, $pagination->getNbResults());
        $this->assertSame(1, $pagination->getPage());
        $this->assertSame(12, $pagination->getResultsPerPage());

        $facets = $result->getFacets();
        $this->assertCount(10, $facets);
        $this->assertContainsOnly(Facet::class, $facets);
        /** @var ListFacet $categoryFacet */
        $categoryFacet = $facets[0];
        $this->assertSame('categories', $categoryFacet->getName());
        $this->assertSame('Catégorie', $categoryFacet->getLabel());
        $this->assertInstanceOf(ListFacet::class, $categoryFacet);
        $this->assertEquals([
            5 => new \Wizaplace\SDK\Catalog\Facet\ListFacetValue([
                'label' => 'Special category dedicated to specific tests',
                'count' => '1',
                'position' => '0',
            ]),
        ], $categoryFacet->getValues());

        $this->assertNull($catalogService->getBrand($product));
    }

    public function testSearchMVP()
    {
        $catalogService = $this->buildCatalogService();

        $result = $catalogService->search('Test MVP');

        $products = $result->getProducts();
        $this->assertContainsOnly(ProductSummary::class, $products);
        $this->assertCount(1, $products);

        $product = $products[0];
        $this->assertSame('c659af24-56b8-48fd-8b86-34ddff28ba1b', $product->getId());
        $this->assertTrue((new DeclinationId('8_0'))->equals($product->getMainDeclinationId()), "got ".$product->getMainDeclinationId());
    }

    public function testSearchProductWithComplexAttributes()
    {
        $catalogService = $this->buildCatalogService();

        $result = $catalogService->search('complex', ['1' => '2']);

        $products = $result->getProducts();
        $this->assertCount(1, $products);

        $product = $products[0];
        $this->assertSame('5', $product->getId());

        $attributes = $product->getAttributes();
        $this->assertContainsOnly(SearchProductAttribute::class, $attributes);

        /** @var SearchProductAttribute[] $attributesMap */
        $attributesMap = [];
        /** @var SearchProductAttribute[] $freeAttributesMap */
        $freeAttributesMap = [];
        foreach ($attributes as $attribute) {
            if ($attribute->getId() === null) {
                $freeAttributesMap[$attribute->getName()] = $attribute;
            } else {
                $attributesMap[$attribute->getId()] = $attribute;
            }
        }

        // Couleur
        $this->assertSame('Couleur', $attributesMap[1]->getName());
        $this->assertSame('', $attributesMap[1]->getSlug());
        $this->assertEquals([
            new ProductAttributeValue([
                'id' => 2,
                'attributeId' => 1,
                'name' => 'Blanc',
                'slug' => 'blanc',
                'image' => null,
            ]),
            new ProductAttributeValue([
                'id' => 3,
                'attributeId' => 1,
                'name' => 'Rouge',
                'slug' => 'rouge',
                'image' => null,
            ]),
        ], $attributesMap[1]->getValues());
        $this->assertTrue(AttributeType::CHECKBOX_MULTIPLE()->equals($attributesMap[1]->getType()));

        // Free attributes
        $this->assertEquals([
            new ProductAttributeValue([
                'id' => null,
                'attributeId' => null,
                'name' => 'valeur simple du free attribute #12M%M_°09£*/.?',
                'slug' => '',
                'image' => null,
            ]),
        ], $freeAttributesMap['Free attribute simple']->getValues());

        $this->assertEquals([
            new ProductAttributeValue([
                'id' => null,
                'attributeId' => null,
                'name' => 'réponse - 1 #',
                'slug' => '',
                'image' => null,
            ]),
            new ProductAttributeValue([
                'id' => null,
                'attributeId' => null,
                'name' => 'réponse - 2 @',
                'slug' => '',
                'image' => null,
            ]),
            new ProductAttributeValue([
                'id' => null,
                'attributeId' => null,
                'name' => '4985',
                'slug' => '',
                'image' => null,
            ]),
        ], $freeAttributesMap['Free attribute multiple']->getValues());

        $brand = $catalogService->getBrand($product);
        $this->assertInstanceOf(ProductAttributeValue::class, $brand);
        $this->assertGreaterThan(0, $brand->getId());
        $this->assertGreaterThan(0, $brand->getAttributeId());
        $this->assertSame('Puma', $brand->getName());
        $this->assertSame('puma', $brand->getSlug());

        $facets = $result->getFacets();
        $this->assertContainsOnly(Facet::class, $facets);
        foreach ($facets as $facet) {
            $this->assertNotEmpty($facet->getName());
            $this->assertNotEmpty($facet->getLabel());
            if ($facet instanceof ListFacet) {
                $values = $facet->getValues();
                $this->assertContainsOnly(ListFacetValue::class, $values);
                foreach ($values as $value) {
                    $this->assertNotEmpty($value->getLabel());
                    $this->assertGreaterThanOrEqual(0, $value->getPosition());
                    $this->assertGreaterThanOrEqual(0, $value->getCount());
                }
            } elseif ($facet instanceof NumericFacet) {
                $this->assertGreaterThanOrEqual($facet->getMax(), $facet->getMin());
            }
        }
    }

    public function testGetCompanyById()
    {
        $catalogService = $this->buildCatalogService();

        $company = $catalogService->getCompanyById(3);

        $this->assertSame(3, $company->getId());
        $this->assertSame('The World Company Inc.', $company->getName());
        $this->assertSame('the-world-company-inc.', $company->getSlug());
        $this->assertSame('The World Company Inc.', $company->getDescription());
        $this->assertSame('40 rue Laure Diebold', $company->getAddress());
        $this->assertSame('01 02 03 04 05', $company->getPhoneNumber());
        $this->assertTrue($company->isProfessional());
        $this->assertEquals(45.778847, $company->getLocation()->getLatitude());
        $this->assertEquals(4.800039, $company->getLocation()->getLongitude());
        $this->assertSame(5, $company->getAverageRating());
        $this->assertSame('Lorem Ipsum', $company->getTerms());
        $this->assertSame('FR', $company->getFullAddress()->getCountry());
        $this->assertSame('40 rue Laure Diebold', $company->getFullAddress()->getAddress());
        $this->assertSame('Paris', $company->getFullAddress()->getCity());
        $this->assertSame('75001', $company->getFullAddress()->getZipCode());
        $this->assertSame([], $company->getExtra());

        $company = $catalogService->getCompanyById(4);

        $this->assertSame(4, $company->getId());
        $this->assertSame('C2C company', $company->getName());
        $this->assertSame('c2c-company', $company->getSlug());
        $this->assertSame('C2C company', $company->getDescription());
        $this->assertSame('40 rue Laure Diebold', $company->getAddress());
        $this->assertSame('01 02 03 04 05', $company->getPhoneNumber());
        $this->assertFalse($company->isProfessional());
        $this->assertNull($company->getLocation());
        $this->assertNull($company->getAverageRating());
        $this->assertSame('Lorem Ipsum', $company->getTerms());
        $this->assertSame('FR', $company->getFullAddress()->getCountry());
        $this->assertSame('40 rue Laure Diebold', $company->getFullAddress()->getAddress());
        $this->assertSame('Paris', $company->getFullAddress()->getCity());
        $this->assertSame('75001', $company->getFullAddress()->getZipCode());
        $this->assertSame([], $company->getExtra());
    }

    public function testGetCompanies()
    {
        $catalogService = $this->buildCatalogService();

        $companies = $catalogService->getCompanies();

        $this->assertInternalType('array', $companies);
        $this->assertGreaterThanOrEqual(4, count($companies));
        $this->assertContainsOnly(CompanyListItem::class, $companies);
        $this->assertSame(1, $companies[0]->getId());
        $this->assertSame('Marchand de test', $companies[0]->getName());
        $this->assertFalse($companies[0]->isProfessional());
        $this->assertSame('FR', $companies[0]->getFullAddress()->getCountry());
        $this->assertSame('Rue des marketplaces', $companies[0]->getFullAddress()->getAddress());
        $this->assertSame('Lyon', $companies[0]->getFullAddress()->getCity());
        $this->assertSame('69000', $companies[0]->getFullAddress()->getZipCode());
    }

    public function testGetC2cCompanyById()
    {
        $catalogService = $this->buildCatalogService();

        $company = $catalogService->getCompanyById(4);

        $this->assertSame(4, $company->getId());
        $this->assertSame('C2C company', $company->getName());
        $this->assertSame('c2c-company', $company->getSlug());
        $this->assertSame('C2C company', $company->getDescription());
        $this->assertSame('40 rue Laure Diebold', $company->getAddress());
        $this->assertSame('01 02 03 04 05', $company->getPhoneNumber());
        $this->assertFalse($company->isProfessional());
        $this->assertNull($company->getLocation());
        $this->assertNull($company->getAverageRating());
        $this->assertNull($company->getImage());
        $this->assertSame('Lorem Ipsum', $company->getTerms());
        $this->assertSame('FR', $company->getFullAddress()->getCountry());
        $this->assertSame('40 rue Laure Diebold', $company->getFullAddress()->getAddress());
        $this->assertSame('Paris', $company->getFullAddress()->getCity());
        $this->assertSame('75001', $company->getFullAddress()->getZipCode());
    }

    public function testGetCompanyWhichDoesntExist()
    {
        $this->expectException(CompanyNotFound::class);

        $this->buildCatalogService()->getCompanyById(404);
    }

    public function testGetCategory()
    {
        $category = $this->buildCatalogService()->getCategory(2);
        $this->assertSame(2, $category->getId());
        $this->assertNull($category->getParentId());
        $this->assertSame('Catégorie principale', $category->getName());
        $this->assertSame('categorie-principale', $category->getSlug());
        $this->assertSame('', $category->getDescription());
        $this->assertSame(10, $category->getPosition());
        $this->assertSame(0, $category->getProductCount());
        $this->assertSame('Catégorie principale - Vente Catégorie principale - au meilleur prix - en ligne', $category->getSeoTitle());
        $this->assertSame('Vente Catégorie principale au meilleur prix. Paiement sécurisé. Grand choix Catégorie principale. Livraison rapide.', $category->getSeoDescription());
        $this->assertNull($category->getImage());

        $categoryPath = $category->getCategoryPath();
        $this->assertCount(1, $categoryPath);
        $this->assertSame(2, $categoryPath[0]->getId());
        $this->assertSame('Catégorie principale', $categoryPath[0]->getName());
        $this->assertSame('categorie-principale', $categoryPath[0]->getSlug());

        $category = $this->buildCatalogService()->getCategory(4);
        $this->assertSame(4, $category->getId());
        $this->assertSame(3, $category->getParentId());
        $this->assertSame('Écrans', $category->getName());
        $this->assertSame('screens', $category->getSlug());
        $this->assertSame('', $category->getDescription());
        $this->assertSame(0, $category->getPosition());
        $this->assertSame(1, $category->getProductCount());
        $this->assertSame('', $category->getSeoTitle());
        $this->assertSame('', $category->getSeoDescription());
        $this->assertNull($category->getImage());

        $categoryPath = $category->getCategoryPath();
        $this->assertCount(2, $categoryPath);
        $this->assertSame(3, $categoryPath[0]->getId());
        $this->assertSame('Informatique', $categoryPath[0]->getName());
        $this->assertSame('it', $categoryPath[0]->getSlug());
        $this->assertSame(4, $categoryPath[1]->getId());
        $this->assertSame('Écrans', $categoryPath[1]->getName());
        $this->assertSame('screens', $categoryPath[1]->getSlug());
    }

    public function testGetCategoryTree()
    {
        $categoryTree = $this->buildCatalogService()->getCategoryTree();
        $this->assertCount(3, $categoryTree);

        $category = $categoryTree[2]->getCategory();

        $this->assertSame(2, $category->getId());
        $this->assertSame('Catégorie principale', $category->getName());
        $this->assertSame('categorie-principale', $category->getSlug());
        $this->assertSame('', $category->getDescription());
        $this->assertSame(10, $category->getPosition());
        $this->assertSame(0, $category->getProductCount());

        $categoryPath = $category->getCategoryPath();
        $this->assertCount(1, $categoryPath);
        $this->assertSame(2, $categoryPath[0]->getId());
        $this->assertSame('Catégorie principale', $categoryPath[0]->getName());
        $this->assertSame('categorie-principale', $categoryPath[0]->getSlug());

        $childrenTrees = $categoryTree[0]->getChildren();
        $this->assertCount(2, $childrenTrees);
        $childCategory = $childrenTrees[0]->getCategory();
        $this->assertSame(4, $childCategory->getId());
        $this->assertSame('Écrans', $childCategory->getName());
        $this->assertSame('screens', $childCategory->getSlug());
        $this->assertSame('', $childCategory->getDescription());
        $this->assertSame(0, $childCategory->getPosition());
        $this->assertSame(1, $childCategory->getProductCount());

        $categoryPath = $childCategory->getCategoryPath();
        $this->assertCount(2, $categoryPath);
        $this->assertSame(3, $categoryPath[0]->getId());
        $this->assertSame('Informatique', $categoryPath[0]->getName());
        $this->assertSame('it', $categoryPath[0]->getSlug());
        $this->assertSame(4, $categoryPath[1]->getId());
        $this->assertSame('Écrans', $categoryPath[1]->getName());
        $this->assertSame('screens', $categoryPath[1]->getSlug());
    }

    public function testGetCategories()
    {
        $categories = $this->buildCatalogService()->getCategories();

        $this->assertGreaterThanOrEqual(5, count($categories));
        $this->assertContainsOnly(Category::class, $categories);
    }

    public function testGetAttributes()
    {
        $attributes = $this->buildCatalogService()->getAttributes();

        $this->assertCount(10, $attributes);
        $this->assertContainsOnly(Attribute::class, $attributes);
        foreach ($attributes as $attribute) {
            $this->assertGreaterThan(0, $attribute->getId());
            $this->assertGreaterThanOrEqual(0, $attribute->getPosition());
            $this->assertNotEmpty($attribute->getName());
            $this->assertNotEmpty($attribute->getType()->getValue());
        }
    }

    public function testGetAttribute()
    {
        $attribute = $this->buildCatalogService()->getAttribute(1);

        $this->assertSame(1, $attribute->getId());
        $this->assertSame(0, $attribute->getPosition());
        $this->assertSame('Couleur', $attribute->getName());
        $this->assertTrue(AttributeType::CHECKBOX_MULTIPLE()->equals($attribute->getType()));
        $this->assertNull($attribute->getParentId());
    }

    public function testGetNonExistingAttribute()
    {
        $this->expectException(NotFound::class);
        $this->buildCatalogService()->getAttribute(404);
    }

    public function testGetAttributeVariant()
    {
        $variant = $this->buildCatalogService()->getAttributeVariant(3);

        $this->assertSame(3, $variant->getId());
        $this->assertSame(1, $variant->getAttributeId());
        $this->assertSame('Rouge', $variant->getName());
        $this->assertSame('Le rouge est un champ chromatique regroupant les couleurs vives situées sur le cercle chromatique entre l\'orange et les pourpres.', $variant->getDescription());
        $this->assertSame('rouge', $variant->getSlug());
        $this->assertSame('', $variant->getSeoTitle());
        $this->assertSame('', $variant->getSeoDescription());
        $this->assertSame(5, $variant->getImage()->getId());
    }

    public function testGetNonExistingAttributeVariant()
    {
        $this->expectException(NotFound::class);
        $this->buildCatalogService()->getAttributeVariant(404);
    }

    public function testGetAttributeVariants()
    {
        $variants = $this->buildCatalogService()->getAttributeVariants(1);

        $expectedVariants = [
            new AttributeVariant([
                'id' => 1,
                'attributeId' => 1,
                'name' => 'Bleu',
                'slug' => '',
                'image' => ['id' => 4],
                'description' => '',
            ]),
            new AttributeVariant([
                'id' => 2,
                'attributeId' => 1,
                'name' => 'Blanc',
                'slug' => 'blanc',
                'image' => null,
                'description' => '',
            ]),
            new AttributeVariant([
                'id' => 3,
                'attributeId' => 1,
                'name' => 'Rouge',
                'slug' => 'rouge',
                'image' => ['id' => 5],
                'description' => 'Le rouge est un champ chromatique regroupant les couleurs vives situées sur le cercle chromatique entre l\'orange et les pourpres.',
            ]),
        ];

        $this->assertEquals($expectedVariants, $variants);
    }

    public function testGetNonExistingAttributeVariants()
    {
        $this->expectException(NotFound::class);
        $this->buildCatalogService()->getAttributeVariants(404);
    }

    public function testGetProductWithOptions()
    {
        $catalogService = $this->buildCatalogService();
        $product = $catalogService->getProductById('3');

        $expectedDeclinations = [
            new Declination([
                'id' => '3_3_7',
                'code' => 'size_13',
                'isAvailable' => true,
                'supplierReference' => 'INFO-ECRAN-001',
                'price' => 15.5,
                'originalPrice' => 15.5,
                'crossedOutPrice' => null,
                'prices' => [
                    'priceWithTaxes' => 15.5,
                    'priceWithoutVat' => 15.18,
                    'vat' => 0.32,
                ],
                'greenTax' => 0,
                'amount' => 10,
                'infiniteStock' => false,
                'affiliateLink' => null,
                'images' => [],
                'isBrandNew' => true,
                'options' => [
                    [
                        'id' => 3,
                        'name' => 'size',
                        'variantId' => 7,
                        'variantName' => '13',
                    ],
                ],
                'company' => [
                    'id' => 3,
                    'name' => 'The World Company Inc.',
                    'slug' => 'the-world-company-inc',
                    'isProfessional' => true,
                    'image' => null,
                    'averageRating' => null,
                ],
            ]),
            new Declination([
                'id' => '3_3_8',
                'code' => 'size_15',
                'isAvailable' => true,
                'supplierReference' => 'INFO-ECRAN-001',
                'price' => 15.5,
                'originalPrice' => 15.5,
                'crossedOutPrice' => null,
                'prices' => [
                    'priceWithTaxes' => 15.5,
                    'priceWithoutVat' => 15.18,
                    'vat' => 0.32,
                ],
                'greenTax' => 0,
                'amount' => 10,
                'infiniteStock' => false,
                'affiliateLink' => null,
                'images' => [],
                'isBrandNew' => true,
                'options' => [
                    [
                        'id' => 3,
                        'name' => 'size',
                        'variantId' => 8,
                        'variantName' => '15',
                    ],
                ],
                'company' => [
                    'id' => 3,
                    'name' => 'The World Company Inc.',
                    'slug' => 'the-world-company-inc',
                    'isProfessional' => true,
                    'image' => null,
                    'averageRating' => null,
                ],
            ]),
            new Declination([
                'id' => '3_3_9',
                'code' => 'size_17',
                'isAvailable' => true,
                'supplierReference' => 'INFO-ECRAN-001',
                'price' => 15.5,
                'originalPrice' => 15.5,
                'crossedOutPrice' => null,
                'prices' => [
                    'priceWithTaxes' => 15.5,
                    'priceWithoutVat' => 15.18,
                    'vat' => 0.32,
                ],
                'greenTax' => 0,
                'amount' => 10,
                'infiniteStock' => false,
                'affiliateLink' => null,
                'images' => [],
                'isBrandNew' => true,
                'options' => [
                    [
                        'id' => 3,
                        'name' => 'size',
                        'variantId' => 9,
                        'variantName' => '17',
                    ],
                ],
                'company' => [
                    'id' => 3,
                    'name' => 'The World Company Inc.',
                    'slug' => 'the-world-company-inc',
                    'isProfessional' => true,
                    'image' => null,
                    'averageRating' => null,
                ],
            ]),
            new Declination([
                'id' => '3_3_10',
                'code' => 'size_21',
                'isAvailable' => true,
                'supplierReference' => 'INFO-ECRAN-001',
                'price' => 15.5,
                'originalPrice' => 15.5,
                'crossedOutPrice' => null,
                'prices' => [
                    'priceWithTaxes' => 15.5,
                    'priceWithoutVat' => 15.18,
                    'vat' => 0.32,
                ],
                'greenTax' => 0,
                'amount' => 10,
                'infiniteStock' => false,
                'affiliateLink' => null,
                'images' => [],
                'isBrandNew' => true,
                'options' => [
                    [
                        'id' => 3,
                        'name' => 'size',
                        'variantId' => 10,
                        'variantName' => '21',
                    ],
                ],
                'company' => [
                    'id' => 3,
                    'name' => 'The World Company Inc.',
                    'slug' => 'the-world-company-inc',
                    'isProfessional' => true,
                    'image' => null,
                    'averageRating' => null,
                ],
            ]),
        ];

        $expectedOption = [
            'id' => 3,
            'name' => 'size',
            'variants' => [
                [
                    'id' => 7,
                    'name' => '13',
                ],
                [
                    'id' => 8,
                    'name' => '15',
                ],
                [
                    'id' => 9,
                    'name' => '17',
                ],
                [
                    'id' => 10,
                    'name' => '21',
                ],
            ],
        ];

        $expectedOptions = [
            new Option($expectedOption),
        ];

        $this->assertSame('3', $product->getId());
        $this->assertEquals($expectedDeclinations, $product->getDeclinations());
        $this->assertEquals($expectedOptions, $product->getOptions());
        $this->assertTrue(in_array($product->getDeclinationFromOptions([7]), $expectedDeclinations));
        $this->assertEquals($expectedDeclinations[0], $product->getDeclinationFromOptions([7]));
    }

    public function testGetProductWithMultipleOptions()
    {
        $catalogService = $this->buildCatalogService();
        $product = $catalogService->getProductById('2');

        $expectedDeclinations = [
            new Declination([
                'id' => '2_1_1_2_5',
                'code' => 'color_white_connectivity_wireles',
                'isAvailable' => true,
                'supplierReference' => 'INFO-002',
                'price' => 15.5,
                'originalPrice' => 15.5,
                'crossedOutPrice' => null,
                'prices' => [
                    'priceWithTaxes' => 15.5,
                    'priceWithoutVat' => 15.18,
                    'vat' => 0.32,
                ],
                'greenTax' => 0,
                'amount' => 10,
                'infiniteStock' => false,
                'affiliateLink' => null,
                'images' => [],
                'isBrandNew' => false,
                'options' => [
                    [
                        'id' => 1,
                        'name' => 'color',
                        'variantId' => 1,
                        'variantName' => 'white',
                    ],
                    [
                        'id' => 2,
                        'name' => 'connectivity',
                        'variantId' => 5,
                        'variantName' => 'wireless',
                    ],
                ],
                'company' => [
                    'id' => 3,
                    'name' => 'The World Company Inc.',
                    'slug' => 'the-world-company-inc.',
                    'isProfessional' => true,
                    'image' => null,
                    'averageRating' => null,
                ],
            ]),
            new Declination([
                'id' => '2_1_1_2_6',
                'code' => 'color_white_connectivity_wired',
                'isAvailable' => true,
                'supplierReference' => 'INFO-002',
                'price' => 15.5,
                'originalPrice' => 15.5,
                'crossedOutPrice' => null,
                'prices' => [
                    'priceWithTaxes' => 15.5,
                    'priceWithoutVat' => 15.18,
                    'vat' => 0.32,
                ],
                'greenTax' => 0,
                'amount' => 10,
                'infiniteStock' => false,
                'affiliateLink' => null,
                'images' => [],
                'isBrandNew' => false,
                'options' => [
                    [
                        'id' => 1,
                        'name' => 'color',
                        'variantId' => 1,
                        'variantName' => 'white',
                    ],
                    [
                        'id' => 2,
                        'name' => 'connectivity',
                        'variantId' => 6,
                        'variantName' => 'wired',
                    ],
                ],
                'company' => [
                    'id' => 3,
                    'name' => 'The World Company Inc.',
                    'slug' => 'the-world-company-inc.',
                    'isProfessional' => true,
                    'image' => null,
                    'averageRating' => null,
                ],
            ]),
            new Declination([
                'id' => '2_1_2_2_5',
                'code' => 'color_black_connectivity_wireles',
                'isAvailable' => true,
                'supplierReference' => 'INFO-002',
                'price' => 15.5,
                'originalPrice' => 15.5,
                'crossedOutPrice' => null,
                'prices' => [
                    'priceWithTaxes' => 15.5,
                    'priceWithoutVat' => 15.18,
                    'vat' => 0.32,
                ],
                'greenTax' => 0,
                'amount' => 10,
                'infiniteStock' => null,
                'affiliateLink' => null,
                'images' => [],
                'isBrandNew' => false,
                'options' => [
                    [
                        'id' => 1,
                        'name' => 'color',
                        'variantId' => 2,
                        'variantName' => 'black',
                    ],
                    [
                        'id' => 2,
                        'name' => 'connectivity',
                        'variantId' => 5,
                        'variantName' => 'wireless',
                    ],
                ],
                'company' => [
                    'id' => 3,
                    'name' => 'The World Company Inc.',
                    'slug' => 'the-world-company-inc.',
                    'isProfessional' => true,
                    'image' => null,
                    'averageRating' => null,
                ],
            ]),
            new Declination([
                'id' => '2_1_2_2_6',
                'code' => 'color_black_connectivity_wired',
                'isAvailable' => true,
                'supplierReference' => 'INFO-002',
                'price' => 15.5,
                'originalPrice' => 15.5,
                'crossedOutPrice' => null,
                'prices' => [
                    'priceWithTaxes' => 15.5,
                    'priceWithoutVat' => 15.18,
                    'vat' => 0.32,
                ],
                'greenTax' => 0,
                'amount' => 10,
                'infiniteStock' => false,
                'affiliateLink' => null,
                'images' => [],
                'isBrandNew' => false,
                'options' => [
                    [
                        'id' => 1,
                        'name' => 'color',
                        'variantId' => 2,
                        'variantName' => 'black',
                    ],
                    [
                        'id' => 2,
                        'name' => 'connectivity',
                        'variantId' => 6,
                        'variantName' => 'wired',
                    ],
                ],
                'company' => [
                    'id' => 3,
                    'name' => 'The World Company Inc.',
                    'slug' => 'the-world-company-inc.',
                    'isProfessional' => true,
                    'image' => null,
                    'averageRating' => null,
                ],
            ]),
            new Declination([
                'id' => '2_1_3_2_5',
                'code' => 'color_blue_connectivity_wireless',
                'isAvailable' => true,
                'supplierReference' => 'INFO-002',
                'price' => 15.5,
                'originalPrice' => 15.5,
                'crossedOutPrice' => null,
                'prices' => [
                    'priceWithTaxes' => 15.5,
                    'priceWithoutVat' => 15.18,
                    'vat' => 0.32,
                ],
                'greenTax' => 0,
                'amount' => 10,
                'infiniteStock' => false,
                'affiliateLink' => null,
                'images' => [],
                'isBrandNew' => false,
                'options' => [
                    [
                        'id' => 1,
                        'name' => 'color',
                        'variantId' => 3,
                        'variantName' => 'blue',
                    ],
                    [
                        'id' => 2,
                        'name' => 'connectivity',
                        'variantId' => 5,
                        'variantName' => 'wireless',
                    ],
                ],
                'company' => [
                    'id' => 3,
                    'name' => 'The World Company Inc.',
                    'slug' => 'the-world-company-inc.',
                    'isProfessional' => true,
                    'image' => null,
                    'averageRating' => null,
                ],
            ]),
            new Declination([
                'id' => '2_1_3_2_6',
                'code' => 'color_blue_connectivity_wired',
                'isAvailable' => true,
                'supplierReference' => 'INFO-002',
                'price' => 15.5,
                'originalPrice' => 15.5,
                'crossedOutPrice' => null,
                'prices' => [
                    'priceWithTaxes' => 15.5,
                    'priceWithoutVat' => 15.18,
                    'vat' => 0.32,
                ],
                'greenTax' => 0,
                'amount' => 10,
                'infiniteStock' => null,
                'affiliateLink' => null,
                'images' => [],
                'isBrandNew' => false,
                'options' => [
                    [
                        'id' => 1,
                        'name' => 'color',
                        'variantId' => 3,
                        'variantName' => 'blue',
                    ],
                    [
                        'id' => 2,
                        'name' => 'connectivity',
                        'variantId' => 6,
                        'variantName' => 'wired',
                    ],
                ],
                'company' => [
                    'id' => 3,
                    'name' => 'The World Company Inc.',
                    'slug' => 'the-world-company-inc.',
                    'isProfessional' => true,
                    'image' => null,
                    'averageRating' => null,
                ],
            ]),
            new Declination([
                'id' => '2_1_4_2_5',
                'code' => 'color_red_connectivity_wireless',
                'isAvailable' => true,
                'supplierReference' => 'INFO-002',
                'price' => 15.5,
                'originalPrice' => 15.5,
                'crossedOutPrice' => null,
                'prices' => [
                    'priceWithTaxes' => 15.5,
                    'priceWithoutVat' => 15.18,
                    'vat' => 0.32,
                ],
                'greenTax' => 0,
                'amount' => 10,
                'infiniteStock' => false,
                'affiliateLink' => null,
                'images' => [],
                'isBrandNew' => false,
                'options' => [
                    [
                        'id' => 1,
                        'name' => 'color',
                        'variantId' => 4,
                        'variantName' => 'red',
                    ],
                    [
                        'id' => 2,
                        'name' => 'connectivity',
                        'variantId' => 5,
                        'variantName' => 'wireless',
                    ],
                ],
                'company' => [
                    'id' => 3,
                    'name' => 'The World Company Inc.',
                    'slug' => 'the-world-company-inc.',
                    'isProfessional' => true,
                    'image' => null,
                    'averageRating' => null,
                ],
            ]),
            new Declination([
                'id' => '2_1_4_2_6',
                'code' => 'color_red_connectivity_wired',
                'isAvailable' => true,
                'supplierReference' => 'INFO-002',
                'price' => 15.5,
                'originalPrice' => 15.5,
                'crossedOutPrice' => null,
                'prices' => [
                    'priceWithTaxes' => 15.5,
                    'priceWithoutVat' => 15.18,
                    'vat' => 0.32,
                ],
                'greenTax' => 0,
                'amount' => 10,
                'infiniteStock' => false,
                'affiliateLink' => null,
                'images' => [],
                'isBrandNew' => false,
                'options' => [
                    [
                        'id' => 1,
                        'name' => 'color',
                        'variantId' => 4,
                        'variantName' => 'red',
                    ],
                    [
                        'id' => 2,
                        'name' => 'connectivity',
                        'variantId' => 6,
                        'variantName' => 'wired',
                    ],
                ],
                'company' => [
                    'id' => 3,
                    'name' => 'The World Company Inc.',
                    'slug' => 'the-world-company-inc.',
                    'isProfessional' => true,
                    'image' => null,
                    'averageRating' => null,
                ],
            ]),
        ];

        $this->assertSame('2', $product->getId());

        $this->assertEquals($expectedDeclinations[0], $product->getDeclinationFromOptions([1, 5]));
        $this->assertEquals($expectedDeclinations[1], $product->getDeclinationFromOptions([1, 6]));
        $this->assertEquals($expectedDeclinations[2], $product->getDeclinationFromOptions([2, 5]));
        $this->assertEquals($expectedDeclinations[3], $product->getDeclinationFromOptions([2, 6]));
        $this->assertEquals($expectedDeclinations[4], $product->getDeclinationFromOptions([3, 5]));
        $this->assertEquals($expectedDeclinations[5], $product->getDeclinationFromOptions([3, 6]));
        $this->assertEquals($expectedDeclinations[6], $product->getDeclinationFromOptions([4, 5]));
        $this->assertEquals($expectedDeclinations[7], $product->getDeclinationFromOptions([4, 6]));
    }

    public function testGetProductWithGeolocation()
    {
        $location = $this->buildCatalogService()->getProductById('7')->getGeolocation();
        $this->assertInstanceOf(ProductLocation::class, $location);
        $this->assertSame(45.75845, $location->getLatitude());
        $this->assertSame(4.799044, $location->getLongitude());
        $this->assertSame('Lyon 5e', $location->getLabel());
        $this->assertSame('69005', $location->getZipcode());
    }

    public function testGetProductWithAttachments()
    {
        $attachments = $this->buildCatalogService()->getProductById('10')->getAttachments();

        $this->assertCount(2, $attachments);
        $this->assertContainsOnly(ProductAttachment::class, $attachments);

        $attachment = $attachments[0];

        $this->assertNotEmpty($attachment->getId());
        $this->assertSame('Manuel de montage', $attachment->getLabel());

        $response = $this->buildApiClient()->rawRequest('GET', $attachment->getUrl());
        $this->assertSame('application/pdf', $response->getHeaderLine('Content-Type'));
        $this->assertStringStartsWith('attachment; filename="', $response->getHeaderLine('Content-Disposition'));
    }

    public function testGetProductWithVideo()
    {
        $video = $this->buildCatalogService()->getProductById('3')->getVideo();
        $this->assertInstanceOf(ProductVideo::class, $video);
        $this->assertSame('//s3-eu-west-1.amazonaws.com/wizachatest/videos/414375b2-61cb-4260-b82b-4a2636cb5673/480-00001.png', (string) $video->getThumbnailUrl());
        $this->assertSame('//s3-eu-west-1.amazonaws.com/wizachatest/videos/414375b2-61cb-4260-b82b-4a2636cb5673/480.mp4', (string) $video->getVideoUrl());
    }

    public function testReportingProduct()
    {
        $report = (new ProductReport())
            ->setProductId('1')
            ->setReporterEmail('user@wizaplace.com')
            ->setReporterName('Mr. User')
            ->setMessage('I am shocked!');

        $this->buildCatalogService()->reportProduct($report);
        // We don't have a way to check that the report was saved.
        // So we just check that a request was made successfully
        $this->assertCount(1, static::$historyContainer);
        /** @var Response $response */
        $response = static::$historyContainer[0]['response'];
        $this->assertSame(204, $response->getStatusCode());
    }

    public function testReportingNonExistingProduct()
    {
        $report = (new ProductReport())
            ->setProductId('404')
            ->setReporterEmail('user@wizaplace.com')
            ->setReporterName('User')
            ->setMessage('Should get a 404');

        $this->expectException(ProductNotFound::class);
        $this->buildCatalogService()->reportProduct($report);
    }

    public function testReportingProductWithInvalidEmail()
    {
        $report = (new ProductReport())
            ->setProductId('1')
            ->setReporterEmail('user@@wizaplace.com')
            ->setReporterName('User')
            ->setMessage('Should get a 400');

        $this->expectException(SomeParametersAreInvalid::class);
        $this->expectExceptionCode(400);
        $this->buildCatalogService()->reportProduct($report);
    }

    public function testReportingProductWithMissingField()
    {
        $report = (new ProductReport())
            ->setProductId('1')
            ->setReporterEmail('user@wizaplace.com')
            ->setReporterName('User');

        $this->expectException(SomeParametersAreInvalid::class);
        $this->buildCatalogService()->reportProduct($report);
    }

    public function testGetBrandFromWrongType()
    {
        $this->expectException(\TypeError::class);
        $this->buildCatalogService()->getBrand(new Declination([
            'id' => 1,
            'code' => '0123456789123',
            'isAvailable' => true,
            'supplierReference' => 'test-supplier',
            'price' => 10,
            'originalPrice' => 10,
            'crossedOutPrice' => 10,
            'prices' => [
                'priceWithTaxes' => 10,
                'priceWithoutVat' => 8,
                'vat' => 2,
            ],
            'greenTax' => 0,
            'amount' => 14,
            'infiniteStock' => false,
            'affiliateLink' => '',
            'images' => [],
            'options' => [],
            'company' => [
                'id' => 1,
                'name' => 'Test company',
                'slug' => 'test-company',
                'isProfessional' => true,
                'averageRating' => null,
                'image' => null,
            ],
        ]));
    }

    public function testNumericFacet()
    {
        $catalogService = $this->buildCatalogService();

        $result = $catalogService->search('');

        $priceFacet = null;
        foreach ($result->getFacets() as $facet) {
            if ($facet->getName() === 'price') {
                /** @var NumericFacet $priceFacet */
                $priceFacet = $facet;
                break;
            }
        }

        $this->assertNotNull($priceFacet, 'Price facet not found');
        $this->assertInstanceOf(NumericFacet::class, $priceFacet);

        $this->assertInternalType('float', $priceFacet->getMin());
        $this->assertGreaterThanOrEqual(0.0, $priceFacet->getMin());
        $this->assertInternalType('float', $priceFacet->getMax());
        $this->assertGreaterThan(0.0, $priceFacet->getMax());
    }

    public function testGetProductWithSeoData()
    {
        $catalogService = $this->buildCatalogService();

        $product = $catalogService->getProductById('4');

        $this->assertSame('4', $product->getId());
        $this->assertSame('Corsair Gaming VOID Pro RGB Dolby 7.1 Sans fil - Edition Carbon', $product->getName());

        $this->assertSame('Micro-Casque Corsair Gaming', $product->getSeoTitle());
        $this->assertSame('Achat Micro-casque Corsair Gaming VOID Pro RGB Dolby 7.1 Wireless - Edition Carbon sur notre MarketPlace. Casque-micro 7.1 sans fil pour gamer.', $product->getSeoDescription());
    }

    public function testGetProductWithoutSeoDescription()
    {
        $catalogService = $this->buildCatalogService();

        $product = $catalogService->getProductById('5');

        $this->assertSame('5', $product->getId());
        $this->assertSame('Logitech G430 Casque Gaming pour PC Gaming, PS4, Xbox One with 7.1 Dolby Surround', $product->getName());

        $this->assertSame('Micro-Casque Logitech Gaming', $product->getSeoTitle());
        $this->assertSame('', $product->getSeoDescription());
    }

    public function testGetProductWithoutSeoTitle()
    {
        $catalogService = $this->buildCatalogService();

        $product = $catalogService->getProductById('6');

        $this->assertSame('6', $product->getId());
        $this->assertSame('Casque Gaming Razer ManO\'War Sans fil 7.1 Surround (PC/PS4)', $product->getName());

        $this->assertSame('', $product->getSeoTitle());
        $this->assertSame('Achat Micro-casque Razer ManO\'War 7.1 Wireless - Edition Carbon sur notre MarketPlace. Casque-micro 7.1 sans fil pour gamer.', $product->getSeoDescription());
    }

    private function buildCatalogService(): CatalogServiceInterface
    {
        return new CatalogService($this->buildApiClient());
    }

    private function sortAttributesById(array &$attributes): void
    {
        usort($attributes, [$this, 'compareAttributesById']);
    }

    /**
     * Had to make a method of it (instead of a closure) because of a phpcs bug
     */
    private function compareAttributesById(ProductAttribute $a, ProductAttribute $b): int
    {
        return $a->getId() <=> $b->getId();
    }

    /**
     * Recursive test for divisions
     *
     * @param Division[] $divisions
     */
    private function divisionTester(array $divisions)
    {
        foreach ($divisions as $division) {
            $this->assertInstanceOf(Division::class, $division);
            if (!empty($division->getChildren())) {
                $this->divisionTester($division->getChildren());
            }
        }
    }
}
