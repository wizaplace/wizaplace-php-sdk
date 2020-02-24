<?php

/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Catalog\Review;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\RequestOptions;
use Wizaplace\SDK\AbstractService;
use Wizaplace\SDK\Exception\NotFound;
use Wizaplace\SDK\Exception\ProductNotFound;
use Wizaplace\SDK\Exception\ReviewsAreDisabled;

/**
 * Class ReviewService
 * @package Wizaplace\SDK\Catalog\Review
 *
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
 *      //Check the ability of the user to review company
 *      $reviewService->canUserReviewCompany($companyId);
 *
 *      // Create a company review with form data $formData, only users who have purchased once from the company are
 *      able to post a review.
 *      $reviewService->reviewCompany($companyId, $formData['message'], $formData['rating']);
 */
final class ReviewService extends AbstractService
{
    private const COMPANY_ENDPOINT = "catalog/companies/%s/reviews";
    private const PRODUCT_ENDPOINT = "catalog/products/%s/reviews";
    private const AUTHORIZATION_ENDPOINT = "catalog/companies/%s/reviews/authorized";
    private const AUTHORIZATION_PRODUCT_ENDPOINT = "catalog/products/%s/reviews/authorized";

    /**
     * @param string $productId
     *
     * @return Review[]
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Wizaplace\SDK\Exception\JsonDecodingError
     */
    public function getProductReviews(string $productId): array
    {
        $reviews = $this->client->get(sprintf(self::PRODUCT_ENDPOINT, $productId));

        $productReviews = [];
        foreach ($reviews as $review) {
            $productReviews[] = $this->createReview($review);
        }

        return $productReviews;
    }

    /**
     * @param string $productId
     * @param string $author
     * @param string $message
     * @param int    $rating
     *
     * @throws \Wizaplace\SDK\Exception\JsonDecodingError
     * @throws ProductNotFound
     * @throws ReviewsAreDisabled
     */
    public function reviewProduct(string $productId, string $author, string $message, int $rating): void
    {
        $review = [
            'author' => $author,
            'message' => $message,
            'rating' => $rating,
        ];

        $this->client->post(sprintf(self::PRODUCT_ENDPOINT, $productId), [RequestOptions::JSON => $review]);
    }

    /**
     * @return Review[]
     * @throws NotFound
     */
    public function getCompanyReviews(int $companyId): array
    {
        try {
            $reviews = $this->client->get(sprintf(self::COMPANY_ENDPOINT, $companyId));
        } catch (ClientException $e) {
            if ($e->getCode() === 404) {
                throw new NotFound('This company has not been found', $e);
            }
            throw $e;
        }

        $companyReviews = [];
        if (!empty($reviews['_embedded'])) {
            foreach ($reviews['_embedded'] as $review) {
                $companyReviews[] = $this->createReview($review);
            }
        }

        return $companyReviews;
    }

    /**
     * @param int    $companyId
     * @param string $message
     * @param int    $rating
     *
     * @throws \Wizaplace\SDK\Exception\JsonDecodingError
     */
    public function reviewCompany(int $companyId, string $message, int $rating): void
    {
        $review = ['message' => $message, 'rating' => $rating];

        $this->client->post(sprintf(self::COMPANY_ENDPOINT, $companyId), [RequestOptions::JSON => $review]);
    }

    /**
     * @param int $companyId
     *
     * @return bool
     * @throws \Wizaplace\SDK\Exception\JsonDecodingError
     */
    public function canUserReviewCompany(int $companyId): bool
    {
        try {
            $this->client->get(sprintf(self::AUTHORIZATION_ENDPOINT, $companyId));
        } catch (ClientException $e) {
            if ($e->getCode() === 401) {
                return false;
            }

            throw $e;
        }

        return true;
    }

    /**
     * @param string $productId
     *
     * @return bool
     * @throws \Wizaplace\SDK\Exception\JsonDecodingError
     */
    public function canUserReviewProduct(string $productId): bool
    {
        try {
            $this->client->get(sprintf(self::AUTHORIZATION_PRODUCT_ENDPOINT, $productId));
        } catch (ClientException $e) {
            if ($e->getCode() === 401) {
                return false;
            }
            throw $e;
        }

        return true;
    }

    /**
     * @param array $review
     *
     * @return Review
     */
    private function createReview(array $review): Review
    {
        if (\is_array($review['author'])) {
            //if $review['author'] is an array, then it's a companyReview
            $author = $this->createAuthor($review['author']['name'], $review['author']['id'], $review['author']['email']);
        } else { // else it's a productReview
            $author = $this->createAuthor($review['author']);
        }

        return new Review(
            $author,
            $review['message'],
            $review['postedAt'],
            $review['rating']
        );
    }

    /**
     * @param string      $name
     * @param int|null    $id
     * @param string|null $email
     *
     * @return Author
     */
    private function createAuthor(string $name, ?int $id = null, ?string $email = null): Author
    {
        return new Author($name, $id, $email);
    }
}
