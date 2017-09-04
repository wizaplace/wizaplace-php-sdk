<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\Tests\Favorite;

use Wizaplace\Catalog\DeclinationSummary;
use Wizaplace\Favorite\Exception\CannotFavoriteDisabledOrInexistentDeclination;
use Wizaplace\Favorite\Exception\FavoriteAlreadyExist;
use Wizaplace\Favorite\FavoriteService;
use Wizaplace\Tests\ApiTestCase;
use Wizaplace\User\ApiKey;

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

        $this->favService->addDeclinationToUserFavorites('1');
    }

    public function testAddProductToFavorite()
    {
        $this->favService->addDeclinationToUserFavorites('2');

        $this->assertTrue($this->favService->isInFavorites('2'));
    }

    public function testAddFavProductToFavorite()
    {
        $this->expectException(FavoriteAlreadyExist::class);

        $this->favService->addDeclinationToUserFavorites('1');
    }

    public function testAddNotProductToFavorite()
    {
        $this->expectException(CannotFavoriteDisabledOrInexistentDeclination::class);

        $this->favService->addDeclinationToUserFavorites('404');
    }

    public function testIsNotFavorite()
    {
        $isFavorite = $this->favService->isInFavorites('3');

        $this->assertFalse($isFavorite);
    }

    public function testIsFavorite()
    {
        $isFavorite = $this->favService->isInFavorites('1');

        $this->assertTrue($isFavorite);
    }

    public function testGetAll()
    {
        $this->favService->addDeclinationToUserFavorites('2');
        $favorites = $this->favService->getAll();
        $this->favService->removeDeclinationToUserFavorites('2');

        $this->assertTrue(is_array($favorites));
        $this->assertCount(2, $favorites);
        $this->assertInstanceOf(DeclinationSummary::class, reset($favorites));
        $this->assertSame(1, reset($favorites)->getProductId());
        next($favorites);
        $this->assertSame(2, current($favorites)->getProductId());
    }

    public function testRemoveProductFromFavorite()
    {
        $this->favService->removeDeclinationToUserFavorites('1');

        $this->assertFalse($this->favService->isInFavorites('1'));
    }

    public function tearDown() :void
    {
        $this->favService->removeDeclinationToUserFavorites('1');
        $this->favService->removeDeclinationToUserFavorites('2');
        parent::tearDown();
    }

    private function buildFavoriteService(): FavoriteService
    {
        $client = $this->buildApiClient();
        $client->authenticate('admin@wizaplace.com', 'password');

        return new FavoriteService($client);
    }
}
