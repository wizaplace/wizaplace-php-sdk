<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\Catalog\Review;

class ProductReview
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
     * @var \DateTimeImmutable
     */
    private $postedAt;

    /**
     * @var int
     */
    private $rating;

    public function __construct(
        ReviewAuthor $reviewAuthor,
        string $message,
        string $postedAt,
        int $rating
    ) {
        $this->reviewAuthor = $reviewAuthor;
        $this->message = $message;
        $this->postedAt = new \DateTimeImmutable("@$postedAt");
        $this->rating = $rating;
    }

    public function getReviewAuthor(): ReviewAuthor
    {
        return $this->reviewAuthor;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getPostedAt(): \DateTimeImmutable
    {
        return $this->postedAt;
    }

    public function getRating(): int
    {
        return $this->rating;
    }
}
