<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */

namespace Wizaplace\Review;

class CompanyReviews
{
    /**
     * @var int
     */
    private $averageRating;

    /**
     * @var array
     */
    private $reviews;

    public function __construct(int $averageRating)
    {
        $this->averageRating = $averageRating;
    }

    public function getAverageRating(): int
    {
        return $this->averageRating;
    }

    public function getReviews(): ?array
    {
        return $this->reviews;
    }

    public function addReview(CompanyReview $companyReview): CompanyReviews
    {
        $this->reviews[] = $companyReview;

        return $this;
    }
}
