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
     * @var null|int
     */
    private $authorId;

    /**
     * @var string
     */
    private $authorName;

    /**
     * @var null|string
     */
    private $authorEmail;

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
        ?int $authorId,
        string $authorName,
        ?string $authorEmail,
        string $message,
        int $rating,
        string $postedAt
    ) {
        $this->authorId = $authorId;
        $this->authorName = $authorName;
        $this->authorEmail = $authorEmail;
        $this->message = $message;
        $this->rating = $rating;
        $this->postedAt = new \DateTimeImmutable("@$postedAt");
    }

    public function getAuthorId(): ?int
    {
        return $this->authorId;
    }

    public function getAuthorName(): string
    {
        return $this->authorName;
    }

    public function getAuthorEmail(): ?string
    {
        return $this->authorEmail;
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
