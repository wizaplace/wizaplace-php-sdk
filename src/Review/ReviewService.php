<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\Review;

use GuzzleHttp\RequestOptions;
use Wizaplace\AbstractService;
use Wizaplace\Exception\NotFound;

class ReviewService extends AbstractService
{
    public function getProductReviews(int $productId) : ProductReviews
    {
        try {
             $reviews = $this->client->get('catalog/products/'.$productId.'/reviews');
        } catch (\Exception $e) {
            throw new NotFound('This product has not been found');
        }

        $productReviews = new ProductReviews();
        foreach ($reviews as $review) {
            $productReview = $this->createProductReview($review);
            $productReviews->addReview($productReview);
        }

        if (!empty($productReviews->getReviews())) {
            return $productReviews;
        } else {
            throw new NotFound('This product has no reviews');
        }
    }

    public function addProductReview(int $productId, string $author, string $message, int $rating) : void
    {
        $review = json_encode(['author' => $author, 'message' => $message, 'rating' => $rating]);

        $options = [
            RequestOptions::HEADERS => [
                "Content-Type" => "application/json",
            ],
            RequestOptions::BODY => $review,
        ];

        $this->client->post('catalog/products/'.$productId.'/reviews', $options);
    }

    public function getCompanyReviews(int $companyId): CompanyReviews
    {
        try {
            $reviews = $this->client->get('catalog/companies/'.$companyId.'/reviews');
        } catch (\Exception $e) {
            throw new NotFound('This company has not been found');
        }

        if ($reviews['averageRating'] !== null) {
            $companyReviews = new CompanyReviews($reviews['averageRating']);
            foreach ($reviews['_embedded'] as $review) {
                $companyReview = $this->createCompanyReview($review);
                $companyReviews->addReview($companyReview);
            }

            return $companyReviews;
        } else {
            throw new NotFound('This company has no reviews');
        }
    }

    public function addCompanyReview(int $companyId, string $message, int $rating): void
    {
        $review = json_encode(['message' => $message, 'rating' => $rating]);

        $options = [
            RequestOptions::HEADERS => [
                "Content-Type" => "application/json",
            ],
            RequestOptions::BODY => $review,
        ];

        $this->client->post('catalog/companies/'.$companyId.'/reviews', $options);
    }

    private function createProductReview(array $review): ProductReview
    {
        return new ProductReview(
            $review['author'],
            $review['message'],
            (int) $review['postedAt'],
            $review['rating']
        );
    }

    private function createCompanyReview(array $review): CompanyReview
    {
        return new CompanyReview(
            $review['threadId'],
            $review['user']['id'],
            $review['user']['name'],
            $review['user']['email'],
            $review['message'],
            $review['rating'],
            $review['status'],
            $review['createdAt']
        );
    }
}
