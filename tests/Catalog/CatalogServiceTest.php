<?php

/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Tests\Catalog;

use GuzzleHttp\Psr7\Response;
use Wizaplace\SDK\Catalog\DeclinationImages;
use Wizaplace\SDK\Catalog\CompanyFilter;
use Wizaplace\SDK\SortDirection;
use Wizaplace\SDK\Catalog\Attribute;
use Wizaplace\SDK\Catalog\AttributeType;
use Wizaplace\SDK\Catalog\AttributeVariant;
use Wizaplace\SDK\Catalog\AttributeFilter;
use Wizaplace\SDK\Catalog\CatalogService;
use Wizaplace\SDK\Catalog\CatalogServiceInterface;
use Wizaplace\SDK\Catalog\Category;
use Wizaplace\SDK\Catalog\CategorySortCriteria;
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
use Wizaplace\SDK\Pim\Option\SystemOption;
use Wizaplace\SDK\Pim\Product\ProductStatus;
use Wizaplace\SDK\Tests\ApiTestCase;

/**
 * @see CatalogService
 */
final class CatalogServiceTest extends ApiTestCase
{
    public function testGetAllProducts(): void
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

    public function testGetProductById(): void
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
        $this->assertSame('product', $product->getProductTemplateType());
        $this->assertFalse($product->hasInfiniteStock());

        $this->assertCount(3, $product->getShippings());

        /** @var Shipping $shipping */
        $shipping = current($product->getShippings());
        $this->assertSame('Lettre prioritaire', $shipping->getName());
        $this->assertNull($shipping->getImage());
        $this->assertSame(0.0, $shipping->getFirstRate());
        $this->assertSame(0.0, $shipping->getNextRate());
        $this->assertSame('', $shipping->getDeliveryTime());
        static::assertNull($shipping->getCarriagePaidThreshold());

        $this->assertSame('', $product->getShortDescription());
        $this->assertSame('INFO-001', $product->getSupplierReference());
        $this->assertTrue($product->isTransactional());
        $this->assertSame(0.0, $product->getGreenTax());
        $this->assertSame(1.23, $product->getWeight());
        static::assertSame(2.5, $product->getAverageRating());
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
        $this->assertEquals(5.0, $companies[0]->getAverageRating());
        $this->assertNull($companies[0]->getImage());
        $this->assertTrue($companies[0]->isProfessional());

        $this->assertGreaterThanOrEqual(1400000000, $product->getCreatedAt()->getTimestamp());
        $this->assertGreaterThanOrEqual(0, $product->getCreatedAt()->diff($product->getUpdatedAt())->s);
        $this->assertInstanceOf(\DateTimeInterface::class, $product->getAvailableSince());
        $this->assertSame(1386284400, $product->getAvailableSince()->getTimestamp());

        $this->assertEmpty($product->getImages());

