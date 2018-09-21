<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\SDK\Tests\Review;

use Wizaplace\SDK\Catalog\Review\ReviewService;
use Wizaplace\SDK\Exception\CompanyNotFound;
use Wizaplace\SDK\Exception\ProductNotFound;
use Wizaplace\SDK\Tests\ApiTestCase;

final class ReviewServiceTest extends ApiTestCase
{
    /**
     * @var ReviewService
     */
    private $reviewService;

    public function setUp(): void
    {
        parent::setUp();
        $this->reviewService = $this->buildReviewService();
    }

    public function testListReviewsFromProduct()
    {
        $reviews = $this->reviewService->getProductReviews('1');

        $this->assertCount(2, $reviews);

        // Première review
        $this->assertSame('Michael Jordan', $reviews[0]->getAuthor()->getName());
        $this->assertEmpty($reviews[0]->getAuthor()->getId());
        $this->assertEmpty($reviews[0]->getAuthor()->getEmail());
        $this->assertSame('Very good product <3', $reviews[0]->getMessage());
        $this->assertInstanceOf('DateTimeImmutable', $reviews[0]->getPostedAt());
        $this->assertSame(4, $reviews[0]->getRating());

        // Deuxième review
        $this->assertSame('Dave Matthews', $reviews[1]->getAuthor()->getName());
        $this->assertEmpty($reviews[1]->getAuthor()->getId());
        $this->assertEmpty($reviews[1]->getAuthor()->getEmail());
        $this->assertSame('I try to forget it !', $reviews[1]->getMessage());
        $this->assertInstanceOf('DateTimeImmutable', $reviews[1]->getPostedAt());
        $this->assertSame(1, $reviews[1]->getRating());
    }

    public function testListInexistantReviewFromProduct()
    {
        $reviews = $this->reviewService->getProductReviews('2');
        $this->assertEmpty($reviews);
    }

    public function testListReviewsOnInexistantProduct()
    {
        $reviews = $this->reviewService->getProductReviews('404');
        $this->assertCount(0, $reviews);
    }

    public function testAddReviewToProduct()
    {
        $this->reviewService->reviewProduct('2', 'fake-author', 'this is a test review', 5);

        $this->assertCount(2, static::$historyContainer);
    }

    public function testAddReviewOnInexistantProduct()
    {
        $this->expectException(ProductNotFound::class);
        $this->expectExceptionCode(404);

        $this->reviewService->reviewProduct('404', 'fake-author', 'this is a test review', 4);
    }

    public function testListReviewsFromCompany()
    {
        $reviews = $this->reviewService->getCompanyReviews(3);

        $this->assertCount(2, $reviews);

        // Première review
        $this->assertSame('Michael Jordan', $reviews[0]->getAuthor()->getName());
        $this->assertSame(7, $reviews[0]->getAuthor()->getId());
        $this->assertSame('customer-1@world-company.com', $reviews[0]->getAuthor()->getEmail());
        $this->assertSame('Very good customer support.', $reviews[0]->getMessage());
        $this->assertInstanceOf('DateTimeImmutable', $reviews[0]->getPostedAt());
        $this->assertSame(4, $reviews[0]->getRating());

        // Deuxième review
        $this->assertSame('Dave Matthews', $reviews[1]->getAuthor()->getName());
        $this->assertSame(8, $reviews[1]->getAuthor()->getId());
        $this->assertSame('customer-2@world-company.com', $reviews[1]->getAuthor()->getEmail());
        $this->assertSame('This company is unbelievable !!', $reviews[1]->getMessage());
        $this->assertInstanceOf('DateTimeImmutable', $reviews[1]->getPostedAt());
        $this->assertSame(5, $reviews[1]->getRating());
    }

    public function testListInexistantReviewFromCompany()
    {
        $reviews = $this->reviewService->getCompanyReviews(2);

        $this->assertEmpty($reviews);
    }

    public function testAddReviewToCompany()
    {
        $this->reviewService->reviewCompany(3, 'test company review', 1);

        $this->assertCount(2, static::$historyContainer);
    }

    public function testAddReviewOnInexistantCompany()
    {
        $this->expectException(CompanyNotFound::class);
        $this->expectExceptionCode(404);

        $this->reviewService->reviewCompany(404, 'test company review', 2);
    }

    public function testUserCanReviewCompany()
    {
        $response = $this->reviewService->canUserReviewCompany(3);

        $this->assertTrue($response);
    }

    public function testUserCannotReviewCompany()
    {
        $response = $this->reviewService->canUserReviewCompany(1);

        $this->assertFalse($response);
    }

    public function testCanUserReviewProduct()
    {
        $response = $this->reviewService->canUserReviewProduct((string) 1);

        var_dump($response);
        die();

        $this->assertTrue($response);
    }

    public function testCannotUserReviewProduct()
    {
        $response = $this->reviewService->canUserReviewProduct((string) 1);
        $this->assertFalse($response);
    }

    private function buildReviewService(): ReviewService
    {
        $client = $this->buildApiClient();
        $client->authenticate('customer-1@world-company.com', 'password-customer-1');

        return new ReviewService($client);
    }
}
