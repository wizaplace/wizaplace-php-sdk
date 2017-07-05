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
use Wizaplace\Review\CompanyReview;
use Wizaplace\Review\ProductReview;
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

    public function testListReviewsFromProduct()
    {
        $productReviews = $this->rs->getProductReviews(1);

        /** @var ProductReview $productReview */
        foreach ($productReviews->getReviews() as $productReview) {
            $this->assertEquals('Administrateur Wizaplace', $productReview->getAuthor());
            $this->assertAttributeGreaterThanOrEqual(1, 'rating', $productReview);
            $this->assertAttributeLessThanOrEqual(5, 'rating', $productReview);
        }
    }

    public function testListInexistantReviewFromProduct()
    {
        $this->expectException(NotFound::class);
        $this->expectExceptionMessage('This product has no reviews');

        $this->rs->getProductReviews(2);
    }

    public function testListReviewsOnInexistantProduct()
    {
        $this->expectException(NotFound::class);
        $this->expectExceptionMessage('This product has not been found');

        $this->rs->getProductReviews(404);
    }

    public function testAddReviewToProduct()
    {
        $this->rs->addProductReview(2, 'fake-author', 'this is a test review', 4);

        $this->assertCount(2, static::$historyContainer);
    }

    public function testAddReviewOnInexistantProduct()
    {
        $this->expectException(ClientException::class);
        $this->expectExceptionCode(404);

        $this->rs->addProductReview(404, 'fake-author', 'this is a test review', 4);
    }

    public function testListReviewsFromCompany()
    {
        $reviews = $this->rs->getCompanyReviews(1);

        /** @var CompanyReview $companyReview */
        foreach ($reviews->getReviews() as $companyReview) {
            $this->assertEquals('Paul Martin', $companyReview->getUserName());
            $this->assertEquals(2, $companyReview->getRating());
        }
    }

    public function testListInexistantReviewFromCompany()
    {
        $this->expectException(NotFound::class);
        $this->expectExceptionMessage('This company has no reviews');

        $this->rs->getCompanyReviews(2);
    }

    public function testAddReviewToCompany()
    {
        $this->rs->addCompanyReview(1, 'testcreview', 1);

        $this->assertCount(2, static::$historyContainer);
    }

    public function testAddReviewOnInexistantCompany()
    {
        $this->expectException(ClientException::class);
        $this->expectExceptionCode(404);

        $this->rs->addCompanyReview(404, 'this is a test review', 2);
    }

    private function buildReviewService(): ReviewService
    {
        $client = $this->buildApiClient();
        $client->authenticate('user@wizaplace.com', 'password');

        return new ReviewService($client);
    }
}
