<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\Review;

class CompanyReviews
{
    /**
     * @var int
     */
    private $averageRating;

    /**
     * @var CompanyReview[]
     */
    private $reviews = [];

    public function __construct(int $averageRating)
    {
        $this->averageRating = $averageRating;
    }

    public function getAverageRating(): int
    {
        return $this->averageRating;
    }

    /**
     * @return CompanyReview[]
     */
    public function getReviews(): array
    {
        return $this->reviews;
    }

    public function addReview(CompanyReview $companyReview): void
    {
        $this->reviews[] = $companyReview;
    }
}
