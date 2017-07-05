<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\Review;

class ProductReviews
{
    /**
     * @var ProductReview[]
     */
    private $reviews;

    /**
     * @return ProductReview[]
     */
    public function getReviews(): array
    {
        return $this->reviews;
    }

    public function addReview(ProductReview $productReview): ProductReviews
    {
        $this->reviews[] = $productReview;

        return $this;
    }
}
