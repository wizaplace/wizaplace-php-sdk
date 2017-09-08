<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\SDK\Tests\Catalog;

use GuzzleHttp\Psr7\Response;
use Wizaplace\SDK\Catalog\AttributeType;
use Wizaplace\SDK\Catalog\AttributeVariant;
use Wizaplace\SDK\Catalog\CatalogService;
use Wizaplace\SDK\Catalog\Declination;
use Wizaplace\SDK\Catalog\Option;
use Wizaplace\SDK\Catalog\ProductLocation;
use Wizaplace\SDK\Catalog\ProductReport;
use Wizaplace\SDK\Exception\NotFound;
use Wizaplace\SDK\Exception\SomeParametersAreInvalid;
use Wizaplace\SDK\Tests\ApiTestCase;

/**
 * @see CatalogService
 */
final class CatalogServiceTest extends ApiTestCase
{
    public function testGetProductById()
    {
        $catalogService = $this->buildCatalogService();

        $product = $catalogService->getProductById(1);

        $this->assertSame('1', $product->getId());
        $this->assertSame('test-product-slug', $product->getSlug());
        $this->assertSame('optio corporis similique voluptatum', $product->getName());
        $this->assertSame('', $product->getDescription());
        $this->assertCount(1, $product->getDeclinations());
        $this->assertCount(0, $product->getAttributes());
        $this->assertCount(1, $product->getCategoryPath());
        $this->assertSame(['informatique'], $product->getCategorySlugs());
        $this->assertSame('6086375420678', $product->getCode());
        $this->assertGreaterThan(1500000000, $product->getCreationDate()->getTimestamp());
        $this->assertSame('http://wizaplace.loc/informatique/test-product-slug.html', $product->getUrl());
        $this->assertSame(20.0, $product->getMinPrice());
        $this->assertCount(1, $product->getShippings());
        $this->assertSame('', $product->getShortDescription());
        $this->assertSame('', $product->getSupplierReference());
        $this->assertTrue($product->isTransactional());
        $this->assertSame(0.0, $product->getGreenTax());
        $this->assertSame(1.23, $product->getWeight());
        $this->assertNull($product->getAverageRating());
        $this->assertNull($product->getGeolocation());

        $companies = $product->getCompanies();
        $this->assertCount(1, $companies);
        $this->assertSame(7, $companies[0]->getId());
        $this->assertSame('Test company', $companies[0]->getName());
    }

    public function testGetProductWithComplexAttributes()
    {
        $catalogService = $this->buildCatalogService();

        $product = $catalogService->getProductById(7);

        $this->assertSame('7', $product->getId());
        $this->assertSame('test-product-complex-attributes', $product->getSlug());
        $this->assertSame(1.23, $product->getWeight());
        $this->assertSame(0.0, $product->getGreenTax());
        $this->assertNull($product->getAverageRating());

        $attributes = $product->getAttributes();

        // Check attributes group
        $this->assertCount(9, $attributes);
        $attributesGroup = $attributes[2];
        $this->assertSame('Groupe attributs', $attributesGroup->getName());
        $this->assertNull($attributesGroup->getValue());
        $this->assertCount(3, $attributesGroup->getChildren());

        // Bulk checks on all attributes
        foreach ($attributes as $attribute) {
            $value = $attribute->getValue();
            if (!is_null($value) && !is_string($value) && !is_array($value)) {
                throw new \TypeError('\Wizaplace\SDK\Catalog\ProductAttribute::getValue must return null, a string, or an array. Got '.var_export($value, true));
            }
            $this->assertNotEmpty($attribute->getName());
            $attribute->getChildren();
            $attribute->getImageUrls();
        }

        // @TODO: more assertions
    }

    public function testGetNonExistingProductById()
    {
        $catalogService = $this->buildCatalogService();

        $this->expectException(NotFound::class);
        $catalogService->getProductById(404);
    }

