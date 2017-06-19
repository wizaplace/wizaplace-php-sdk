<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\Tests\Favorite;

use Wizaplace\Favorite\Exception\CannotFavoriteDisabledOrInexistantDeclination;
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

        $this->favService->addToFavorite($this->apiKey, 1);
    }

    public function testAddProductToFavorite()
    {
        $this->favService->addToFavorite($this->apiKey, 2);

        $this->assertEquals(true, $this->favService->isFavorite($this->apiKey, 2));
    }

    public function testAddFavProductToFavorite()
    {
        $this->expectException(FavoriteAlreadyExist::class);

        $this->favService->addToFavorite($this->apiKey, 1);
    }

    public function testAddNotProductToFavorite()
    {
        $this->expectException(CannotFavoriteDisabledOrInexistantDeclination::class);

        $this->favService->addToFavorite($this->apiKey, 404);
    }

    public function testIsNotFavorite()
    {
        $isFavorite = $this->favService->isFavorite($this->apiKey, 3);

        $this->assertEquals(false, $isFavorite);
    }

    public function testIsFavorite()
    {
        $isFavorite = $this->favService->isFavorite($this->apiKey, 1);

        $this->assertEquals(true, $isFavorite);
    }

    public function testRemoveProductFromFavorite()
    {
        $this->favService->removeFromFavorite($this->apiKey, 1);

        $this->assertEquals(false, $this->favService->isFavorite($this->apiKey, 1));
    }

    public function tearDown() :void
    {
        $this->favService->removeFromFavorite($this->apiKey, 1);
        $this->favService->removeFromFavorite($this->apiKey, 2);
        parent::tearDown();
    }
}
