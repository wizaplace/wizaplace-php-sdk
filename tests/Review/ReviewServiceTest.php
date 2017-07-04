<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\tests\Review;

use GuzzleHttp\Exception\ClientException;
use Wizaplace\Exception\NotFound;
use Wizaplace\Review\ReviewService;
use Wizaplace\Tests\ApiTestCase;

class ReviewServiceTest extends ApiTestCase
{
    /**
     * @var $rs ReviewService
     */
    private $rs;

    public function setUp(): void
    {
        parent::setUp();
        $this->rs = $this->buildReviewService();
    }

    public function testGetReviewOnProduct()
    {
        $review = $this->rs->getProductReview(1);

        $this->assertEquals('Administrateur Wizaplace', $review->getAuthor());
    }

    public function testGetInexistantReviewOnProduct()
    {
        $this->expectException(NotFound::class);
        $this->expectExceptionMessage('This product has no reviews');

        $this->rs->getProductReview(2);
    }

    public function testGetReviewOnInexistantProduct()
    {
        $this->expectException(NotFound::class);
        $this->expectExceptionMessage('This product has not been found');

        $this->rs->getProductReview(404);
    }

    public function testAddReviewOnProduct()
    {
        $this->rs->addProductReview(2, 'fake-author', 'this is a test review', 4);

        $this->assertCount(2, static::$historyContainer);
    }

    public function testAddReviewOnInvalidProduct()
    {
        $this->expectException(ClientException::class);
        $this->expectExceptionCode(404);

        $this->rs->addProductReview(404, 'fake-author', 'this is a test review', 4);
    }

    private function buildReviewService(): ReviewService
    {
        $client = $this->buildApiClient();
        $client->authenticate('admin@wizaplace.com', 'password');

        return new ReviewService($client);
    }
}
