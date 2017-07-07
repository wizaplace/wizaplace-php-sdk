<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */

namespace Wizaplace\Catalog\Review;

use Wizaplace\AbstractService;
use Wizaplace\Exception\NotFound;

class ReviewService extends AbstractService
{
    /**
     * @return ProductReview[]
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
            $productReview = $this->createProductReview($review);
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
     * @return CompanyReview[]
     * @throws NotFound
     */
    public function getCompanyReviews(int $companyId): ?array
    {
        try {
            $reviews = $this->client->get('catalog/companies/'.$companyId.'/reviews');
        } catch (\Exception $e) {
            throw new NotFound('This company has not been found');
        }

        $companyReviews = [];
        if (!empty($reviews['_embedded'])) {
            foreach ($reviews['_embedded'] as $review) {
                $companyReviews[] = $this->createCompanyReview($review);
            }

            return $companyReviews;
        } else {
            return null;
        }
    }

    public function reviewCompany(int $companyId, string $message, int $rating): void
    {
        $review = ['message' => $message, 'rating' => $rating];

        $this->client->post('catalog/companies/'.$companyId.'/reviews', ['json' => $review]);
    }

    private function createProductReview(array $review): ProductReview
    {
        return new ProductReview(
            $review['author'],
            $review['message'],
            $review['postedAt'],
            $review['rating']
        );
    }

    private function createCompanyReview(array $review): CompanyReview
    {
        return new CompanyReview(
            $review['author']['id'],
            $review['author']['name'],
            $review['author']['email'],
            $review['message'],
            $review['rating'],
            $review['postedAt']
        );
    }
}
