<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\SDK\Tests\Catalog;

use GuzzleHttp\Psr7\Uri;
use PHPUnit\Framework\TestCase;
use Wizaplace\SDK\Catalog\Declination;
use Wizaplace\SDK\Catalog\DeclinationId;
use Wizaplace\SDK\Catalog\Product;

final class ProductTest extends TestCase
{
    public function testGetMultipleOtherOffers()
    {
        $product = new Product(array(
            'id' => 3,
            'code' => '0000001',
            'supplierReference' => 'INFO-ECRAN-001',
            'name' => 'Ecran PC Full HD Noir',
            'url' => 'http://wizaplace.loc/informatique/ecrans/ecran-pc-full-hd-noir.html',
            'shortDescription' => '',
            'description' => '',
            'slug' => 'ecran-pc-full-hd-noir',
            'minPrice' => 15.5,
            'greenTax' => 0,
            'isTransactional' => true,
            'infiniteStock' => false,
            'weight' => 1.23,
            'averageRating' => null,
            'shippings' =>
                array(
                    0 =>
                        array(
                            'name' => 'TNT Express',
                            'image' => null,
                            'firstRate' => 0,
                            'nextRate' => 0,
                            'deliveryTime' => '24h',
                        ),
                    1 =>
                        array(
                            'name' => 'Lettre prioritaire',
                            'image' => null,
                            'firstRate' => 0,
                            'nextRate' => 0,
                            'deliveryTime' => '',
                        ),
                    2 =>
                        array(
                            'name' => 'Colissmo',
                            'image' => null,
                            'firstRate' => 0,
                            'nextRate' => 0,
                            'deliveryTime' => '',
                        ),
                ),
            'companies' =>
                array(
                    0 =>
                        array(
                            'id' => 3,
                            'name' => 'The World Company Inc.',
                            'slug' => 'the-world-company-inc.',
                            'isProfessional' => true,
                            'image' => null,
                            'averageRating' => null,
                        ),
                ),
            'categoryPath' =>
                array(
                    0 =>
                        array(
                            'id' => 3,
                            'name' => 'Informatique',
                            'slug' => 'informatique',
                        ),
                    1 =>
                        array(
                            'id' => 4,
                            'name' => 'Écrans',
                            'slug' => 'ecrans',
                        ),
                ),
            'geolocation' => null,
            'video' =>
                array(
                    'thumbnailUrl' => '//s3-eu-west-1.amazonaws.com/wizachatest/videos/414375b2-61cb-4260-b82b-4a2636cb5673/480-00001.png',
                    'videoUrl' => '//s3-eu-west-1.amazonaws.com/wizachatest/videos/414375b2-61cb-4260-b82b-4a2636cb5673/480.mp4',
                ),
            'declinations' =>
                array(
                    0 =>
                        array(
                            'id' => '3_8_7',
                            'code' => 'size_13',
                            'supplierReference' => 'INFO-ECRAN-001',
                            'price' => 15.5,
                            'originalPrice' => 15.5,
                            'crossedOutPrice' => null,
                            'prices' =>
                                array(
                                    'priceWithTaxes' => 15.5,
                                    'priceWithoutVat' => 15.18,
                                    'vat' => 0.32,
                                ),
                            'greenTax' => 0,
                            'amount' => 10,
                            'affiliateLink' => null,
                            'images' =>
                                array(),
                            'options' =>
                                array(
                                    0 =>
                                        array(
                                            'id' => 8,
                                            'name' => 'size',
                                            'variantId' => 7,
                                            'variantName' => '13',
                                        ),
                                ),
                            'isBrandNew' => true,
                            'company' =>
                                array(
                                    'id' => 3,
                                    'name' => 'The World Company Inc.',
                                    'slug' => 'the-world-company-inc.',
                                    'isProfessional' => true,
                                    'image' => null,
                                    'averageRating' => null,
                                ),
                            'isAvailable' => true,
                            'infiniteStock' => false,
                        ),
                    1 =>
                        array(
                            'id' => '3_8_8',
                            'code' => 'size_15',
                            'supplierReference' => 'INFO-ECRAN-001',
                            'price' => 15.5,
                            'originalPrice' => 15.5,
                            'crossedOutPrice' => null,
                            'prices' =>
                                array(
                                    'priceWithTaxes' => 15.5,
                                    'priceWithoutVat' => 15.18,
                                    'vat' => 0.32,
                                ),
                            'greenTax' => 0,
                            'amount' => 10,
                            'affiliateLink' => null,
                            'images' =>
                                array(),
                            'options' =>
                                array(
                                    0 =>
                                        array(
                                            'id' => 8,
                                            'name' => 'size',
                                            'variantId' => 8,
                                            'variantName' => '15',
                                        ),
                                ),
                            'isBrandNew' => true,
                            'company' =>
                                array(
                                    'id' => 3,
                                    'name' => 'The World Company Inc.',
                                    'slug' => 'the-world-company-inc.',
                                    'isProfessional' => true,
                                    'image' => null,
                                    'averageRating' => null,
                                ),
                            'isAvailable' => true,
                            'infiniteStock' => false,
                        ),
                    2 =>
                        array(
                            'id' => '3_8_9',
                            'code' => 'size_17',
                            'supplierReference' => 'INFO-ECRAN-001',
                            'price' => 15.5,
                            'originalPrice' => 15.5,
                            'crossedOutPrice' => null,
                            'prices' =>
                                array(
                                    'priceWithTaxes' => 15.5,
                                    'priceWithoutVat' => 15.18,
                                    'vat' => 0.32,
                                ),
                            'greenTax' => 0,
                            'amount' => 10,
                            'affiliateLink' => null,
                            'images' =>
                                array(),
                            'options' =>
                                array(
                                    0 =>
                                        array(
                                            'id' => 8,
                                            'name' => 'size',
                                            'variantId' => 9,
                                            'variantName' => '17',
                                        ),
                                ),
                            'isBrandNew' => true,
                            'company' =>
                                array(
                                    'id' => 3,
                                    'name' => 'The World Company Inc.',
                                    'slug' => 'the-world-company-inc.',
                                    'isProfessional' => true,
                                    'image' => null,
                                    'averageRating' => null,
                                ),
                            'isAvailable' => true,
                            'infiniteStock' => false,
                        ),
                    3 =>
                        array(
                            'id' => '3_8_10',
                            'code' => 'size_21',
                            'supplierReference' => 'INFO-ECRAN-001',
                            'price' => 15.5,
                            'originalPrice' => 15.5,
                            'crossedOutPrice' => null,
                            'prices' =>
                                array(
                                    'priceWithTaxes' => 15.5,
                                    'priceWithoutVat' => 15.18,
                                    'vat' => 0.32,
                                ),
                            'greenTax' => 0,
                            'amount' => 10,
                            'affiliateLink' => null,
                            'images' =>
                                array(),
                            'options' =>
                                array(
                                    0 =>
                                        array(
                                            'id' => 8,
                                            'name' => 'size',
                                            'variantId' => 10,
                                            'variantName' => '21',
                                        ),
                                ),
                            'isBrandNew' => true,
                            'company' =>
                                array(
                                    'id' => 3,
                                    'name' => 'The World Company Inc.',
                                    'slug' => 'the-world-company-inc.',
                                    'isProfessional' => true,
                                    'image' => null,
                                    'averageRating' => null,
                                ),
                            'isAvailable' => true,
                            'infiniteStock' => false,
                        ),
                    4 =>
                        array(
                            'id' => '4_8_10',
                            'code' => 'size_21_2',
                            'supplierReference' => 'INFO-ECRAN-001',
                            'price' => 15.5,
                            'originalPrice' => 15.5,
                            'crossedOutPrice' => null,
                            'prices' =>
                                array(
                                    'priceWithTaxes' => 15.5,
                                    'priceWithoutVat' => 15.18,
                                    'vat' => 0.32,
                                ),
                            'greenTax' => 0,
                            'amount' => 10,
                            'affiliateLink' => null,
                            'images' =>
                                array(),
                            'options' =>
                                array(
                                    0 =>
                                        array(
                                            'id' => 8,
                                            'name' => 'size',
                                            'variantId' => 10,
                                            'variantName' => '21',
                                        ),
                                ),
                            'isBrandNew' => true,
                            'company' =>
                                array(
                                    'id' => 4,
                                    'name' => 'The Other World Company Inc.',
                                    'slug' => 'the-other-world-company-inc.',
                                    'isProfessional' => true,
                                    'image' => null,
                                    'averageRating' => null,
                                ),
                            'isAvailable' => true,
                            'infiniteStock' => false,
                        ),
                    5 =>
                        array(
                            'id' => '6_8_10',
                            'code' => 'size_21_3',
                            'supplierReference' => 'INFO-ECRAN-001',
                            'price' => 15.5,
                            'originalPrice' => 15.5,
                            'crossedOutPrice' => null,
                            'prices' =>
                                array(
                                    'priceWithTaxes' => 15.5,
                                    'priceWithoutVat' => 15.18,
                                    'vat' => 0.32,
                                ),
                            'greenTax' => 0,
                            'amount' => 10,
                            'affiliateLink' => null,
                            'images' =>
                                array(),
                            'options' =>
                                array(
                                    0 =>
                                        array(
                                            'id' => 8,
                                            'name' => 'size',
                                            'variantId' => 10,
                                            'variantName' => '21',
                                        ),
                                ),
                            'isBrandNew' => true,
                            'company' =>
                                array(
                                    'id' => 5,
                                    'name' => 'The Other Other World Company Inc.',
                                    'slug' => 'the-other-other-world-company-inc.',
                                    'isProfessional' => true,
                                    'image' => null,
                                    'averageRating' => null,
                                ),
                            'isAvailable' => true,
                            'infiniteStock' => false,
                        ),
                ),
            'attributes' =>
                array(),
            'options' =>
                array(
                    0 =>
                        array(
                            'id' => 8,
                            'name' => 'size',
                            'variants' =>
                                array(
                                    0 =>
                                        array(
                                            'id' => 7,
                                            'name' => '13',
                                        ),
                                    1 =>
                                        array(
                                            'id' => 8,
                                            'name' => '15',
                                        ),
                                    2 =>
                                        array(
                                            'id' => 9,
                                            'name' => '17',
                                        ),
                                    3 =>
                                        array(
                                            'id' => 10,
                                            'name' => '21',
                                        ),
                                ),
                        ),
                ),
            'attachments' =>
                array(),
        ), new Uri('https://example.com/'));

        $otherOffers = $product->getOtherOffers($product->getDeclination(new DeclinationId('4_8_10')));
        $this->assertContainsOnly(Declination::class, $otherOffers);
        $this->assertCount(2, $otherOffers);
        $this->assertSame(['3_8_10', '6_8_10'], array_map(function (Declination $declination): string {
            return (string) $declination->getId();
        }, $otherOffers));

        $otherOffers = $product->getOtherOffers($product->getDeclination(new DeclinationId('6_8_10')));
        $this->assertContainsOnly(Declination::class, $otherOffers);
        $this->assertCount(2, $otherOffers);
        $this->assertSame(['3_8_10', '4_8_10'], array_map(function (Declination $declination): string {
            return (string) $declination->getId();
        }, $otherOffers));

        $otherOffers = $product->getOtherOffers($product->getDeclination(new DeclinationId('3_8_10')));
        $this->assertContainsOnly(Declination::class, $otherOffers);
        $this->assertCount(2, $otherOffers);
        $this->assertSame(['4_8_10', '6_8_10'], array_map(function (Declination $declination): string {
            return (string) $declination->getId();
        }, $otherOffers));

        // Declination without other offers
        $otherOffers = $product->getOtherOffers($product->getDeclination(new DeclinationId('3_8_9')));
        $this->assertCount(0, $otherOffers);

        $this->expectException(\InvalidArgumentException::class);
        $product->getOtherOffers(new Declination(array(
            'id' => '404_404_404',
            'code' => 'size_17',
            'supplierReference' => 'INFO-ECRAN-001',
            'price' => 15.5,
            'originalPrice' => 15.5,
            'crossedOutPrice' => null,
            'prices' =>
                array(
                    'priceWithTaxes' => 15.5,
                    'priceWithoutVat' => 15.18,
                    'vat' => 0.32,
                ),
            'greenTax' => 0,
            'amount' => 10,
            'affiliateLink' => null,
            'images' =>
                array(),
            'options' =>
                array(
                    0 =>
                        array(
                            'id' => 8,
                            'name' => 'size',
                            'variantId' => 9,
                            'variantName' => '17',
                        ),
                ),
            'isBrandNew' => true,
            'company' =>
                array(
                    'id' => 3,
                    'name' => 'The World Company Inc.',
                    'slug' => 'the-world-company-inc.',
                    'isProfessional' => true,
                    'image' => null,
                    'averageRating' => null,
                ),
            'isAvailable' => true,
            'infiniteStock' => false,
        )));
    }
}
