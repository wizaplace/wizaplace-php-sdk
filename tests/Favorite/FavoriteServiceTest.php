<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\Tests\Favorite;

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

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        $userService = new UserService(self::getGuzzleClient());
        $apiKey = $userService->authenticate('admin@wizaplace.com', 'password');

        $favService = new FavoriteService(self::getGuzzleClient());

        $favService->addToFavorite($apiKey, 1);
        $favService->addToFavorite($apiKey, 2);
        $favService->addToFavorite($apiKey, 3);
    }

    public function setUp(): void
    {
        parent::setUp();
        $userService = new UserService($this->getGuzzleClient());
        $this->apiKey = $userService->authenticate('admin@wizaplace.com', 'password');

        $this->favService = new FavoriteService($this->getGuzzleClient());
    }

    public function testAddProductToFavorite()
    {
        $responseCode = $this->favService->addToFavorite($this->apiKey, 4);

        $this->assertEquals(201, $responseCode);
    }

    public function testAddFavProductToFavorite()
    {
        $responseCode = $this->favService->addToFavorite($this->apiKey, 1);

        $this->assertEquals(409, $responseCode);
    }

    public function testAddNotProductToFavorite()
    {
        $responseCode = $this->favService->addToFavorite($this->apiKey, 7);

        $this->assertEquals(400, $responseCode);
    }

    public function testIsNotFavorite()
    {
        $isFavorite = $this->favService->isFavorite($this->apiKey, 5);

        $this->assertEquals(false, $isFavorite);
    }

    public function testIsFavorite()
    {
        $isFavorite = $this->favService->isFavorite($this->apiKey, 2);

        $this->assertEquals(true, $isFavorite);
    }

    public function testRemoveProductFromFavorite()
    {
        $responseCode = $this->favService->removeFromFavorite($this->apiKey, 3);

        $this->assertEquals(204, $responseCode);
    }

    public function testRemoveNotProductFromFavorite()
    {
        $responseCode = $this->favService->removeFromFavorite($this->apiKey, 7);

        $this->assertEquals(204, $responseCode);
    }

    public static function tearDownAfterClass()
    {
        parent::tearDownAfterClass();
        $userService = new UserService(self::getGuzzleClient());
        $apiKey = $userService->authenticate('admin@wizaplace.com', 'password');

        $favService = new FavoriteService(self::getGuzzleClient());
        $favService->removeFromFavorite($apiKey, 1);
        $favService->removeFromFavorite($apiKey, 2);
        $favService->removeFromFavorite($apiKey, 3);
        $favService->removeFromFavorite($apiKey, 4);
    }
}
