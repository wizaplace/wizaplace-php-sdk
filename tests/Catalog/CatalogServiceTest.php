<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\Tests\Catalog;

use Wizaplace\Catalog\AttributeType;
use Wizaplace\Catalog\CatalogService;
use Wizaplace\Catalog\combinations;
use Wizaplace\Catalog\Declination;
use Wizaplace\Catalog\Option;
use Wizaplace\Exception\NotFound;
use Wizaplace\Tests\ApiTestCase;

/**
 * @see CatalogService
 */
class CatalogServiceTest extends ApiTestCase
{
    public function testGetProductById()
    {
        $catalogService = $this->buildCatalogService();

        $product = $catalogService->getProductById(1);

        $this->assertEquals(1, $product->getId());
        $this->assertEquals('test-product-slug', $product->getSlug());
        $this->assertEquals('optio corporis similique voluptatum', $product->getName());
        $this->assertEquals('', $product->getDescription());
        $this->assertCount(1, $product->getDeclinations());
        $this->assertCount(0, $product->getAttributes());
        $this->assertCount(1, $product->getCategoryPath());
        $this->assertEquals(['informatique'], $product->getCategorySlugs());
        $this->assertEquals('6086375420678', $product->getCode());
        $this->assertGreaterThan(1500000000, $product->getCreationDate()->getTimestamp());
        $this->assertEquals('http://wizaplace.loc/informatique/test-product-slug.html', $product->getUrl());
        $this->assertEquals(20, $product->getMinPrice());
        $this->assertCount(1, $product->getShippings());
        $this->assertEquals('', $product->getShortDescription());
        $this->assertEquals('', $product->getSupplierReference());
        $this->assertTrue($product->isTransactional());
        $this->assertEquals(0, $product->getGreenTax());
        $this->assertEquals(1.23, $product->getWeight());
        $this->assertEquals(3, $product->getAverageRating());

        $companies = $product->getCompanies();
        $this->assertCount(1, $companies);
        $this->assertEquals(5, $companies[0]->getId());
        $this->assertEquals('Test company', $companies[0]->getName());
    }