    public function testSearchOneProductByName()
    {
        $catalogService = $this->buildCatalogService();

        $result = $catalogService->search('optio corporis similique voluptatum');

        $products = $result->getProducts();
        $this->assertCount(1, $products);

        $product = $products[0];
        $this->assertSame('1', $product->getId());
        $this->assertSame('test-product-slug', $product->getSlug());
        $this->assertTrue($product->isAvailable());
        $this->assertGreaterThan(1400000000, $product->getCreatedAt()->getTimestamp());
        $this->assertNull($product->getAverageRating());
        $this->assertSame('optio corporis similique voluptatum', $product->getName());
        $this->assertSame(['N'], $product->getCondition());
        $this->assertSame(1, $product->getDeclinationCount());
        $this->assertSame('', $product->getSubtitle());
        $this->assertGreaterThanOrEqual($product->getCreatedAt()->getTimestamp(), $product->getUpdatedAt()->getTimestamp());
        $this->assertNull($product->getAffiliateLink());
        $this->assertSame('/informatique/test-product-slug.html', $product->getUrl());
        $this->assertSame(['informatique'], $product->getCategorySlugs());
        $this->assertCount(1, $product->getCategoryPath());
        $this->assertNull($product->getMainImage());
        $this->assertSame(20.0, $product->getMinimumPrice());
        $this->assertNull($product->getCrossedOutPrice());
        $this->assertCount(0, $product->getAttributes());
        $companies = $product->getCompanies();
        $this->assertCount(1, $companies);
        $this->assertSame('Test company', $companies[0]->getName());
        $this->assertSame('test-company', $companies[0]->getSlug());
        $this->assertSame(7, $companies[0]->getId());
        $this->assertNull($companies[0]->getImage());


        $pagination = $result->getPagination();
        $this->assertSame(1, $pagination->getNbPages());
        $this->assertSame(1, $pagination->getNbResults());
        $this->assertSame(1, $pagination->getPage());
        $this->assertSame(12, $pagination->getResultsPerPage());

        $facets = $result->getFacets();
        $this->assertCount(8, $facets);
        $this->assertSame('categories', $facets[0]->getName());
        $this->assertSame('Catégorie', $facets[0]->getLabel());
        $this->assertSame([
            3 => [
            'label' => 'Informatique',
            'count' => '1',
            'position' => '0',
            ],
        ], $facets[0]->getValues());
        $this->assertFalse($facets[0]->isIsNumeric());
    }

    public function testGetCompanyById()
    {
        $catalogService = $this->buildCatalogService();

        $company = $catalogService->getCompanyById(5);

        $this->assertSame(5, $company->getId());
        $this->assertSame('Company with reviews', $company->getName());
        $this->assertSame('company-with-reviews', $company->getSlug());
        $this->assertSame('Company with reviews', $company->getDescription());
        $this->assertSame('40 rue Laure Diebold', $company->getAddress());
        $this->assertSame('01 02 03 04 05', $company->getPhoneNumber());
        $this->assertFalse($company->isProfessional());
        $this->assertNull($company->getLocation());
        $this->assertSame(2, $company->getAverageRating());
        $this->assertSame('Lorem Ipsum', $company->getTerms());

        $company = $catalogService->getCompanyById(6);

        $this->assertSame(6, $company->getId());
        $this->assertSame('Company with geoloc', $company->getName());
        $this->assertSame('company-with-geoloc', $company->getSlug());
        $this->assertSame('Company with geoloc', $company->getDescription());
        $this->assertSame('40 rue Laure Diebold', $company->getAddress());
        $this->assertSame('01 02 03 04 05', $company->getPhoneNumber());
        $this->assertTrue($company->isProfessional());
        $this->assertEquals(45.778847, $company->getLocation()->getLatitude());
        $this->assertEquals(4.800039, $company->getLocation()->getLongitude());
        $this->assertNull($company->getAverageRating());
        $this->assertSame('Lorem Ipsum', $company->getTerms());
    }

    public function testGetC2cCompanyById()
    {
        $catalogService = $this->buildCatalogService();

        $company = $catalogService->getCompanyById(4);

        $this->assertSame(4, $company->getId());
        $this->assertSame('C2C company', $company->getName());
        $this->assertSame('c2c-company', $company->getSlug());
        $this->assertSame('C2C company', $company->getDescription());
        $this->assertSame('', $company->getAddress());
        $this->assertSame('', $company->getPhoneNumber());
        $this->assertFalse($company->isProfessional());
        $this->assertNull($company->getLocation());
        $this->assertNull($company->getAverageRating());
        $this->assertNull($company->getImage());
        $this->assertSame('Lorem Ipsum', $company->getTerms());
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
        $this->assertNull($category->getImage());
    }

