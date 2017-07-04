<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\Review;

use function GuzzleHttp\default_ca_bundle;
use GuzzleHttp\RequestOptions;
use Wizaplace\AbstractService;
use Wizaplace\Exception\NotFound;

class ReviewService extends AbstractService
{
    public function getProductReview(int $productId) : Review
    {
        try {
             $result = $this->client->get('catalog/products/'.$productId.'/reviews');
        } catch (\Exception $e) {
            throw new NotFound('This product has not been found');
        }

        if (!$result[0]) {
            throw new NotFound('This product has no reviews');
        } else {
            return new Review($result[0]['author'], $result[0]['message'], (int) $result[0]['postedAt'], $result[0]['rating']);
        }
    }

    public function addProductReview(int $productId, string $author ,string $message, int $rating) : void
    {
        $review = json_encode(['author' => $author, 'message' => $message, 'rating' => $rating]);

        $options = [
            RequestOptions::HEADERS => [
                "Content-Type" => "application/json",
            ],
            RequestOptions::BODY => $review
        ];

        $this->client->post('catalog/products/'.$productId.'/reviews', $options);
    }
}
