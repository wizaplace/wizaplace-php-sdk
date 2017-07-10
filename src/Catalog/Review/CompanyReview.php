<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\Catalog\Review;

class CompanyReview
{
    /**
     * @var ReviewAuthor
     */
    private $reviewAuthor;

    /**
     * @var string
     */
    private $message;

    /**
     * @var int
     */
    private $rating;

    /**
     * @var \DateTimeImmutable
     */
    private $postedAt;

    public function __construct(
        ReviewAuthor $reviewAuthor,
        string $message,
        int $rating,
        string $postedAt
    ) {
        $this->reviewAuthor = $reviewAuthor;
        $this->message = $message;
        $this->rating = $rating;
        $this->postedAt = new \DateTimeImmutable("@$postedAt");
    }

    public function getReviewAuthor(): ReviewAuthor
    {
        return $this->reviewAuthor;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getRating(): int
    {
        return $this->rating;
    }

    public function getPostedAt(): \DateTimeImmutable
    {
        return $this->postedAt;
    }
}
