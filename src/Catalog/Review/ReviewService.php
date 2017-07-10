<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\Catalog\Review;

use Wizaplace\AbstractService;
use Wizaplace\Exception\NotFound;

/**
 * This service helps getting and creating reviews for products or companies
 *
 * Example :
 *
 *      // Get a product reviews
 *      $productReviews = $reviewService->getProductReviews($productId);
 *
 *      // Create a product review with form data $formData
 *      $reviewService->reviewProduct($productId, $formData['author'], $formData['message'], $formData['rating']);
 *
 *      // Get a company reviews
 *      $companyReviews = $reviewService->getCompanyReviews($companyId);
 *
 *      // Create a company review with form data $formData, only users who have purchased once from the company are
 *      able to post a review.
 *      TODO: Add an API to check if the user is able to review a company
 *      $reviewService->reviewCompany($companyId, $formData['message'], $formData['rating']);
 */
class ReviewService extends AbstractService
{
    /**
     * @return Review[]
     * @throws NotFound
     */
    public function getProductReviews(int $productId): array
    {
        try {
            $reviews = $this->client->get('catalog/products/'.$productId.'/reviews');
        } catch (\Exception $e) {
            throw new NotFound('This product has not been found');
        }

        $productReviews = [];
        foreach ($reviews as $review) {
            $productReview = $this->createReview($review);
            $productReviews[] = $productReview;
        }

        return $productReviews;
    }

    public function reviewProduct(int $productId, string $author, string $message, int $rating) : void
    {
        $review = ['author' => $author, 'message' => $message, 'rating' => $rating];

        $this->client->post('catalog/products/'.$productId.'/reviews', ['json' => $review]);
    }

    /**
     * @return Review[]
     * @throws NotFound
     */
    public function getCompanyReviews(int $companyId): array
    {
        try {
            $reviews = $this->client->get('catalog/companies/'.$companyId.'/reviews');
        } catch (\Exception $e) {
            throw new NotFound('This company has not been found');
        }

        $companyReviews = [];
        if (!empty($reviews['_embedded'])) {
            foreach ($reviews['_embedded'] as $review) {
                $companyReviews[] = $this->createReview($review);
            }
        }

        return $companyReviews;
    }

    public function reviewCompany(int $companyId, string $message, int $rating): void
    {
        $review = ['message' => $message, 'rating' => $rating];

        $this->client->post('catalog/companies/'.$companyId.'/reviews', ['json' => $review]);
    }

    private function createReview(array $review): Review
    {
        if (is_array($review['author'])) {
            //if $review['author'] is an array, then it's a companyReview
            $author = $this->createReviewAuthor($review['author']['name'], $review['author']['id'], $review['author']['email']);
        } else { // else it's a productReview
            $author = $this->createReviewAuthor($review['author']);
        }

        return new Review(
            $author,
            $review['message'],
            $review['postedAt'],
            $review['rating']
        );
    }

    private function createReviewAuthor(string $name, ?int $id = null, ?string $email = null): ReviewAuthor
    {
        return new ReviewAuthor($name, $id, $email);
    }
}
