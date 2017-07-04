<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\Review;

class Review
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
     * @var integer
     */
    private $postedAt;

    /**
     * @var int
     */
    private $rating;

    public function __construct(
        string $author,
        string $message,
        int $postedAt,
        int $rating
    ) {
        $this->author = $author;
        $this->message = $message;
        $this->postedAt = $postedAt;
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

    public function getPostedAt(): int
    {
        return $this->postedAt;
    }

    public function getRating(): int
    {
        return $this->rating;
    }
}
