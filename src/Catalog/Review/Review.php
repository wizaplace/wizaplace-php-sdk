<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\SDK\Catalog\Review;

/**
 * Class Review
 * @package Wizaplace\SDK\Catalog\Review
 */
final class Review
{
    /**
     * @var Author
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

    /**
     * Review constructor.
     *
     * @param Author $author
     * @param string $message
     * @param string $postedAt
     * @param int    $rating
     */
    public function __construct(
        Author $author,
        string $message,
        string $postedAt,
        int $rating
    ) {
        $this->author = $author;
        $this->message = $message;
        $this->postedAt = \DateTimeImmutable::createFromFormat(\DateTime::RFC3339, $postedAt);
        $this->rating = $rating;
    }

    /**
     * @return Author
     */
    public function getAuthor(): Author
    {
        return $this->author;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getPostedAt(): \DateTimeImmutable
    {
        return $this->postedAt;
    }

    /**
     * @return int
     */
    public function getRating(): int
    {
        return $this->rating;
    }
}