    public function testGetProductWithComplexAttributes()
    {
        $catalogService = $this->buildCatalogService();

        $product = $catalogService->getProductById(7);

        $this->assertEquals(7, $product->getId());
        $this->assertEquals('test-product-complex-attributes', $product->getSlug());
        $this->assertEquals(1.23, $product->getWeight());
        $this->assertEquals(0, $product->getGreenTax());
        $this->assertNull($product->getAverageRating());

        $attributes = $product->getAttributes();

        // Check attributes group
        $this->assertCount(9, $attributes);
        $attributesGroup = $attributes[2];
        $this->assertEquals('Groupe attributs', $attributesGroup->getName());
        $this->assertNull($attributesGroup->getValue());
        $this->assertCount(3, $attributesGroup->getChildren());

        // Bulk checks on all attributes
        foreach ($attributes as $attribute) {
            $value = $attribute->getValue();
            if (!is_null($value) && !is_string($value) && !is_array($value)) {
                throw new \TypeError('\Wizaplace\Catalog\ProductAttribute::getValue must return null, a string, or an array. Got '.var_export($value, true));
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
        $this->assertEquals(1, $product->getId());
        $this->assertEquals('test-product-slug', $product->getSlug());
        $this->assertTrue($product->isAvailable());
        $this->assertGreaterThan(1400000000, $product->getCreatedAt()->getTimestamp());
        $this->assertEquals(3, $product->getAverageRating());
        $this->assertEquals('optio corporis similique voluptatum', $product->getName());
        $this->assertEquals(['N'], $product->getCondition());
        $this->assertEquals(1, $product->getDeclinationCount());
        $this->assertEquals('', $product->getSubtitle());
        $this->assertGreaterThanOrEqual($product->getCreatedAt()->getTimestamp(), $product->getUpdatedAt()->getTimestamp());
        $this->assertEquals('', $product->getAffiliateLink());
        $this->assertEquals('/informatique/test-product-slug.html', $product->getUrl());
        $this->assertEquals(['informatique'], $product->getCategorySlugs());
        $this->assertCount(1, $product->getCategoryPath());
        $this->assertNull($product->getMainImage());
        $this->assertEquals(20, $product->getMinimumPrice());
        $this->assertNull($product->getCrossedOutPrice());
        $this->assertCount(0, $product->getAttributes());
        $companies = $product->getCompanies();
        $this->assertCount(1, $companies);
        $this->assertEquals('Test company', $companies[0]->getName());
        $this->assertEquals('test-company', $companies[0]->getSlug());
        $this->assertEquals(5, $companies[0]->getId());
        $this->assertNull($companies[0]->getImage());


        $pagination = $result->getPagination();
        $this->assertEquals(1, $pagination->getNbPages());
        $this->assertEquals(1, $pagination->getNbResults());
        $this->assertEquals(1, $pagination->getPage());
        $this->assertEquals(12, $pagination->getResultsPerPage());

        $facets = $result->getFacets();
        $this->assertCount(9, $facets);
        $this->assertEquals('categories', $facets[0]->getName());
        $this->assertEquals('Catégorie', $facets[0]->getLabel());
        $this->assertEquals([
            3 => [
            'label' => 'Informatique',
            'count' => '1',
            'position' => 0,
            ],
        ], $facets[0]->getValues());
        $this->assertFalse($facets[0]->isIsNumeric());
    }

    public function testGetCompanyById()
    {
        $catalogService = $this->buildCatalogService();

        $company = $catalogService->getCompanyById(5);

        $this->assertEquals(5, $company->getId());
        $this->assertEquals('Test company', $company->getName());
        $this->assertEquals('test-company', $company->getSlug());
        $this->assertEquals('Test company', $company->getDescription());
        $this->assertEquals('40 rue Laure Diebold', $company->getAddress());
        $this->assertEquals('01 02 03 04 05', $company->getPhoneNumber());
        $this->assertFalse($company->isProfessional());
        $this->assertNull($company->getLocation());
        $this->assertEquals(2, $company->getAverageRating());

        $company = $catalogService->getCompanyById(6);

        $this->assertEquals(6, $company->getId());
        $this->assertEquals('Test company', $company->getName());
        $this->assertEquals('test-company-2', $company->getSlug());
        $this->assertEquals('Test company', $company->getDescription());
        $this->assertEquals('40 rue Laure Diebold', $company->getAddress());
        $this->assertEquals('01 02 03 04 05', $company->getPhoneNumber());
        $this->assertTrue($company->isProfessional());
        $this->assertNull($company->getLocation());
        $this->assertNull($company->getAverageRating());
    }

    public function testGetC2cCompanyById()
    {
        $catalogService = $this->buildCatalogService();

        $company = $catalogService->getCompanyById(4);

        $this->assertEquals(4, $company->getId());
        $this->assertEquals('C2C company', $company->getName());
        $this->assertEquals('c2c-company', $company->getSlug());
        $this->assertEquals('C2C company', $company->getDescription());
        $this->assertEquals('', $company->getAddress());
        $this->assertEquals('', $company->getPhoneNumber());
        $this->assertFalse($company->isProfessional());
        $this->assertNull($company->getLocation());
        $this->assertNull($company->getAverageRating());
        $this->assertNull($company->getImage());
    }

    public function testGetCategory()
    {
        $category = $this->buildCatalogService()->getCategory(2);
        $this->assertEquals(2, $category->getId());
        $this->assertNull($category->getParentId());
        $this->assertEquals('Catégorie principale', $category->getName());
        $this->assertEquals('categorie-principale', $category->getSlug());
        $this->assertEquals('', $category->getDescription());
        $this->assertEquals(10, $category->getPosition());
        $this->assertEquals(0, $category->getProductCount());
        $this->assertNull($category->getImage());
    }

    public function testGetCategoryTree()
    {
        $categoryTree = $this->buildCatalogService()->getCategoryTree();
        $this->assertCount(3, $categoryTree);

        $firstCategory = $categoryTree[0]->getCategory();

        $this->assertEquals(2, $firstCategory->getId());
        $this->assertEquals('Catégorie principale', $firstCategory->getName());
        $this->assertEquals('categorie-principale', $firstCategory->getSlug());
        $this->assertEquals('', $firstCategory->getDescription());
        $this->assertEquals(10, $firstCategory->getPosition());
        $this->assertEquals(0, $firstCategory->getProductCount());

        $childrenTrees = $categoryTree[1]->getChildren();
        $this->assertCount(1, $childrenTrees);
        $childCategory = $childrenTrees[0]->getCategory();
        $this->assertEquals(4, $childCategory->getId());
        $this->assertEquals('Écrans', $childCategory->getName());
        $this->assertEquals('ecrans', $childCategory->getSlug());
        $this->assertEquals('', $childCategory->getDescription());
        $this->assertEquals(0, $childCategory->getPosition());
        $this->assertEquals(2, $childCategory->getProductCount());
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

        $this->assertEquals(1, $attribute->getId());
        $this->assertEquals(0, $attribute->getPosition());
        $this->assertEquals('Couleur', $attribute->getName());
        $this->assertEquals(AttributeType::CHECKBOX_MULTIPLE(), $attribute->getType());
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

        $this->assertEquals(3, $variant->getId());
        $this->assertEquals(1, $variant->getAttributeId());
        $this->assertEquals('Rouge', $variant->getName());
        $this->assertEquals('rouge', $variant->getSlug());
        $this->assertNull($variant->getImage());
    }

    public function testGetNonExistingAttributeVariant()
    {
        $this->expectException(NotFound::class);
        $this->buildCatalogService()->getAttributeVariant(404);
    }

    public function testGetProductWithOptions()
    {
        $catalogService = $this->buildCatalogService();
        $product = $catalogService->getProductById(2);

        $ids = ['2_7_1', '2_7_2', '2_7_3', '2_7_4'];
        $codes = ['size_13', 'size_15', 'size_17', 'size_21'];

        $expectedDeclinations = [
            new Declination([
                'id' => '2_7_1',
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
                'combinations' => [
                    [
                        'optionId' => 7,
                        'optionName' => 'size',
                        'variantId' => 1,
                        'variantName' => '13',
                    ],
                ],
            ]),
            new Declination([
                'id' => '2_7_2',
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
                'combinations' => [
                    [
                        'optionId' => 7,
                        'optionName' => 'size',
                        'variantId' => 2,
                        'variantName' => '15',
                    ],
                ],
            ]),
            new Declination([
                'id' => '2_7_3',
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
                'combinations' => [
                    [
                        'optionId' => 7,
                        'optionName' => 'size',
                        'variantId' => 3,
                        'variantName' => '17',
                    ],
                ],
            ]),
            new Declination([
                'id' => '2_7_4',
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
                'combinations' => [
                    [
                        'optionId' => 7,
                        'optionName' => 'size',
                        'variantId' => 4,
                        'variantName' => '21',
                    ],
                ],
            ]),
        ];

        $expectedOption = [
            'id' => 7,
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

        $this->assertEquals(2, $product->getId());
        $this->assertEquals($expectedDeclinations, $product->getDeclinations());
        $this->assertEquals($expectedOptions, $product->getOptions());
        $this->assertEquals($expectedDeclinations[0], $product->getDeclinationFromOptions([1]));
    }

    public function testGetProductWithMultipleOptions()
    {
        $catalogService = $this->buildCatalogService();
        $product = $catalogService->getProductById(3);

        $expectedDeclinations = [
            new Declination([
                'id' => '3_9_5',
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
                'combinations' => [
                    [
                        'optionId' => 9,
                        'optionName' => 'color',
                        'variantId' => 5,
                        'variantName' => 'white',
                    ],
                ],
            ]),
            new Declination([
                'id' => '3_9_5_10_9',
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
                'combinations' => [
                    [
                        'optionId' => 9,
                        'optionName' => 'color',
                        'variantId' => 5,
                        'variantName' => 'white',
                    ],
                    [
                        'optionId' => 10,
                        'optionName' => 'connectivity',
                        'variantId' => 9,
                        'variantName' => 'wireless',
                    ],
                ],
            ]),
            new Declination([
                'id' => '3_9_5_10_10',
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
                'combinations' => [
                    [
                        'optionId' => 9,
                        'optionName' => 'color',
                        'variantId' => 5,
                        'variantName' => 'white',
                    ],
                    [
                        'optionId' => 10,
                        'optionName' => 'connectivity',
                        'variantId' => 10,
                        'variantName' => 'wired',
                    ],
                ],
            ]),
            new Declination([
                'id' => '3_9_6',
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
                'combinations' => [
                    [
                        'optionId' => 9,
                        'optionName' => 'color',
                        'variantId' => 6,
                        'variantName' => 'black',
                    ],
                ],
            ]),
            new Declination([
                'id' => '3_9_6_10_9',
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
                'combinations' => [
                    [
                        'optionId' => 9,
                        'optionName' => 'color',
                        'variantId' => 6,
                        'variantName' => 'black',
                    ],
                    [
                        'optionId' => 10,
                        'optionName' => 'connectivity',
                        'variantId' => 9,
                        'variantName' => 'wireless',
                    ],
                ],
            ]),
            new Declination([
                'id' => '3_9_6_10_10',
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
                'combinations' => [
                    [
                        'optionId' => 9,
                        'optionName' => 'color',
                        'variantId' => 6,
                        'variantName' => 'black',
                    ],
                    [
                        'optionId' => 10,
                        'optionName' => 'connectivity',
                        'variantId' => 10,
                        'variantName' => 'wired',
                    ],
                ],
            ]),
            new Declination([
                'id' => '3_9_7',
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
                'combinations' => [
                    [
                        'optionId' => 9,
                        'optionName' => 'color',
                        'variantId' => 7,
                        'variantName' => 'blue',
                    ],
                ],
            ]),
            new Declination([
                'id' => '3_9_7_10_9',
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
                'combinations' => [
                    [
                        'optionId' => 9,
                        'optionName' => 'color',
                        'variantId' => 7,
                        'variantName' => 'blue',
                    ],
                    [
                        'optionId' => 10,
                        'optionName' => 'connectivity',
                        'variantId' => 9,
                        'variantName' => 'wireless',
                    ],
                ],
            ]),
            new Declination([
                'id' => '3_9_7_10_10',
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
                'combinations' => [
                    [
                        'optionId' => 9,
                        'optionName' => 'color',
                        'variantId' => 7,
                        'variantName' => 'blue',
                    ],
                    [
                        'optionId' => 10,
                        'optionName' => 'connectivity',
                        'variantId' => 10,
                        'variantName' => 'wired',
                    ],
                ],
            ]),
            new Declination([
                'id' => '3_9_8',
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
                'combinations' => [
                    [
                        'optionId' => 9,
                        'optionName' => 'color',
                        'variantId' => 8,
                        'variantName' => 'red',
                    ],
                ],
            ]),
            new Declination([
                'id' => '3_9_8_10_9',
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
                'combinations' => [
                    [
                        'optionId' => 9,
                        'optionName' => 'color',
                        'variantId' => 8,
                        'variantName' => 'red',
                    ],
                    [
                        'optionId' => 10,
                        'optionName' => 'connectivity',
                        'variantId' => 9,
                        'variantName' => 'wireless',
                    ],
                ],
            ]),
            new Declination([
                'id' => '3_9_8_10_10',
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
                'combinations' => [
                    [
                        'optionId' => 9,
                        'optionName' => 'color',
                        'variantId' => 8,
                        'variantName' => 'red',
                    ],
                    [
                        'optionId' => 10,
                        'optionName' => 'connectivity',
                        'variantId' => 10,
                        'variantName' => 'wired',
                    ],
                ],
            ]),
            new Declination([
                'id' => '3_10_9',
                'code' => 'connectivity_wireless',
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
                'combinations' => [
                    [
                        'optionId' => 10,
                        'optionName' => 'connectivity',
                        'variantId' => 9,
                        'variantName' => 'wireless',
                    ],
                ],
            ]),
            new Declination([
                'id' => '3_10_10',
                'code' => 'connectivity_wired',
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
                'combinations' => [
                    [
                        'optionId' => 10,
                        'optionName' => 'connectivity',
                        'variantId' => 10,
                        'variantName' => 'wired',
                    ],
                ],
            ]),
        ];

        $expectedOption1 = [
            'id' => 9,
            'name' => 'color',
            'variants' => [
                [
                    'id' => 5,
                    'name' => 'white',
                ],
                [
                    'id' => 6,
                    'name' => 'black',
                ],
                [
                    'id' => 7,
                    'name' => 'blue',
                ],
                [
                    'id' => 8,
                    'name' => 'red',
                ],
            ],
        ];

        $expectedOption2 = [
            'id' => 10,
            'name' => 'connectivity',
            'variants' => [
                [
                    'id' => 9,
                    'name' => 'wireless',
                ],
                [
                    'id' => 10,
                    'name' => 'wired',
                ],
            ],
        ];

        $expectedOptions = [
            new Option($expectedOption1),
            new Option($expectedOption2),
        ];

        $this->assertEquals(3, $product->getId());
        $this->assertEquals($expectedDeclinations, $product->getDeclinations());
        $this->assertEquals($expectedOptions, $product->getOptions());
        $this->assertTrue(in_array($product->getDeclinationFromOptions([5]), $expectedDeclinations));
        $this->assertTrue(in_array($product->getDeclinationFromOptions([5, 9]), $expectedDeclinations));
        $this->assertTrue(in_array($product->getDeclinationFromOptions([5, 10]), $expectedDeclinations));
        $this->assertTrue(in_array($product->getDeclinationFromOptions([6]), $expectedDeclinations));
        $this->assertTrue(in_array($product->getDeclinationFromOptions([6, 9]), $expectedDeclinations));
        $this->assertTrue(in_array($product->getDeclinationFromOptions([6, 10]), $expectedDeclinations));
        $this->assertTrue(in_array($product->getDeclinationFromOptions([7]), $expectedDeclinations));
        $this->assertTrue(in_array($product->getDeclinationFromOptions([7, 9]), $expectedDeclinations));
        $this->assertTrue(in_array($product->getDeclinationFromOptions([7, 10]), $expectedDeclinations));
        $this->assertTrue(in_array($product->getDeclinationFromOptions([8]), $expectedDeclinations));
        $this->assertTrue(in_array($product->getDeclinationFromOptions([8, 9]), $expectedDeclinations));
        $this->assertTrue(in_array($product->getDeclinationFromOptions([8, 10]), $expectedDeclinations));
        $this->assertTrue(in_array($product->getDeclinationFromOptions([8, 10]), $expectedDeclinations));
        $this->assertTrue(in_array($product->getDeclinationFromOptions([9]), $expectedDeclinations));
    }

    private function buildCatalogService(): CatalogService
    {
        return new CatalogService($this->buildApiClient());
    }
}
