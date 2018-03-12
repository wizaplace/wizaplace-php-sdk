<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\SDK\Tests\Favorite;

use Wizaplace\SDK\Catalog\DeclinationId;
use Wizaplace\SDK\Catalog\DeclinationSummary;
use Wizaplace\SDK\Favorite\Exception\CannotFavoriteDisabledOrInexistentDeclination;
use Wizaplace\SDK\Favorite\Exception\FavoriteAlreadyExist;
use Wizaplace\SDK\Favorite\FavoriteService;
use Wizaplace\SDK\Tests\ApiTestCase;

final class FavoriteServiceTest extends ApiTestCase
{
    /**
     * @var FavoriteService
     */
    private $favService;

    public function setUp(): void
    {
        parent::setUp();
        $this->favService = $this->buildFavoriteService();

        $this->favService->addDeclinationToUserFavorites(new DeclinationId('1'));
    }

    public function testAddProductToFavorite()
    {
        $this->favService->addDeclinationToUserFavorites(new DeclinationId('2_7_1_8_5'));

        $this->assertTrue($this->favService->isInFavorites(new DeclinationId('2_7_1_8_5')));
    }

    public function testAddFavProductToFavorite()
    {
        $this->expectException(FavoriteAlreadyExist::class);

        $this->favService->addDeclinationToUserFavorites(new DeclinationId('1'));
    }

    public function testAddNotProductToFavorite()
    {
        $this->expectException(CannotFavoriteDisabledOrInexistentDeclination::class);

        $this->favService->addDeclinationToUserFavorites(new DeclinationId('404'));
    }

    public function testIsNotFavorite()
    {
        $isFavorite = $this->favService->isInFavorites(new DeclinationId('3'));

        $this->assertFalse($isFavorite);
    }

    public function testIsFavorite()
    {
        $isFavorite = $this->favService->isInFavorites(new DeclinationId('1_0'));

        $this->assertTrue($isFavorite);
    }

    public function testGetAll()
    {
        $this->favService->addDeclinationToUserFavorites(new DeclinationId('2'));
        $favorites = $this->favService->getAll();
        $this->favService->removeDeclinationToUserFavorites(new DeclinationId('2'));

        $this->assertTrue(is_array($favorites));
        $this->assertCount(2, $favorites);
        $this->assertInstanceOf(DeclinationSummary::class, reset($favorites));
        $this->assertSame('1', reset($favorites)->getProductId());
        $this->assertTrue(current($favorites)->isAvailable());
        next($favorites);
        $this->assertSame('2', current($favorites)->getProductId());
        $this->assertTrue(current($favorites)->isAvailable());
    }

    public function testRemoveProductFromFavorite()
    {
        $this->favService->removeDeclinationToUserFavorites(new DeclinationId('1'));

        $this->assertFalse($this->favService->isInFavorites(new DeclinationId('1')));
    }

    public function tearDown() :void
    {
        $this->favService->removeDeclinationToUserFavorites(new DeclinationId('1'));
        $this->favService->removeDeclinationToUserFavorites(new DeclinationId('2'));
        parent::tearDown();
    }

    private function buildFavoriteService(): FavoriteService
    {
        $client = $this->buildApiClient();
        $client->authenticate('customer-1@world-company.com', 'password-customer-1');

        return new FavoriteService($client);
    }
}
