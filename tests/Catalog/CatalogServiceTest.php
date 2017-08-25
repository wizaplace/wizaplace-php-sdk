<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\Tests\Catalog;

use GuzzleHttp\Psr7\Response;
use Wizaplace\Catalog\AttributeType;
use Wizaplace\Catalog\CatalogService;
use Wizaplace\Catalog\Declination;
use Wizaplace\Catalog\Option;
use Wizaplace\Catalog\ProductReport;
use Wizaplace\Exception\NotFound;
use Wizaplace\Exception\SomeParametersAreInvalid;
use Wizaplace\Tests\ApiTestCase;

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
        $this->assertSame(3.0, $product->getAverageRating());

        $companies = $product->getCompanies();
        $this->assertCount(1, $companies);
        $this->assertSame(5, $companies[0]->getId());
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
        $this->assertSame('1', $product->getId());
        $this->assertSame('test-product-slug', $product->getSlug());
        $this->assertTrue($product->isAvailable());
        $this->assertGreaterThan(1400000000, $product->getCreatedAt()->getTimestamp());
        $this->assertSame(3, $product->getAverageRating());
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
        $this->assertSame(5, $companies[0]->getId());
        $this->assertNull($companies[0]->getImage());


        $pagination = $result->getPagination();
        $this->assertSame(1, $pagination->getNbPages());
        $this->assertSame(1, $pagination->getNbResults());
        $this->assertSame(1, $pagination->getPage());
        $this->assertSame(12, $pagination->getResultsPerPage());

        $facets = $result->getFacets();
        $this->assertCount(9, $facets);
        $this->assertSame('categories', $facets[0]->getName());
        $this->assertSame('Catégorie', $facets[0]->getLabel());
        $this->assertSame([
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

        $this->assertSame(5, $company->getId());
        $this->assertSame('Test company', $company->getName());
        $this->assertSame('test-company', $company->getSlug());
        $this->assertSame('Test company', $company->getDescription());
        $this->assertSame('40 rue Laure Diebold', $company->getAddress());
        $this->assertSame('01 02 03 04 05', $company->getPhoneNumber());
        $this->assertFalse($company->isProfessional());
        $this->assertNull($company->getLocation());
        $this->assertSame(2, $company->getAverageRating());
        $this->assertSame('Lorem Ipsum', $company->getTerms());

        $company = $catalogService->getCompanyById(6);

        $this->assertSame(6, $company->getId());
        $this->assertSame('Test company', $company->getName());
        $this->assertSame('test-company-2', $company->getSlug());
        $this->assertSame('Test company', $company->getDescription());
        $this->assertSame('40 rue Laure Diebold', $company->getAddress());
        $this->assertSame('01 02 03 04 05', $company->getPhoneNumber());
        $this->assertTrue($company->isProfessional());
        $this->assertNull($company->getLocation());
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
        $this->assertCount(3, $categoryTree);

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

    public function testGetProductWithOptions()
    {
        $catalogService = $this->buildCatalogService();
        $product = $catalogService->getProductById(2);

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
                'options' => [
                    [
                        'id' => 7,
                        'name' => 'size',
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
                'options' => [
                    [
                        'id' => 7,
                        'name' => 'size',
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
                'options' => [
                    [
                        'id' => 7,
                        'name' => 'size',
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
                'options' => [
                    [
                        'id' => 7,
                        'name' => 'size',
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
                'options' => [
                    [
                        'id' => 9,
                        'name' => 'color',
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
                'options' => [
                    [
                        'id' => 9,
                        'name' => 'color',
                        'variantId' => 5,
                        'variantName' => 'white',
                    ],
                    [
                        'id' => 10,
                        'name' => 'connectivity',
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
                'options' => [
                    [
                        'id' => 9,
                        'name' => 'color',
                        'variantId' => 5,
                        'variantName' => 'white',
                    ],
                    [
                        'id' => 10,
                        'name' => 'connectivity',
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
                'options' => [
                    [
                        'id' => 9,
                        'name' => 'color',
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
                'options' => [
                    [
                        'id' => 9,
                        'name' => 'color',
                        'variantId' => 6,
                        'variantName' => 'black',
                    ],
                    [
                        'id' => 10,
                        'name' => 'connectivity',
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
                'options' => [
                    [
                        'id' => 9,
                        'name' => 'color',
                        'variantId' => 6,
                        'variantName' => 'black',
                    ],
                    [
                        'id' => 10,
                        'name' => 'connectivity',
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
                'options' => [
                    [
                        'id' => 9,
                        'name' => 'color',
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
                'options' => [
                    [
                        'id' => 9,
                        'name' => 'color',
                        'variantId' => 7,
                        'variantName' => 'blue',
                    ],
                    [
                        'id' => 10,
                        'name' => 'connectivity',
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
                'options' => [
                    [
                        'id' => 9,
                        'name' => 'color',
                        'variantId' => 7,
                        'variantName' => 'blue',
                    ],
                    [
                        'id' => 10,
                        'name' => 'connectivity',
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
                'options' => [
                    [
                        'id' => 9,
                        'name' => 'color',
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
                'options' => [
                    [
                        'id' => 9,
                        'name' => 'color',
                        'variantId' => 8,
                        'variantName' => 'red',
                    ],
                    [
                        'id' => 10,
                        'name' => 'connectivity',
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
                'options' => [
                    [
                        'id' => 9,
                        'name' => 'color',
                        'variantId' => 8,
                        'variantName' => 'red',
                    ],
                    [
                        'id' => 10,
                        'name' => 'connectivity',
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
                'options' => [
                    [
                        'id' => 10,
                        'name' => 'connectivity',
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
                'options' => [
                    [
                        'id' => 10,
                        'name' => 'connectivity',
                        'variantId' => 10,
                        'variantName' => 'wired',
                    ],
                ],
            ]),
        ];

        $this->assertSame('3', $product->getId());
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

    public function testReportingProduct()
    {
        $report = new ProductReport();
        $report->setProductId('1');
        $report->setReporterEmail('user@wizaplace.com');
        $report->setReporterName('Mr. User');
        $report->setMessage('I am shocked!');

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
        $report = new ProductReport();
        $report->setProductId('404');
        $report->setReporterEmail('user@wizaplace.com');
        $report->setReporterName('User');
        $report->setMessage('Should get a 404');

        $this->expectException(NotFound::class);
        $this->buildCatalogService()->reportProduct($report);
    }

    public function testReportingProductWithInvalidEmail()
    {
        $report = new ProductReport();
        $report->setProductId('1');
        $report->setReporterEmail('user@@wizaplace.com');
        $report->setReporterName('User');
        $report->setMessage('Should get a 400');

        $this->expectException(SomeParametersAreInvalid::class);
        $this->expectExceptionCode(400);
        $this->buildCatalogService()->reportProduct($report);
    }

    public function testReportingProductWithMissingField()
    {
        $report = new ProductReport();
        $report->setProductId('1');
        $report->setReporterEmail('user@wizaplace.com');
        $report->setReporterName('User');

        $this->expectException(SomeParametersAreInvalid::class);
        $this->buildCatalogService()->reportProduct($report);
    }

    private function buildCatalogService(): CatalogService
    {
        return new CatalogService($this->buildApiClient());
    }
}
