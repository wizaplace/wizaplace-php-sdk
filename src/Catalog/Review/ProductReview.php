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
     * @var string
     */
    private $author;

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
        string $author,
        string $message,
        string $postedAt,
        int $rating
    ) {
        $this->author = $author;
        $this->message = $message;
        $this->postedAt = new \DateTimeImmutable("@$postedAt");
        $this->rating = $rating;
    }

    public function getAuthor(): string
    {
        return $this->author;
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