        foreach ($product->getOffers() as $offer) {
            $this->assertSame(1, $offer->getProductId());
            $this->assertSame(3, $offer->getCompanyId());
            $this->assertSame(67.9, $offer->getPrice());
            $this->assertSame([], $offer->getDivisions());
            $this->assertEquals(ProductStatus::ENABLED(), $offer->getStatus());
        }
    }

    public function testGetProductByIdWithIsUpToDate(): void
    {
        $catalogService = $this->buildCatalogService();
        $product = $catalogService->getProductById('1');
        static::assertSame(true, $product->isUpToDate());

        $product = $catalogService->getProductById('2');
        static::assertSame(false, $product->isUpToDate());
    }

    public function testGetDeclinationByID(): void
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

    public function testGetProductsByCode(): void
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
        $this->assertSame('Lettre prioritaire', $shipping->getName());
        $this->assertNull($shipping->getImage());
        $this->assertSame(0.0, $shipping->getFirstRate());
        $this->assertSame(0.0, $shipping->getNextRate());
        $this->assertSame('', $shipping->getDeliveryTime());
        static::assertNull($shipping->getCarriagePaidThreshold());

        $this->assertSame('', $product->getShortDescription());
        $this->assertSame('INFO-001', $product->getSupplierReference());
        $this->assertTrue($product->isTransactional());
        $this->assertSame(0.0, $product->getGreenTax());
        $this->assertSame(1.23, $product->getWeight());
        static::assertSame(2.5, $product->getAverageRating());
        $this->assertNull($product->getGeolocation());
        $this->assertNull($product->getVideo());
        $this->assertCount(0, $product->getAttachments());
        $this->assertNull($catalogService->getBrand($product));
        $this->assertEmpty($product->getOtherOffers($product->getDeclinations()[0]));
        $this->assertTrue($product->getDeclinations()[0]->isAvailable());
        $this->assertSame('product', $product->getProductTemplateType());

        $companies = $product->getCompanies();
        $this->assertCount(1, $companies);
        $this->assertSame(3, $companies[0]->getId());
        $this->assertSame('The World Company Inc.', $companies[0]->getName());
        $this->assertSame('the-world-company-inc', $companies[0]->getSlug());
        $this->assertEquals(5.0, $companies[0]->getAverageRating());
        $this->assertNull($companies[0]->getImage());
        $this->assertTrue($companies[0]->isProfessional());

        $this->assertGreaterThanOrEqual(1400000000, $product->getCreatedAt()->getTimestamp());
        $this->assertGreaterThanOrEqual(0, $product->getCreatedAt()->diff($product->getUpdatedAt())->s);
        $this->assertSame(1386284400, $product->getAvailableSince()->getTimestamp());
        $this->assertInstanceOf(\DateTimeInterface::class, $product->getAvailableSince());

        $this->assertEmpty($product->getImages());
    }

    public function testGetProductsBySupplierReference(): void
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
        $this->assertSame('product', $product->getProductTemplateType());

        /** @var Shipping $shipping */
        $shipping = current($product->getShippings());
        $this->assertSame('Lettre prioritaire', $shipping->getName());
        $this->assertNull($shipping->getImage());
        $this->assertSame(0.0, $shipping->getFirstRate());
        $this->assertSame(0.0, $shipping->getNextRate());
        $this->assertSame('', $shipping->getDeliveryTime());
        static::assertNull($shipping->getCarriagePaidThreshold());

        $this->assertSame('', $product->getShortDescription());
        $this->assertSame('INFO-001', $product->getSupplierReference());
        $this->assertTrue($product->isTransactional());
        $this->assertSame(0.0, $product->getGreenTax());
        $this->assertSame(1.23, $product->getWeight());
        static::assertSame(2.5, $product->getAverageRating());
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
        $this->assertEquals(5.0, $companies[0]->getAverageRating());
        $this->assertNull($companies[0]->getImage());
        $this->assertTrue($companies[0]->isProfessional());

        $this->assertGreaterThanOrEqual(1400000000, $product->getCreatedAt()->getTimestamp());
        $this->assertGreaterThanOrEqual(0, $product->getCreatedAt()->diff($product->getUpdatedAt())->s);
        $this->assertInstanceOf(\DateTimeInterface::class, $product->getAvailableSince());
        $this->assertSame(1386284400, $product->getAvailableSince()->getTimestamp());

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
        $this->assertCount(5, $products);

        $expected = ['1', '10', '2', '3', '9'];

        foreach ($products as $key => $product) {
            $this->assertSame($expected[$key], $product->getId());
        }
    }

    public function testGetProductByFiltersNoMvp(): void
    {
        $catalogService = $this->buildCatalogService();

        // Filter with companyId
        $filters = (new ProductFilter())
            ->setCodes(['20230495447'])
            ->setSupplierRefs(['TEST-MVP'])
        ;

        $products = $catalogService->getProductsByFilters($filters, false);
        $this->assertCount(2, $products);

        $expected = ['11', '12'];

        foreach ($products as $key => $product) {
            $this->assertSame($expected[$key], $product->getId());
        }
    }

    public function testGetProductByFiltersWithCompany(): void
    {
        $catalogService = $this->buildCatalogService();

        // Filter with companyId
        $filters = (new ProductFilter())
            ->setCodes(['20230495447'])
            ->setSupplierRefs(['TEST-MVP'])
            ->setCompanyId(3)
        ;

        $products = $catalogService->getProductsByFilters($filters, false);
        $this->assertCount(1, $products);

        $expected = ['11'];

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

    public function testGetMVPById(): void
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

    public function testGetProductsByMVPId(): void
    {
        $list = $this->buildCatalogService()->getProductsByMvpId('0adaf6bc-d362-34be-b72f-42d5aa3b4a4e');
        static::assertCount(1, $list);
        $mvp = reset($list);
        static::assertInstanceOf(Product::class, $mvp);

        static::assertSame('0adaf6bc-d362-34be-b72f-42d5aa3b4a4e', $mvp->getId());
        static::assertSame('Test MVP', $mvp->getName());
        static::assertCount(2, $mvp->getCompanies());
        static::assertCount(2, $mvp->getDeclinations());
        $otherOffers = $mvp->getOtherOffers($mvp->getDeclinations()[0]);
        static::assertSame([$mvp->getDeclinations()[1]], $otherOffers);
    }

    public function testGetProductAttributes(): void
    {
        $catalogService = $this->buildCatalogService();

        $product = $catalogService->getProductById('1');

        static::assertSame('1', $product->getId());

        static::assertSame('Couleur 2', $product->getAttributes()[0]->getName());
        static::assertSame('azerty 6', $product->getAttributes()[0]->getCode());
    }

    public function testGetProductWithComplexAttributes(): void
    {
        $catalogService = $this->buildCatalogService();

        $product = $catalogService->getProductById('0adaf6bc-d362-34be-b72f-42d5aa3b4a4e');

        $this->assertSame('0adaf6bc-d362-34be-b72f-42d5aa3b4a4e', $product->getId());
        $this->assertSame('eius-et-velit-eum-corrupti-voluptatibus-velit-totam', $product->getSlug());

        $expectedAttributes = [
            new ProductAttribute(
                [
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
                ]
            ),
            new ProductAttribute(
                [
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
                ]
            ),
            new ProductAttribute(
                [
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
                ]
            ),
            new ProductAttribute(
                [
                    'id' => 9,
                    'name' => 'Marque',
                    'values' => [
                        [
                            'id' => 20,
                            'attributeId' => 9,
                            'name' => 'Puma',
                            'slug' => 'puma',
                            'image' => [
                                'id' => 13,
                            ],
                        ],
                    ],
                    'value' => ['Puma'],
                    'valueIds' => [20],
                    'children' => [],
                    'imageUrls' => [],
                    'type' => AttributeType::LIST_BRAND()->getValue(),
                ]
            ),
            new ProductAttribute(
                [
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
                ]
            ),
            new ProductAttribute(
                [
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
                ]
            ),
            new ProductAttribute(
                [
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
                ]
            ),
            new ProductAttribute(
                [
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
                ]
            ),
            new ProductAttribute(
                [
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
                ]
            ),
            new ProductAttribute(
                [
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
                ]
            ),
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

    public function testGetNonExistingProductById(): void
    {
        $catalogService = $this->buildCatalogService();

        $this->expectException(ProductNotFound::class);
        $catalogService->getProductById('404');
    }

    public function testSearchReturnMaxPriceAdjustment(): void
    {
        $catalogService = $this->buildCatalogService();

        $result = $catalogService->search('Product with shippings');
        $products = $result->getProducts();
        static::assertCount(1, $products);
        $product = $products[0];
        static::assertNull($product->getMaxPriceAdjustment());
    }

    public function testSearchOneProductByName(): void
    {
        $catalogService = $this->buildCatalogService();

        $result = $catalogService->search('Product with shippings');

        $products = $result->getProducts();
        $this->assertCount(1, $products);

        $product = $products[0];
        $this->assertSame('7', $product->getId());
        $this->assertSame('product-with-shippings', $product->getSlug());
        $this->assertTrue($product->isAvailable());
        $this->assertGreaterThan(1400000000, $product->getCreatedAt()->getTimestamp());
        $this->assertSame(0, $product->getAverageRating());
        $this->assertSame('Product with shippings', $product->getName());
        $this->assertContainsOnly(Condition::class, $product->getConditions());
        $this->assertEquals([Condition::BRAND_NEW()], $product->getConditions());
        $this->assertTrue((new DeclinationId('7_0'))->equals($product->getMainDeclinationId()), "got " . $product->getMainDeclinationId());
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
        $this->assertSame('the-world-company-inc', $companies[0]->getSlug());
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
        $this->assertCount(11, $facets);
        $this->assertContainsOnly(Facet::class, $facets);
        /** @var ListFacet $categoryFacet */
        $categoryFacet = $facets[0];
        $this->assertSame('categories', $categoryFacet->getName());
        $this->assertSame('Catégorie', $categoryFacet->getLabel());
        $this->assertInstanceOf(ListFacet::class, $categoryFacet);
        $this->assertEquals(
            [
                6 => new \Wizaplace\SDK\Catalog\Facet\ListFacetValue(
                    [
                        'label' => 'Special category dedicated to specific tests',
                        'count' => '1',
                        'position' => '0',
                    ]
                ),
            ],
            $categoryFacet->getValues()
        );

        $this->assertNull($catalogService->getBrand($product));
    }

    public function testSearchMVP(): void
    {
        $catalogService = $this->buildCatalogService();

        $result = $catalogService->search('Test MVP');

        $products = $result->getProducts();
        $this->assertContainsOnly(ProductSummary::class, $products);
        $this->assertCount(1, $products);

        $product = $products[0];
        $this->assertSame('c659af24-56b8-48fd-8b86-34ddff28ba1b', $product->getId());
        $this->assertTrue((new DeclinationId('8_0'))->equals($product->getMainDeclinationId()), "got " . $product->getMainDeclinationId());
    }

    public function testSearchProductWithComplexAttributes(): void
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
        $this->assertEquals(
            [
                new ProductAttributeValue(
                    [
                        'id' => 2,
                        'attributeId' => 1,
                        'name' => 'Blanc',
                        'slug' => 'blanc',
                        'image' => null,
                    ]
                ),
                new ProductAttributeValue(
                    [
                        'id' => 3,
                        'attributeId' => 1,
                        'name' => 'Rouge',
                        'slug' => 'rouge',
                        'image' => null,
                    ]
                ),
            ],
            $attributesMap[1]->getValues()
        );
        $this->assertTrue(AttributeType::CHECKBOX_MULTIPLE()->equals($attributesMap[1]->getType()));

        // Free attributes
        $this->assertEquals(
            [
                new ProductAttributeValue(
                    [
                        'id' => null,
                        'attributeId' => null,
                        'name' => 'valeur simple du free attribute #12M%M_°09£*/.?',
                        'slug' => '',
                        'image' => null,
                    ]
                ),
            ],
            $freeAttributesMap['Free attribute simple']->getValues()
        );

        $this->assertEquals(
            [
                new ProductAttributeValue(
                    [
                        'id' => null,
                        'attributeId' => null,
                        'name' => 'réponse - 1 #',
                        'slug' => '',
                        'image' => null,
                    ]
                ),
                new ProductAttributeValue(
                    [
                        'id' => null,
                        'attributeId' => null,
                        'name' => 'réponse - 2 @',
                        'slug' => '',
                        'image' => null,
                    ]
                ),
                new ProductAttributeValue(
                    [
                        'id' => null,
                        'attributeId' => null,
                        'name' => '4985',
                        'slug' => '',
                        'image' => null,
                    ]
                ),
            ],
            $freeAttributesMap['Free attribute multiple']->getValues()
        );

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

    public function testGetCompanyById(): void
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

    public function testGetCompanyByIdWithCorporateName(): void
    {
        $catalogService = $this->buildCatalogService();

        $company = $catalogService->getCompanyById(1);
        static::assertSame(1, $company->getId());
        static::assertSame('Marchand de test', $company->getName());
        static::assertSame('Marchand de test', $company->getCorporateName());

        $company = $catalogService->getCompanyById(8);
        static::assertSame(8, $company->getId());
        static::assertSame('Super C2C Company', $company->getName());
        static::assertSame('Corporate name C2C', $company->getCorporateName());
    }

    public function testGetCompanies(): void
    {
        $catalogService = $this->buildCatalogService();

        $companies = $catalogService->getCompanies();

        $this->assertInternalType('array', $companies);
        $this->assertGreaterThanOrEqual(4, \count($companies));
        $this->assertContainsOnly(CompanyListItem::class, $companies);
        $this->assertSame(1, $companies[0]->getId());
        $this->assertSame('Marchand de test', $companies[0]->getName());
        $this->assertFalse($companies[0]->isProfessional());
        $this->assertSame('FR', $companies[0]->getFullAddress()->getCountry());
        $this->assertSame('Rue des marketplaces', $companies[0]->getFullAddress()->getAddress());
        $this->assertSame('Lyon', $companies[0]->getFullAddress()->getCity());
        $this->assertSame('69000', $companies[0]->getFullAddress()->getZipCode());

        //------------------
        // Test extra data
        //------------------

        // With returned values
        $this->assertSame(['field' => 'value', 'field2' => 'value2'], $companies[0]->getExtra());
        // Without values
        $this->assertSame([], $companies[1]->getExtra());
        // With empty values
        $this->assertSame([], $companies[4]->getExtra());
    }

    public function testGetCompaniesWithCorporateName(): void
    {
        $catalogService = $this->buildCatalogService();

        $companies = $catalogService->getCompanies();

        static::assertInternalType('array', $companies);
        static::assertContainsOnly(CompanyListItem::class, $companies);
        static::assertSame('Marchand de test', $companies[0]->getCorporateName());
        static::assertSame('ACME', $companies[1]->getCorporateName());
        static::assertSame('The World Company Inc.', $companies[2]->getCorporateName());
    }

    public function testGetC2cCompanyById(): void
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

    public function testGetCompanyWhichDoesntExist(): void
    {
        $this->expectException(CompanyNotFound::class);

        $this->buildCatalogService()->getCompanyById(404);
    }

    public function testGetCategory(): void
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

    public function testGetCategoryWithAgeLimit(): void
    {
        $category = $this->buildCatalogService()->getCategory(1);
        static::assertSame(1, $category->getId());
        static::assertSame(13, $category->getAgeLimit());

        $category = $this->buildCatalogService()->getCategory(2);
        static::assertSame(2, $category->getId());
        static::assertSame(0, $category->getAgeLimit());

        $category = $this->buildCatalogService()->getCategory(3);
        static::assertSame(3, $category->getId());
        static::assertSame(18, $category->getAgeLimit());
    }

    public function testGetCategoryTree(): void
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

    public function testGetCategoryTreeWithFilterNameAsc(): void
    {
        $categoryTree = $this->buildCatalogService()->getCategoryTree(CategorySortCriteria::NAME, SortDirection::ASC);
        foreach ($categoryTree as $key => $categorie) {
            if ($key > 0) {
                static::GreaterThanOrEqual($categoryTree[$key - 1]->getCategory()->getName(), $categorie->getCategory()->getName());
            }
            foreach ($categorie->getChildren() as $key2 => $children) {
                if ($key2 > 0) {
                    static::assertGreaterThanOrEqual($categorie->getChildren()[$key2 - 1]->getCategory()->getName(), $children->getCategory()->getName());
                }
            }
        }
    }

    public function testGetCategoryTreeWithFilterNameDesc(): void
    {
        $categoryTree = $this->buildCatalogService()->getCategoryTree(CategorySortCriteria::NAME, SortDirection::DESC);
        foreach ($categoryTree as $key => $categorie) {
            if ($key > 0) {
                static::assertLessThanOrEqual($categoryTree[$key - 1]->getCategory()->getName(), $categorie->getCategory()->getName());
            }
            foreach ($categorie->getChildren() as $key2 => $children) {
                if ($key2 > 0) {
                    static::assertLessThanOrEqual($categorie->getChildren()[$key2 - 1]->getCategory()->getName(), $children->getCategory()->getName());
                }
            }
        }
    }

    public function testGetCategoryTreeWithFilterIdAsc(): void
    {
        $categoryTree = $this->buildCatalogService()->getCategoryTree(CategorySortCriteria::ID, SortDirection::ASC);
        foreach ($categoryTree as $key => $categorie) {
            if ($key > 0) {
                static::assertGreaterThanOrEqual($categoryTree[$key - 1]->getCategory()->getId(), $categorie->getCategory()->getId());
            }
            foreach ($categorie->getChildren() as $key2 => $children) {
                if ($key2 > 0) {
                    static::assertGreaterThanOrEqual($categoryTree[$key2 - 1]->getCategory()->getId(), $children->getCategory()->getId());
                }
            }
        }
    }

    public function testGetCategoryTreeWithFilterIdDesc(): void
    {
        $categoryTree = $this->buildCatalogService()->getCategoryTree(CategorySortCriteria::ID, SortDirection::DESC);
        foreach ($categoryTree as $key => $categorie) {
            if ($key > 0) {
                static::assertLessThanOrEqual($categoryTree[$key - 1]->getCategory()->getId(), $categorie->getCategory()->getId());
            }
            foreach ($categorie->getChildren() as $key2 => $children) {
                if ($key2 > 0) {
                    static::assertLessThanOrEqual($categorie->getChildren()[$key2 - 1]->getCategory()->getId(), $children->getCategory()->getId());
                }
            }
        }
    }

    public function testGetCategoryTreeWithFilterProductCountAsc(): void
    {
        $categoryTree = $this->buildCatalogService()->getCategoryTree(CategorySortCriteria::PRODUCT_COUNT, SortDirection::ASC);
        foreach ($categoryTree as $key => $categorie) {
            if ($key > 0) {
                static::assertGreaterThanOrEqual($categoryTree[$key - 1]->getCategory()->getProductCount(), $categorie->getCategory()->getProductCount());
            }
            foreach ($categorie->getChildren() as $key2 => $children) {
                if ($key2 > 0) {
                    static::assertGreaterThanOrEqual($categorie->getChildren()[$key2 - 1]->getCategory()->getProductCount(), $children->getCategory()->getProductCount());
                }
            }
        }
    }

    public function testGetCategoryTreeWithFilterProductCountDesc(): void
    {
        $categoryTree = $this->buildCatalogService()->getCategoryTree(CategorySortCriteria::PRODUCT_COUNT, SortDirection::DESC);
        foreach ($categoryTree as $key => $categorie) {
            if ($key > 0) {
                static::assertLessThanOrEqual($categoryTree[$key - 1]->getCategory()->getProductCount(), $categorie->getCategory()->getProductCount());
            }
            foreach ($categorie->getChildren() as $key2 => $children) {
                if ($key2 > 0) {
                    static::assertLessThanOrEqual($categorie->getChildren()[$key2 - 1]->getCategory()->getProductCount(), $children->getCategory()->getProductCount());
                }
            }
        }
    }

    public function testGetCategoryTreeWithFilterPositionAsc(): void
    {
        $categoryTree = $this->buildCatalogService()->getCategoryTree(CategorySortCriteria::POSITION, SortDirection::ASC);
        foreach ($categoryTree as $key => $categorie) {
            if ($key > 0) {
                static::assertGreaterThanOrEqual($categoryTree[$key - 1]->getCategory()->getPosition(), $categorie->getCategory()->getPosition());
            }
            foreach ($categorie->getChildren() as $key2 => $children) {
                if ($key2 > 0) {
                    static::assertGreaterThanOrEqual($categorie->getChildren()[$key2 - 1]->getCategory()->getPosition(), $children->getCategory()->getPosition());
                }
            }
        }
    }

    public function testGetCategoryTreeWithFilterPositionDesc(): void
    {
        $categoryTree = $this->buildCatalogService()->getCategoryTree(CategorySortCriteria::POSITION, SortDirection::DESC);
        foreach ($categoryTree as $key => $categorie) {
            if ($key > 0) {
                static::assertLessThanOrEqual($categoryTree[$key - 1]->getCategory()->getPosition(), $categorie->getCategory()->getPosition());
            }
            foreach ($categorie->getChildren() as $key2 => $children) {
                if ($key2 > 0) {
                    static::assertLessThanOrEqual($categorie->getChildren()[$key2 - 1]->getCategory()->getPosition(), $children->getCategory()->getPosition());
                }
            }
        }
    }

    public function testGetCategoryTreeWithoutFilter(): void
    {
        $categoryTree = $this->buildCatalogService()->getCategoryTree();
        foreach ($categoryTree as $key => $categorie) {
            if ($key > 0) {
                static::assertGreaterThanOrEqual($categoryTree[$key - 1]->getCategory()->getPosition(), $categorie->getCategory()->getPosition());
            }
            foreach ($categorie->getChildren() as $key2 => $children) {
                if ($key2 > 0) {
                    static::assertGreaterThanOrEqual($categorie->getChildren()[$key2 - 1]->getCategory()->getPosition(), $children->getCategory()->getPosition());
                }
            }
        }
    }

    public function testGetCategoryTreeWithWrongKey(): void
    {
        static::expectException(\UnexpectedValueException::class);
        $this->buildCatalogService()->getCategoryTree('wrong', SortDirection::DESC);
    }

    public function testGetCategoryTreeWithWrongValue(): void
    {
        static::expectException(\UnexpectedValueException::class);
        $this->buildCatalogService()->getCategoryTree(CategorySortCriteria::ID, 'wrong');
    }

    public function testGetCategories(): void
    {
        $categories = $this->buildCatalogService()->getCategories();

        $this->assertGreaterThanOrEqual(5, \count($categories));
        $this->assertContainsOnly(Category::class, $categories);

        $categories = $this->buildCatalogService()->getCategories([3,4,5]);

        $this->assertCount(3, $categories);
        $this->assertContainsOnly(Category::class, $categories);
    }

    public function testGetCategoriesWithAgeLimit(): void
    {
        $categories = $this->buildCatalogService()->getCategories([2,3]);

        static::assertCount(2, $categories);
        static::assertContainsOnly(Category::class, $categories);

        static::assertSame(18, $categories[0]->getAgeLimit());
        static::assertSame(0, $categories[1]->getAgeLimit());
    }

    public function testGetAttributes(): void
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

    public function testGetAttribute(): void
    {
        $attribute = $this->buildCatalogService()->getAttribute(1);

        $this->assertSame(1, $attribute->getId());
        $this->assertSame(0, $attribute->getPosition());
        $this->assertSame('Couleur', $attribute->getName());
        $this->assertTrue(AttributeType::CHECKBOX_MULTIPLE()->equals($attribute->getType()));
        $this->assertNull($attribute->getParentId());
    }

    public function testGetNonExistingAttribute(): void
    {
        $this->expectException(NotFound::class);
        $this->buildCatalogService()->getAttribute(404);
    }

    public function testGetAttributeVariant(): void
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
        $this->assertSame('keywords', $variant->getSeoKeywords());
        $this->assertSame(4, $variant->getPosition());
    }

    public function testGetNonExistingAttributeVariant(): void
    {
        $this->expectException(NotFound::class);
        $this->buildCatalogService()->getAttributeVariant(404);
    }

    public function testGetAttributeVariants(): void
    {
        $variants = $this->buildCatalogService()->getAttributeVariants(1);

        $expectedVariants = [
            new AttributeVariant(
                [
                    'id' => 1,
                    'attributeId' => 1,
                    'name' => 'Bleu',
                    'slug' => 'bleu-slug',
                    'image' => ['id' => 4],
                    'description' => '',
                    'position' => 0,
                    'seoData' => [
                        'title' => 'Bleu title',
                        'description' => 'Bleu description',
                        'keywords' => 'Bleu keywords',
                    ],
                ]
            ),
            new AttributeVariant(
                [
                    'id' => 2,
                    'attributeId' => 1,
                    'name' => 'Blanc',
                    'slug' => 'blanc',
                    'image' => null,
                    'description' => '',
                    'position' => 1,
                    'seoData' => [
                        'title' => 'Blanc title',
                        'description' => 'Blanc description',
                        'keywords' => 'Blanc keywords',
                    ],
                ]
            ),
            new AttributeVariant(
                [
                    'id' => 3,
                    'attributeId' => 1,
                    'name' => 'Rouge',
                    'slug' => 'rouge',
                    'image' => ['id' => 5],
                    'description' => 'Le rouge est un champ chromatique regroupant les couleurs vives situées sur le cercle chromatique entre l\'orange et les pourpres.',
                    'position' => 2,
                    'seoData' => [
                        'title' => 'Rouge title',
                        'description' => 'Rouge description',
                        'keywords' => 'Rouge keywords',
                    ],
                ]
            ),
        ];

        $this->assertEquals($expectedVariants, $variants);
    }

    public function testGetNonExistingAttributeVariants(): void
    {
        $this->expectException(NotFound::class);
        $this->buildCatalogService()->getAttributeVariants(404);
    }

    public function testGetProductWithOptions(): void
    {
        $catalogService = $this->buildCatalogService();
        $product = $catalogService->getProductById('3');

        $expectedDeclinations = [
            new Declination(
                [
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
                    'shippings' => [
                        new Shipping(
                            [
                                "shipping_id" => 1,
                                "name" => "TNT Express",
                                "image" => null,
                                "firstRate" => 0,
                                "nextRate" => 0,
                                "deliveryTime" => "24h",
                            ]
                        ),
                        new Shipping(
                            [
                                "shipping_id" => 38,
                                "name" => "Lettre prioritaire",
                                "image" => null,
                                "firstRate" => 0,
                                "nextRate" => 0,
                                "deliveryTime" => "",
                            ]
                        ),
                        new Shipping(
                            [
                                "shipping_id" => 39,
                                "name" => "Colissmo",
                                "image" => null,
                                "firstRate" => 0,
                                "nextRate" => 0,
                                "deliveryTime" => "",
                            ]
                        ),
                    ],
                ]
            ),
            new Declination(
                [
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
                    'shippings' => [
                        new Shipping(
                            [
                                "shipping_id" => 1,
                                "name" => "TNT Express",
                                "image" => null,
                                "firstRate" => 0,
                                "nextRate" => 0,
                                "deliveryTime" => "24h",
                            ]
                        ),
                        new Shipping(
                            [
                                "shipping_id" => 38,
                                "name" => "Lettre prioritaire",
                                "image" => null,
                                "firstRate" => 0,
                                "nextRate" => 0,
                                "deliveryTime" => "",
                            ]
                        ),
                        new Shipping(
                            [
                                "shipping_id" => 39,
                                "name" => "Colissmo",
                                "image" => null,
                                "firstRate" => 0,
                                "nextRate" => 0,
                                "deliveryTime" => "",
                            ]
                        ),
                    ],
                ]
            ),
            new Declination(
                [
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
                    'shippings' => [
                        new Shipping(
                            [
                                "shipping_id" => 1,
                                "name" => "TNT Express",
                                "image" => null,
                                "firstRate" => 0,
                                "nextRate" => 0,
                                "deliveryTime" => "24h",
                            ]
                        ),
                        new Shipping(
                            [
                                "shipping_id" => 38,
                                "name" => "Lettre prioritaire",
                                "image" => null,
                                "firstRate" => 0,
                                "nextRate" => 0,
                                "deliveryTime" => "",
                            ]
                        ),
                        new Shipping(
                            [
                                "shipping_id" => 39,
                                "name" => "Colissmo",
                                "image" => null,
                                "firstRate" => 0,
                                "nextRate" => 0,
                                "deliveryTime" => "",
                            ]
                        ),
                    ],
                ]
            ),
            new Declination(
                [
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
                    'shippings' => [
                        new Shipping(
                            [
                                "shipping_id" => 1,
                                "name" => "TNT Express",
                                "image" => null,
                                "firstRate" => 0,
                                "nextRate" => 0,
                                "deliveryTime" => "24h",
                            ]
                        ),
                        new Shipping(
                            [
                                "shipping_id" => 38,
                                "name" => "Lettre prioritaire",
                                "image" => null,
                                "firstRate" => 0,
                                "nextRate" => 0,
                                "deliveryTime" => "",
                            ]
                        ),
                        new Shipping(
                            [
                                "shipping_id" => 39,
                                "name" => "Colissmo",
                                "image" => null,
                                "firstRate" => 0,
                                "nextRate" => 0,
                                "deliveryTime" => "",
                            ]
                        ),
                    ],
                ]
            ),
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
        $this->assertTrue(\in_array($product->getDeclinationFromOptions([7]), $expectedDeclinations));
        $this->assertEquals($expectedDeclinations[0], $product->getDeclinationFromOptions([7]));
    }

    public function testGetProductWithMultipleOptions(): void
    {
        $catalogService = $this->buildCatalogService();
        $product = $catalogService->getProductById('2');

        $expectedDeclinations = [
            new Declination(
                [
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
                    'shippings' => [
                        new Shipping(
                            [
                                "shipping_id" => 1,
                                "name" => "TNT Express",
                                "image" => null,
                                "firstRate" => 0,
                                "nextRate" => 0,
                                "deliveryTime" => "24h",
                            ]
                        ),
                        new Shipping(
                            [
                                "shipping_id" => 38,
                                "name" => "Lettre prioritaire",
                                "image" => null,
                                "firstRate" => 0,
                                "nextRate" => 0,
                                "deliveryTime" => "",
                            ]
                        ),
                        new Shipping(
                            [
                                "shipping_id" => 39,
                                "name" => "Colissmo",
                                "image" => null,
                                "firstRate" => 0,
                                "nextRate" => 0,
                                "deliveryTime" => "",
                            ]
                        ),
                    ],
                ]
            ),
            new Declination(
                [
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
                    'shippings' => [
                        new Shipping(
                            [
                                "shipping_id" => 1,
                                "name" => "TNT Express",
                                "image" => null,
                                "firstRate" => 0,
                                "nextRate" => 0,
                                "deliveryTime" => "24h",
                            ]
                        ),
                        new Shipping(
                            [
                                "shipping_id" => 38,
                                "name" => "Lettre prioritaire",
                                "image" => null,
                                "firstRate" => 0,
                                "nextRate" => 0,
                                "deliveryTime" => "",
                            ]
                        ),
                        new Shipping(
                            [
                                "shipping_id" => 39,
                                "name" => "Colissmo",
                                "image" => null,
                                "firstRate" => 0,
                                "nextRate" => 0,
                                "deliveryTime" => "",
                            ]
                        ),
                    ],
                ]
            ),
            new Declination(
                [
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
                    'shippings' => [
                        new Shipping(
                            [
                                "shipping_id" => 1,
                                "name" => "TNT Express",
                                "image" => null,
                                "firstRate" => 0,
                                "nextRate" => 0,
                                "deliveryTime" => "24h",
                            ]
                        ),
                        new Shipping(
                            [
                                "shipping_id" => 38,
                                "name" => "Lettre prioritaire",
                                "image" => null,
                                "firstRate" => 0,
                                "nextRate" => 0,
                                "deliveryTime" => "",
                            ]
                        ),
                        new Shipping(
                            [
                                "shipping_id" => 39,
                                "name" => "Colissmo",
                                "image" => null,
                                "firstRate" => 0,
                                "nextRate" => 0,
                                "deliveryTime" => "",
                            ]
                        ),
                    ],
                ]
            ),
            new Declination(
                [
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
                    'shippings' => [
                        new Shipping(
                            [
                                "shipping_id" => 1,
                                "name" => "TNT Express",
                                "image" => null,
                                "firstRate" => 0,
                                "nextRate" => 0,
                                "deliveryTime" => "24h",
                            ]
                        ),
                        new Shipping(
                            [
                                "shipping_id" => 38,
                                "name" => "Lettre prioritaire",
                                "image" => null,
                                "firstRate" => 0,
                                "nextRate" => 0,
                                "deliveryTime" => "",
                            ]
                        ),
                        new Shipping(
                            [
                                "shipping_id" => 39,
                                "name" => "Colissmo",
                                "image" => null,
                                "firstRate" => 0,
                                "nextRate" => 0,
                                "deliveryTime" => "",
                            ]
                        ),
                    ],
                ]
            ),
            new Declination(
                [
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
                    'shippings' => [
                        new Shipping(
                            [
                                "shipping_id" => 1,
                                "name" => "TNT Express",
                                "image" => null,
                                "firstRate" => 0,
                                "nextRate" => 0,
                                "deliveryTime" => "24h",
                            ]
                        ),
                        new Shipping(
                            [
                                "shipping_id" => 38,
                                "name" => "Lettre prioritaire",
                                "image" => null,
                                "firstRate" => 0,
                                "nextRate" => 0,
                                "deliveryTime" => "",
                            ]
                        ),
                        new Shipping(
                            [
                                "shipping_id" => 39,
                                "name" => "Colissmo",
                                "image" => null,
                                "firstRate" => 0,
                                "nextRate" => 0,
                                "deliveryTime" => "",
                            ]
                        ),
                    ],
                ]
            ),
            new Declination(
                [
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
                    'shippings' => [
                        new Shipping(
                            [
                                "shipping_id" => 1,
                                "name" => "TNT Express",
                                "image" => null,
                                "firstRate" => 0,
                                "nextRate" => 0,
                                "deliveryTime" => "24h",
                            ]
                        ),
                        new Shipping(
                            [
                                "shipping_id" => 38,
                                "name" => "Lettre prioritaire",
                                "image" => null,
                                "firstRate" => 0,
                                "nextRate" => 0,
                                "deliveryTime" => "",
                            ]
                        ),
                        new Shipping(
                            [
                                "shipping_id" => 39,
                                "name" => "Colissmo",
                                "image" => null,
                                "firstRate" => 0,
                                "nextRate" => 0,
                                "deliveryTime" => "",
                            ]
                        ),
                    ],
                ]
            ),
            new Declination(
                [
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
                    'shippings' => [
                        new Shipping(
                            [
                                "shipping_id" => 1,
                                "name" => "TNT Express",
                                "image" => null,
                                "firstRate" => 0,
                                "nextRate" => 0,
                                "deliveryTime" => "24h",
                            ]
                        ),
                        new Shipping(
                            [
                                "shipping_id" => 38,
                                "name" => "Lettre prioritaire",
                                "image" => null,
                                "firstRate" => 0,
                                "nextRate" => 0,
                                "deliveryTime" => "",
                            ]
                        ),
                        new Shipping(
                            [
                                "shipping_id" => 39,
                                "name" => "Colissmo",
                                "image" => null,
                                "firstRate" => 0,
                                "nextRate" => 0,
                                "deliveryTime" => "",
                            ]
                        ),
                    ],
                ]
            ),
            new Declination(
                [
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
                    'shippings' => [
                        new Shipping(
                            [
                                "shipping_id" => 1,
                                "name" => "TNT Express",
                                "image" => null,
                                "firstRate" => 0,
                                "nextRate" => 0,
                                "deliveryTime" => "24h",
                            ]
                        ),
                        new Shipping(
                            [
                                "shipping_id" => 38,
                                "name" => "Lettre prioritaire",
                                "image" => null,
                                "firstRate" => 0,
                                "nextRate" => 0,
                                "deliveryTime" => "",
                            ]
                        ),
                        new Shipping(
                            [
                                "shipping_id" => 39,
                                "name" => "Colissmo",
                                "image" => null,
                                "firstRate" => 0,
                                "nextRate" => 0,
                                "deliveryTime" => "",
                            ]
                        ),
                    ],
                ]
            ),
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

    public function testGetProductWithGeolocation(): void
    {
        $location = $this->buildCatalogService()->getProductById('7')->getGeolocation();
        $this->assertInstanceOf(ProductLocation::class, $location);
        $this->assertSame(45.75845, $location->getLatitude());
        $this->assertSame(4.799044, $location->getLongitude());
        $this->assertSame('Lyon 5e', $location->getLabel());
        $this->assertSame('69005', $location->getZipcode());
    }

    public function testGetProductWithAttachments(): void
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

    public function testGetProductWithVideo(): void
    {
        $video = $this->buildCatalogService()->getProductById('3')->getVideo();
        $this->assertInstanceOf(ProductVideo::class, $video);
        $this->assertSame('//s3-eu-west-1.amazonaws.com/wizachatest/videos/414375b2-61cb-4260-b82b-4a2636cb5673/480-00001.png', (string) $video->getThumbnailUrl());
        $this->assertSame('//s3-eu-west-1.amazonaws.com/wizachatest/videos/414375b2-61cb-4260-b82b-4a2636cb5673/480.mp4', (string) $video->getVideoUrl());
    }

    public function testReportingProduct(): void
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

    public function testReportingNonExistingProduct(): void
    {
        $report = (new ProductReport())
            ->setProductId('404')
            ->setReporterEmail('user@wizaplace.com')
            ->setReporterName('User')
            ->setMessage('Should get a 404');

        $this->expectException(ProductNotFound::class);
        $this->buildCatalogService()->reportProduct($report);
    }

    public function testReportingProductWithInvalidEmail(): void
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

    public function testReportingProductWithMissingField(): void
    {
        $report = (new ProductReport())
            ->setProductId('1')
            ->setReporterEmail('user@wizaplace.com')
            ->setReporterName('User');

        $this->expectException(SomeParametersAreInvalid::class);
        $this->buildCatalogService()->reportProduct($report);
    }

    public function testGetBrandFromWrongType(): void
    {
        $this->expectException(\TypeError::class);
        $this->buildCatalogService()->getBrand(
            new Declination(
                [
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
                ]
            )
        );
    }

    public function testGetCompanyByIdWidhMoreInfo(): void
    {
        $catalogService = $this->buildCatalogService();

        $company = $catalogService->getCompanyById(3);

        static::assertSame(3, $company->getId());
        static::assertSame('The World Company Inc.', $company->getName());
        static::assertSame('the-world-company-inc', $company->getSlug());
        static::assertSame('The World Company Inc.', $company->getDescription());
        static::assertSame('40 rue Laure Diebold', $company->getAddress());
        static::assertSame('01 02 03 04 05', $company->getPhoneNumber());
        static::assertTrue($company->isProfessional());
        static::assertEquals(45.778847, $company->getLocation()->getLatitude());
        static::assertEquals(4.800039, $company->getLocation()->getLongitude());
        static::assertSame(5, $company->getAverageRating());
        static::assertSame('Lorem Ipsum', $company->getTerms());
        static::assertSame('FR', $company->getFullAddress()->getCountry());
        static::assertSame('40 rue Laure Diebold', $company->getFullAddress()->getAddress());
        static::assertSame('Paris', $company->getFullAddress()->getCity());
        static::assertSame('75001', $company->getFullAddress()->getZipCode());
        static::assertNull($company->getCapital());
        static::assertNull($company->getLegalStatus());
        static::assertSame("40483304800023", $company->getSiretNumber());
        static::assertSame("FR83404833049", $company->getVatNumber());
        static::assertSame([], $company->getExtra());
    }

    public function testNumericFacet(): void
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

    public function testGetProductWithSeoData(): void
    {
        $catalogService = $this->buildCatalogService();

        $product = $catalogService->getProductById('4');

        $this->assertSame('4', $product->getId());
        $this->assertSame('Corsair Gaming VOID Pro RGB Dolby 7.1 Sans fil - Edition Carbon', $product->getName());

        $this->assertSame('Micro-Casque Corsair Gaming', $product->getSeoTitle());
        $this->assertSame('Achat Micro-casque Corsair Gaming VOID Pro RGB Dolby 7.1 Wireless - Edition Carbon sur notre MarketPlace. Casque-micro 7.1 sans fil pour gamer.', $product->getSeoDescription());
    }

    public function testGetProductWithoutSeoDescription(): void
    {
        $catalogService = $this->buildCatalogService();

        $product = $catalogService->getProductById('5');

        $this->assertSame('5', $product->getId());
        $this->assertSame('Logitech G430 Casque Gaming pour PC Gaming, PS4, Xbox One with 7.1 Dolby Surround', $product->getName());

        $this->assertSame('Micro-Casque Logitech Gaming', $product->getSeoTitle());
        $this->assertSame('', $product->getSeoDescription());
    }

    public function testGetProductWithoutSeoTitle(): void
    {
        $catalogService = $this->buildCatalogService();

        $product = $catalogService->getProductById('6');

        $this->assertSame('6', $product->getId());
        $this->assertSame('Casque Gaming Razer ManO\'War Sans fil 7.1 Surround (PC/PS4)', $product->getName());

        $this->assertSame('', $product->getSeoTitle());
        $this->assertSame('Achat Micro-casque Razer ManO\'War 7.1 Wireless - Edition Carbon sur notre MarketPlace. Casque-micro 7.1 sans fil pour gamer.', $product->getSeoDescription());
    }

    public function testGetCompanyMetadata(): void
    {
        $catalogService = $this->buildCatalogService();

        $company = $catalogService->getCompanyById(3);

        static::assertEquals(3, $company->getId());
        static::assertSame('Title', $company->getMetadata()->getTitle());
        static::assertSame('Description', $company->getMetadata()->getDescription());
        static::assertSame('Keyword, Keyword2', $company->getMetadata()->getKeywords());
    }

    public function testGetAttachment(): void
    {
        $response = $this->buildCatalogService()->getProductAttachment("91d90a26-9ec1-4092-a40c-4acd9c3fefa9");
        static::assertSame('image/jpeg', $response->getHeaderLine('Content-Type'));
        static::assertStringStartsWith('attachment; filename="', $response->getHeaderLine('Content-Disposition'));
    }

    public function testGetProductWithPriceTier(): void
    {
        $catalogService = $this->buildCatalogService();
        $product = $catalogService->getProductById('1');

        static::assertEquals(1, \count($product->getDeclination(new DeclinationId('1_0'))->getPriceTiers()));
        static::assertEquals(1.4, $product->getDeclination(new DeclinationId('1_0'))->getPriceTiers()[0]->getTaxes());
        static::assertEquals(67.9, $product->getDeclination(new DeclinationId('1_0'))->getPriceTiers()[0]->getPriceIncludeTax());
        static::assertEquals(0, $product->getDeclination(new DeclinationId('1_0'))->getPriceTiers()[0]->getLowerLimit());
    }

    public function testGetBrandFromMVP(): void
    {
        $mvp = $this->buildCatalogService()->getProductsByMvpId('0adaf6bc-d362-34be-b72f-42d5aa3b4a4e');
        $brand = $this->buildCatalogService()->getBrandFromProduct($mvp[0]);

        static::assertSame(20, $brand->getId());
        static::assertSame(9, $brand->getAttributeId());
        static::assertSame('Puma', $brand->getName());
        static::assertSame('puma', $brand->getSlug());
        static::assertSame(13, $brand->getImage()->getId());
    }

    public function testGetProductByWithMaxPriceAdjustment()
    {
        $catalogService = $this->buildCatalogService();
        $product1 = $catalogService->getProductById((string) 1);
        static::assertSame((string) 1, $product1->getId());
        static::assertSame(24, $product1->getMaxPriceAdjustment());

        $product2 = $catalogService->getProductById((string) 4);
        static::assertSame((string) 4, $product2->getId());
        static::assertSame(35, $product2->getMaxPriceAdjustment());

        $product3 = $catalogService->getProductById((string) 3);
        static::assertSame((string) 3, $product3->getId());
        static::assertNull($product3->getMaxPriceAdjustment());
    }

    public function testGetProductByFiltersWithMaxPriceAdjustment(): void
    {
        $catalogService = $this->buildCatalogService();

        $filters = (new ProductFilter())->setIds([1, 4, 3]);

        $products = $catalogService->getProductsByFilters($filters);
        static::assertCount(3, $products);

        $expected = ['1', '3', '4'];

        foreach ($products as $key => $product) {
            static::assertSame($expected[$key], $product->getId());
        }

        static::assertSame('1', $products[0]->getId());
        static::assertSame(24, $products[0]->getMaxPriceAdjustment());

        static::assertSame('4', $products[2]->getId());
        static::assertSame(35, $products[2]->getMaxPriceAdjustment());

        static::assertSame('3', $products[1]->getId());
        static::assertNull($products[1]->getMaxPriceAdjustment());
    }

    public function testGetProductsWithSubscription(): void
    {
        $products = $this->buildCatalogService()->getProductsByCode("product_with_sub");

        static::assertCount(1, $products);
        static::assertTrue($products[0]->isSubscription());
        static::assertTrue($products[0]->isRenewable());
    }

    public function testGetProductWithSubscription(): void
    {
        $product = $this->buildCatalogService()->getProductById((string) 37);

        static::assertTrue($product->isSubscription());
        static::assertTrue($product->isRenewable());
    }

    public function testSearchProductWithSubscription(): void
    {
        $products = $this->buildCatalogService()->search('Product with sub')->getProducts();

        static::assertCount(1, $products);
        static::assertTrue($products[0]->isSubscription());
        static::assertTrue($products[0]->isRenewable());
    }

    public function testSearchProductsByFiltersWithSubscription(): void
    {
        $products = $this
            ->buildCatalogService()
            ->search(
                '',
                [
                    ProductFilter::IS_SUBSCRIPTION => true,
                ]
            )
            ->getProducts();

        static::assertCount(1, $products);
        static::assertTrue($products[0]->isSubscription());
        static::assertTrue($products[0]->isRenewable());
    }

    public function testGetProductDeclinationWithSubscription(): void
    {
        $declination = $this->buildCatalogService()->getDeclinationById('36_0');

        static::assertTrue($declination->isSubscription());
        static::assertTrue($declination->isRenewable());
    }

    public function testGetProductWithSystemOption(): void
    {
        $product = $this->buildCatalogService()->getProductById('1');

        static::assertCount(9, $product->getDeclinations());
        foreach ($product->getDeclinations() as $declination) {
            static::assertCount(2, $declination->getOptions());
            static::assertSame(SystemOption::PAYMENT_FREQUENCY()->getValue(), $declination->getOptions()[0]->getCode());
            static::assertSame(SystemOption::COMMITMENT_PERIOD()->getValue(), $declination->getOptions()[1]->getCode());
        }

        static::assertCount(2, $product->getOptions());
        static::assertSame(SystemOption::PAYMENT_FREQUENCY()->getValue(), $product->getOptions()[0]->getCode());
        static::assertSame(SystemOption::COMMITMENT_PERIOD()->getValue(), $product->getOptions()[1]->getCode());
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

    public function testAverageRaitingOnProductHasComments(): void
    {
        $catalogService = $this->buildCatalogService();

        $product = $catalogService->getProductById('1');

        static::assertSame(2.5, $product->getAverageRating());
    }

    public function testNullAverageRaiting(): void
    {
        $catalogService = $this->buildCatalogService();

        $product = $catalogService->getProductById('2');

        static::assertSame(0.0, $product->getAverageRating());
    }

    public function testGetAttributesByFiltersUsingOneId(): void
    {
        $filters = (new AttributeFilter())->setIds([1]);

        $catalogService = $this->buildCatalogService();
        $attributes = $catalogService->getAttributes($filters);

        static::assertCount(1, $attributes);

        $attribute = $attributes[0];
        static::assertInstanceOf(Attribute::class, $attribute);
        static::assertSame(1, $attribute->getId());
        static::assertSame(0, $attribute->getPosition());
        static::assertSame('Couleur', $attribute->getName());
        static::assertTrue(AttributeType::CHECKBOX_MULTIPLE()->equals($attribute->getType()));
        static::assertNull($attribute->getParentId());
    }

    public function testGetAttributesByFiltersUsingManyIds(): void
    {
        $ids = [1, 2];
        $filters = (new AttributeFilter())->setIds($ids);

        $catalogService = $this->buildCatalogService();
        $attributes = $catalogService->getAttributes($filters);

        static::assertCount(2, $attributes);

        foreach ($attributes as $key => $attribute) {
            static::assertInstanceOf(Attribute::class, $attribute);
            static::assertSame($ids[$key], $attribute->getId());
        }
    }

    public function testGetAttributesByFiltersUsingIOneCode(): void
    {
        $filters = (new AttributeFilter())->setCodes(['code commentaire']);

        $catalogService = $this->buildCatalogService();
        $attributes = $catalogService->getAttributes($filters);

        static::assertCount(1, $attributes);

        $attribute = $attributes[0];
        static::assertInstanceOf(Attribute::class, $attribute);
        static::assertSame(3, $attribute->getId());
        static::assertSame(0, $attribute->getPosition());
        static::assertSame('Commentaire', $attribute->getName());
        static::assertTrue(AttributeType::FREE_TEXT()->equals($attribute->getType()));
        static::assertNull($attribute->getParentId());
    }

    public function testGetAttributesByFilters(): void
    {
        $filters = (new AttributeFilter())
            ->setIds([1, 2, 3,])
            ->setCodes(['code commentaire',]);

        $catalogService = $this->buildCatalogService();
        $attributes = $catalogService->getAttributes($filters);

        static::assertCount(1, $attributes);
        static::assertSame(3, $attributes[0]->getId());
        static::assertSame('code commentaire', $attributes[0]->getCode());
    }

    public function testGetAttributesWithoutFilter(): void
    {
        $catalogService = $this->buildCatalogService();
        $attributes = $catalogService->getAttributes();

        static::assertCount(13, $attributes);
    }

    public function testSearchReturnCode(): void
    {
        $catalogService = $this->buildCatalogService();
        $result = $catalogService->search('Z11 Plus Boîtier PC en Acier ATX');
        $products = $result->getProducts();
        static::assertCount(1, $products);
        $product = $products[0];
        static::assertSame($product->getCode(), '978020137962');
    }

    public function testGetProductWithOptionsVariantsDisplayingPositionField(): void
    {
        $product = $this->buildCatalogService()->getProductById('2');

        static::assertSame(0, $product->getOptions()[0]->getVariants()[0]->getPosition());
    }

    public function testGetAndSearchProductDisplayingAttributesCode(): void
    {
        $catalogService = $this->buildCatalogService();

        //get product from catalog
        $catalogProduct = $catalogService->getProductById('1');
        static::assertSame('code color', $catalogProduct->getAttributes()[0]->getCode());

        //get product from search
        $result = $catalogService->search('Z11 Plus Boîtier PC en Acier ATX');
        $searchProduct = $result->getProducts()[0];
        static::assertSame('1', $searchProduct->getId());
        static::assertSame('code color', $searchProduct->getAttributes()[0]->getCode());
    }

    public function testSearchReturnsProductExpectedImages(): void
    {
        $catalogService = $this->buildCatalogService();
        $result = $catalogService->search('Z11 Plus Boîtier PC en Acier ATX');
        $products = $result->getProducts();
        static::assertCount(1, $products);
        $product = $products[0];
        $this->assertEquals(
            [
                new Image(['id' => 13]),
                new Image(['id' => 14]),
                new Image(['id' => 15]),
            ],
            $product->getImages()
        );
        $this->assertEquals(
            [
                new DeclinationImages([
                    'declinationId' => '1_2_5',
                    'images' => [new Image(['id' => 16])]
                ]),
                new DeclinationImages([
                    'declinationId' => '1_2_6',
                    'images' => [new Image(['id' => 17])]
                ]),
            ],
            $product->getDeclinationsImages()
        );
    }

    public function testGetCompaniesWithFilterExtra(): void
    {
        $catalogService = $this->buildCatalogService();
        $allCompanies = $catalogService->getCompanies();
        $company = $catalogService->getCompanyById(\reset($allCompanies)->getId());

        static::assertSame(
            [
                'type' => 'matchmaker',
                'highlight' => 'true',
            ],
            $company->getExtra()
        );

        static::assertInternalType('array', $allCompanies);
        static::assertCount(4, $allCompanies);

        $companyFilter = new CompanyFilter();
        $companyFilter->setExtra(
            [
                'type' => 'matchmaker',
                'highlight' => 'true',
            ]
        );

        $companies = $catalogService->getCompanies($companyFilter);

        static::assertInternalType('array', $companies);
        static::assertCount(1, $companies);

        $companyFound = $catalogService->getCompanyById(\reset($companies)->getId());

        static::assertSame($companyFound->getExtra(), $company->getExtra());
    }

    public function testGetCompaniesWithFilterExtraUsingOnlyKey(): void
    {
        $catalogService = $this->buildCatalogService();
        $allCompanies = $catalogService->getCompanies();

        static::assertInternalType('array', $allCompanies);
        static::assertCount(4, $allCompanies);

        $numberOfCompanyHasExtraKey = 0;
        foreach ($allCompanies as $company) {
            $company = $catalogService->getCompanyById($company->getId());

            if (\array_key_exists('highlight', $company->getExtra()) === true) {
                $numberOfCompanyHasExtraKey++;
            }
        }

        $companyFilter = new CompanyFilter();
        $companyFilter->setExtra(
            [
                'highlight' => ''
            ]
        );

        $companies = $catalogService->getCompanies($companyFilter);

        static::assertInternalType('array', $companies);
        static::assertCount($numberOfCompanyHasExtraKey, $companies);
    }

    public function testGetCompaniesWithFilterExtraUsingTheSameKey(): void
    {
        $catalogService = $this->buildCatalogService();
        $allCompanies = $catalogService->getCompanies();

        static::assertInternalType('array', $allCompanies);
        static::assertCount(4, $allCompanies);

        $numberOfCompanyHasExtraKeyAndValue = 0;
        foreach ($allCompanies as $company) {
            $company = $catalogService->getCompanyById($company->getId());

            if (\array_key_exists('type', $company->getExtra()) === true
                && \in_array($company->getExtra()['type'], ['matchmaker', 'recruiter']) === true
            ) {
                $numberOfCompanyHasExtraKeyAndValue++;
            }
        }

        $companyFilter = new CompanyFilter();
        $companyFilter->setExtra(
            [
                'type' => [
                    'matchmaker',
                    'recruiter'
                ]
            ]
        );

        $companies = $catalogService->getCompanies($companyFilter);

        static::assertInternalType('array', $companies);
        static::assertCount($numberOfCompanyHasExtraKeyAndValue, $companies);
    }
}
