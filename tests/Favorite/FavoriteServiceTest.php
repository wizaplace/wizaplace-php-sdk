<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\Tests\Favorite;

use Wizaplace\Favorite\Exception\CannotFavoriteDisabledOrInexistentDeclination;
use Wizaplace\Favorite\Exception\FavoriteAlreadyExist;
use Wizaplace\Favorite\FavoriteService;
use Wizaplace\Tests\ApiTestCase;
use Wizaplace\User\ApiKey;
use Wizaplace\User\UserService;

class FavoriteServiceTest extends ApiTestCase
{
    /**
     * @var FavoriteService
     */
    private $favService;

    /**
     * @var ApiKey
     */
    private $apiKey;

    public function setUp(): void
    {
        parent::setUp();
        $userService = new UserService($this->getGuzzleClient());
        $this->apiKey = $userService->authenticate('admin@wizaplace.com', 'password');

        $this->favService = new FavoriteService($this->getGuzzleClient());

        $this->favService->addDeclinationToUserFavorites($this->apiKey, 1);
    }

    public function testAddProductToFavorite()
    {
        $this->favService->addDeclinationToUserFavorites($this->apiKey, 2);

        $this->assertTrue($this->favService->isInFavorites($this->apiKey, 2));
    }

    public function testAddFavProductToFavorite()
    {
        $this->expectException(FavoriteAlreadyExist::class);

        $this->favService->addDeclinationToUserFavorites($this->apiKey, 1);
    }

    public function testAddNotProductToFavorite()
    {
        $this->expectException(CannotFavoriteDisabledOrInexistentDeclination::class);

        $this->favService->addDeclinationToUserFavorites($this->apiKey, 404);
    }

    public function testIsNotFavorite()
    {
        $isFavorite = $this->favService->isInFavorites($this->apiKey, 3);

        $this->assertFalse($isFavorite);
    }

    public function testIsFavorite()
    {
        $isFavorite = $this->favService->isInFavorites($this->apiKey, 1);

        $this->assertTrue($isFavorite);
    }

    public function testRemoveProductFromFavorite()
    {
        $this->favService->removeDeclinationToUserFavorites($this->apiKey, 1);

        $this->assertFalse($this->favService->isInFavorites($this->apiKey, 1));
    }

    public function tearDown() :void
    {
        $this->favService->removeDeclinationToUserFavorites($this->apiKey, 1);
        $this->favService->removeDeclinationToUserFavorites($this->apiKey, 2);
        parent::tearDown();
    }
}