    public function testGetCategoryTree()
    {
        $categoryTree = $this->buildCatalogService()->getCategoryTree();
        $this->assertCount(4, $categoryTree);

        $firstCategory = $categoryTree[0]->getCategory();

        $this->assertSame(2, $firstCategory->getId());
        $this->assertSame('Catégorie principale', $firstCategory->getName());
        $this->assertSame('categorie-principale', $firstCategory->getSlug());
        $this->assertSame('', $firstCategory->getDescription());
        $this->assertSame(10, $firstCategory->getPosition());
        $this->assertSame(0, $firstCategory->getProductCount());

        $childrenTrees = $categoryTree[1]->getChildren();
        $this->assertCount(1, $childrenTrees);
        $childCategory = $childrenTrees[0]->getCategory();
        $this->assertSame(4, $childCategory->getId());
        $this->assertSame('Écrans', $childCategory->getName());
        $this->assertSame('ecrans', $childCategory->getSlug());
        $this->assertSame('', $childCategory->getDescription());
        $this->assertSame(0, $childCategory->getPosition());
        $this->assertSame(2, $childCategory->getProductCount());
    }

    public function testGetAttributes()
    {
        $attributes = $this->buildCatalogService()->getAttributes();

        $this->assertCount(9, $attributes);
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
        $this->assertSame('rouge', $variant->getSlug());
        $this->assertNull($variant->getImage());
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
            new AttributeVariant(1, 1, 'Bleu', '', null),
            new AttributeVariant(2, 1, 'Blanc', 'blanc', null),
            new AttributeVariant(3, 1, 'Rouge', 'rouge', null),
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
        $product = $catalogService->getProductById(2);

        $expectedDeclinations = [
            new Declination([
                'id' => '2_9_1',
                'code' => 'size_13',
                'supplierReference' => '',
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
                'affiliateLink' => null,
                'images' => [],
                'options' => [
                    [
                        'id' => 9,
                        'name' => 'size',
                        'variantId' => 1,
                        'variantName' => '13',
                    ],
                ],
            ]),
            new Declination([
                'id' => '2_9_2',
                'code' => 'size_15',
                'supplierReference' => '',
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
                'affiliateLink' => null,
                'images' => [],
                'options' => [
                    [
                        'id' => 9,
                        'name' => 'size',
                        'variantId' => 2,
                        'variantName' => '15',
                    ],
                ],
            ]),
            new Declination([
                'id' => '2_9_3',
                'code' => 'size_17',
                'supplierReference' => '',
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
                'affiliateLink' => null,
                'images' => [],
                'options' => [
                    [
                        'id' => 9,
                        'name' => 'size',
                        'variantId' => 3,
                        'variantName' => '17',
                    ],
                ],
            ]),
            new Declination([
                'id' => '2_9_4',
                'code' => 'size_21',
                'supplierReference' => '',
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
                'affiliateLink' => null,
                'images' => [],
                'options' => [
                    [
                        'id' => 9,
                        'name' => 'size',
                        'variantId' => 4,
                        'variantName' => '21',
                    ],
                ],
            ]),
        ];

        $expectedOption = [
            'id' => 9,
            'name' => 'size',
            'variants' => [
                [
                    'id' => 1,
                    'name' => '13',
                ],
                [
                    'id' => 2,
                    'name' => '15',
                ],
                [
                    'id' => 3,
                    'name' => '17',
                ],
                [
                    'id' => 4,
                    'name' => '21',
                ],
            ],
        ];

        $expectedOptions = [
            new Option($expectedOption),
        ];

        $this->assertSame('2', $product->getId());
        $this->assertEquals($expectedDeclinations, $product->getDeclinations());
        $this->assertEquals($expectedOptions, $product->getOptions());
        $this->assertTrue(in_array($product->getDeclinationFromOptions([1]), $expectedDeclinations));
        $this->assertEquals($expectedDeclinations[0], $product->getDeclinationFromOptions([1]));
    }

