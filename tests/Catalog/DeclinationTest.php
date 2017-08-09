<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types = 1);


namespace Wizaplace\Tests\Catalog;

use Wizaplace\Catalog\Declination;
use Wizaplace\Tests\ApiTestCase;

class DeclinationTest extends ApiTestCase
{
    public function testDeclinationHasRequestedVariant()
    {
        $declination = new Declination([
            'id' => 1,
            'code' => '0123456789123',
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
            'affiliateLink' => '',
            'images' => [],
            'options' => [
                [
                    'id' => 1,
                    'name' => 'size',
                    'variantId' => 1,
                    'variantName' => 'xs',
                ],
            ],
        ]);

        $this->assertTrue($declination->hasVariants([1]));
    }

    public function testDeclinationWithDifferentVariantThanRequestedReturnFalse()
    {
        $declination = new Declination([
            'id' => 1,
            'code' => '0123456789123',
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
            'affiliateLink' => '',
            'images' => [],
            'options' => [
                [
                    'id' => 1,
                    'name' => 'size',
                    'variantId' => 1,
                    'variantName' => 'xs',
                ],
            ],
        ]);

        $this->assertFalse($declination->hasVariants([2]));
    }

    public function testDeclinationWithRequestedVariantAndAnOtherOneReturnFalse()
    {
        $declination = new Declination([
            'id' => 1,
            'code' => '0123456789123',
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
            'affiliateLink' => '',
            'images' => [],
            'options' => [
                [
                    'id' => 1,
                    'name' => 'size',
                    'variantId' => 1,
                    'variantName' => 'xs',
                ],
                [
                    'id' => 2,
                    'name' => 'color',
                    'variantId' => 2,
                    'variantName' => 'red',
                ],
            ],
        ]);

        $this->assertFalse($declination->hasVariants([2]));
    }
    public function testDeclinationHasRequestedVariants()
    {
        $declination = new Declination([
            'id' => 1,
            'code' => '0123456789123',
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
            'affiliateLink' => '',
            'images' => [],
            'options' => [
                [
                    'id' => 1,
                    'name' => 'size',
                    'variantId' => 1,
                    'variantName' => 'xs',
                ],
                [
                    'id' => 2,
                    'name' => 'color',
                    'variantId' => 2,
                    'variantName' => 'red',
                ],
            ],
        ]);

        $this->assertTrue($declination->hasVariants([1, 2]));
    }

    public function testDeclinationWithDifferentVariantsThanRequestedReturnFalse()
    {
        $declination = new Declination([
            'id' => 1,
            'code' => '0123456789123',
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
            'affiliateLink' => '',
            'images' => [],
            'options' => [
                [
                    'id' => 1,
                    'name' => 'size',
                    'variantId' => 1,
                    'variantName' => 'xs',
                ],
                [
                    'id' => 2,
                    'name' => 'color',
                    'variantId' => 2,
                    'variantName' => 'red',
                ],
            ],
        ]);

        $this->assertFalse($declination->hasVariants([2, 3]));
    }

    public function testDeclinationWithRequestedVariantsAndOtherOnesReturnFalse()
    {
        $declination = new Declination([
            'id' => 1,
            'code' => '0123456789123',
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
            'affiliateLink' => '',
            'images' => [],
            'options' => [
                [
                    'id' => 1,
                    'name' => 'size',
                    'variantId' => 1,
                    'variantName' => 'xs',
                ],
                [
                    'id' => 2,
                    'name' => 'color',
                    'variantId' => 2,
                    'variantName' => 'red',
                ],
                [
                    'id' => 3,
                    'name' => 'sleeves',
                    'variantId' => 3,
                    'variantName' => 'short',
                ],
            ],
        ]);

        $this->assertFalse($declination->hasVariants([1, 2]));
    }
}
