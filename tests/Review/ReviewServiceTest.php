<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\Tests\Review;

use GuzzleHttp\Exception\ClientException;
use Wizaplace\Exception\NotFound;
use Wizaplace\Catalog\Review\ReviewService;
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

    public function testListReviewsFromProduct()
    {
        $reviews = $this->rs->getProductReviews(1);

        foreach ($reviews as $review) {
            $this->assertEquals('Administrateur Wizaplace', $review->getAuthor()->getName());
            $this->assertInternalType('string', $review->getMessage());
            $this->assertInstanceOf('DateTimeImmutable', $review->getPostedAt());
            $this->assertAttributeGreaterThanOrEqual(1, 'rating', $review);
            $this->assertAttributeLessThanOrEqual(5, 'rating', $review);
        }
    }

    public function testListInexistantReviewFromProduct()
    {
        $reviews = $this->rs->getProductReviews(2);
        $this->assertEmpty($reviews);
    }

    public function testListReviewsOnInexistantProduct()
    {
        $this->expectException(NotFound::class);
        $this->expectExceptionMessage('This product has not been found');

        $this->rs->getProductReviews(404);
    }

    public function testAddReviewToProduct()
    {
        $this->rs->reviewProduct(2, 'fake-author', 'this is a test review', 4);

        $this->assertCount(2, static::$historyContainer);
    }

    public function testAddReviewOnInexistantProduct()
    {
        $this->expectException(ClientException::class);
        $this->expectExceptionCode(404);

        $this->rs->reviewProduct(404, 'fake-author', 'this is a test review', 4);
    }

    public function testListReviewsFromCompany()
    {
        $reviews = $this->rs->getCompanyReviews(4);

        foreach ($reviews as $review) {
            $this->assertEquals('Paul Martin', $review->getAuthor()->getName());
            $this->assertEquals(3, $review->getAuthor()->getId());
            $this->assertEquals('user@wizaplace.com', $review->getAuthor()->getEmail());
            $this->assertEquals('this is a test review', $review->getMessage());
            $this->assertInstanceOf('DateTimeImmutable', $review->getPostedAt());
            $this->assertEquals(2, $review->getRating());
        }
    }

    public function testListInexistantReviewFromCompany()
    {
        $reviews = $this->rs->getCompanyReviews(2);

        $this->assertEmpty($reviews);
    }

    public function testAddReviewToCompany()
    {
        $this->rs->reviewCompany(4, 'test company review', 1);

        $this->assertCount(2, static::$historyContainer);
    }

    public function testAddReviewOnInexistantCompany()
    {
        $this->expectException(ClientException::class);
        $this->expectExceptionCode(404);

        $this->rs->reviewCompany(404, 'test company review', 2);
    }

    private function buildReviewService(): ReviewService
    {
        $client = $this->buildApiClient();
        $client->authenticate('user@wizaplace.com', 'password');

        return new ReviewService($client);
    }
}