    public function testGetProductWithMultipleOptions()
    {
        $catalogService = $this->buildCatalogService();
        $product = $catalogService->getProductById(3);

        $expectedDeclinations = [
            new Declination([
                'id' => '3_11_5',
                'code' => 'color_white',
                'supplierReference' => '',
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
                'affiliateLink' => null,
                'images' => [],
                'options' => [
                    [
                        'id' => 11,
                        'name' => 'color',
                        'variantId' => 5,
                        'variantName' => 'white',
                    ],
                ],
            ]),
            new Declination([
                'id' => '3_11_5_12_9',
                'code' => '1438900263352',
                'supplierReference' => '',
                'price' => 0,
                'originalPrice' => 0,
                'crossedOutPrice' => null,
                'prices' => [
                    'priceWithTaxes' => 0,
                    'priceWithoutVat' => 0,
                    'vat' => 0,
                ],
                'greenTax' => 0,
                'amount' => 0,
                'affiliateLink' => null,
                'images' => [],
                'options' => [
                    [
                        'id' => 11,
                        'name' => 'color',
                        'variantId' => 5,
                        'variantName' => 'white',
                    ],
                    [
                        'id' => 12,
                        'name' => 'connectivity',
                        'variantId' => 9,
                        'variantName' => 'wireless',
                    ],
                ],
            ]),
            new Declination([
                'id' => '3_11_5_12_10',
                'code' => '1438900263352',
                'supplierReference' => '',
                'price' => 0,
                'originalPrice' => 0,
                'crossedOutPrice' => null,
                'prices' => [
                    'priceWithTaxes' => 0,
                    'priceWithoutVat' => 0,
                    'vat' => 0,
                ],
                'greenTax' => 0,
                'amount' => 0,
                'affiliateLink' => null,
                'images' => [],
                'options' => [
                    [
                        'id' => 11,
                        'name' => 'color',
                        'variantId' => 5,
                        'variantName' => 'white',
                    ],
                    [
                        'id' => 12,
                        'name' => 'connectivity',
                        'variantId' => 10,
                        'variantName' => 'wired',
                    ],
                ],
            ]),
            new Declination([
                'id' => '3_11_6',
                'code' => 'color_black',
                'supplierReference' => '',
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
                'affiliateLink' => null,
                'images' => [],
                'options' => [
                    [
                        'id' => 11,
                        'name' => 'color',
                        'variantId' => 6,
                        'variantName' => 'black',
                    ],
                ],
            ]),
            new Declination([
                'id' => '3_11_6_12_9',
                'code' => '1438900263352',
                'supplierReference' => '',
                'price' => 0,
                'originalPrice' => 0,
                'crossedOutPrice' => null,
                'prices' => [
                    'priceWithTaxes' => 0,
                    'priceWithoutVat' => 0,
                    'vat' => 0,
                ],
                'greenTax' => 0,
                'amount' => 0,
                'affiliateLink' => null,
                'images' => [],
                'options' => [
                    [
                        'id' => 11,
                        'name' => 'color',
                        'variantId' => 6,
                        'variantName' => 'black',
                    ],
                    [
                        'id' => 12,
                        'name' => 'connectivity',
                        'variantId' => 9,
                        'variantName' => 'wireless',
                    ],
                ],
            ]),
            new Declination([
                'id' => '3_11_6_12_10',
                'code' => '1438900263352',
                'supplierReference' => '',
                'price' => 0,
                'originalPrice' => 0,
                'crossedOutPrice' => null,
                'prices' => [
                    'priceWithTaxes' => 0,
                    'priceWithoutVat' => 0,
                    'vat' => 0,
                ],
                'greenTax' => 0,
                'amount' => 0,
                'affiliateLink' => null,
                'images' => [],
                'options' => [
                    [
                        'id' => 11,
                        'name' => 'color',
                        'variantId' => 6,
                        'variantName' => 'black',
                    ],
                    [
                        'id' => 12,
                        'name' => 'connectivity',
                        'variantId' => 10,
                        'variantName' => 'wired',
                    ],
                ],
            ]),
            new Declination([
                'id' => '3_11_7',
                'code' => 'color_blue',
                'supplierReference' => '',
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
                'affiliateLink' => null,
                'images' => [],
                'options' => [
                    [
                        'id' => 11,
                        'name' => 'color',
                        'variantId' => 7,
                        'variantName' => 'blue',
                    ],
                ],
            ]),
            new Declination([
                'id' => '3_11_7_12_9',
                'code' => '1438900263352',
                'supplierReference' => '',
                'price' => 0,
                'originalPrice' => 0,
                'crossedOutPrice' => null,
                'prices' => [
                    'priceWithTaxes' => 0,
                    'priceWithoutVat' => 0,
                    'vat' => 0,
                ],
                'greenTax' => 0,
                'amount' => 0,
                'affiliateLink' => null,
                'images' => [],
                'options' => [
                    [
                        'id' => 11,
                        'name' => 'color',
                        'variantId' => 7,
                        'variantName' => 'blue',
                    ],
                    [
                        'id' => 12,
                        'name' => 'connectivity',
                        'variantId' => 9,
                        'variantName' => 'wireless',
                    ],
                ],
            ]),
            new Declination([
                'id' => '3_11_7_12_10',
                'code' => '1438900263352',
                'supplierReference' => '',
                'price' => 0,
                'originalPrice' => 0,
                'crossedOutPrice' => null,
                'prices' => [
                    'priceWithTaxes' => 0,
                    'priceWithoutVat' => 0,
                    'vat' => 0,
                ],
                'greenTax' => 0,
                'amount' => 0,
                'affiliateLink' => null,
                'images' => [],
                'options' => [
                    [
                        'id' => 11,
                        'name' => 'color',
                        'variantId' => 7,
                        'variantName' => 'blue',
                    ],
                    [
                        'id' => 12,
                        'name' => 'connectivity',
                        'variantId' => 10,
                        'variantName' => 'wired',
                    ],
                ],
            ]),
            new Declination([
                'id' => '3_11_8',
                'code' => 'color_red',
                'supplierReference' => '',
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
                'affiliateLink' => null,
                'images' => [],
                'options' => [
                    [
                        'id' => 11,
                        'name' => 'color',
                        'variantId' => 8,
                        'variantName' => 'red',
                    ],
                ],
            ]),
            new Declination([
                'id' => '3_11_8_12_9',
                'code' => 'color_red_connectivity_wireless',
                'supplierReference' => '',
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
                'affiliateLink' => null,
                'images' => [],
                'options' => [
                    [
                        'id' => 11,
                        'name' => 'color',
                        'variantId' => 8,
                        'variantName' => 'red',
                    ],
                    [
                        'id' => 12,
                        'name' => 'connectivity',
                        'variantId' => 9,
                        'variantName' => 'wireless',
                    ],
                ],
            ]),
            new Declination([
                'id' => '3_11_8_12_10',
                'code' => 'color_red_connectivity_wired',
                'supplierReference' => '',
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
                'affiliateLink' => null,
                'images' => [],
                'options' => [
                    [
                        'id' => 11,
                        'name' => 'color',
                        'variantId' => 8,
                        'variantName' => 'red',
                    ],
                    [
                        'id' => 12,
                        'name' => 'connectivity',
                        'variantId' => 10,
                        'variantName' => 'wired',
                    ],
                ],
            ]),
        ];

        $this->assertSame('3', $product->getId());

        $this->assertEquals($expectedDeclinations[0], $product->getDeclinationFromOptions([5]));
        $this->assertEquals($expectedDeclinations[1], $product->getDeclinationFromOptions([5, 9]));
        $this->assertEquals($expectedDeclinations[2], $product->getDeclinationFromOptions([5, 10]));
        $this->assertEquals($expectedDeclinations[3], $product->getDeclinationFromOptions([6]));
        $this->assertEquals($expectedDeclinations[4], $product->getDeclinationFromOptions([6, 9]));
        $this->assertEquals($expectedDeclinations[5], $product->getDeclinationFromOptions([6, 10]));
        $this->assertEquals($expectedDeclinations[6], $product->getDeclinationFromOptions([7]));
        $this->assertEquals($expectedDeclinations[7], $product->getDeclinationFromOptions([7, 9]));
        $this->assertEquals($expectedDeclinations[8], $product->getDeclinationFromOptions([7, 10]));
        $this->assertEquals($expectedDeclinations[9], $product->getDeclinationFromOptions([8]));
        $this->assertEquals($expectedDeclinations[10], $product->getDeclinationFromOptions([8, 9]));
        $this->assertEquals($expectedDeclinations[11], $product->getDeclinationFromOptions([8, 10]));
    }

    public function testGetProductWithGeolocation()
    {
        $location = $this->buildCatalogService()->getProductById(8)->getGeolocation();
        $this->assertInstanceOf(ProductLocation::class, $location);
        $this->assertSame(45.778848, $location->getLatitude());
        $this->assertSame(4.800039, $location->getLongitude());
        $this->assertSame('Wizacha', $location->getLabel());
        $this->assertSame('69009', $location->getZipcode());
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

        $this->expectException(NotFound::class);
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

    private function buildCatalogService(): CatalogService
    {
        return new CatalogService($this->buildApiClient());
    }
}
