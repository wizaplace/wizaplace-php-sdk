<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\Review;

class CompanyReview
{
    /**
     * @var int
     */
    private $threadId;

    /**
     * @var int
     */
    private $userId;

    /**
     * @var string
     */
    private $userName;

    /**
     * @var string
     */
    private $userEmail;

    /**
     * @var string
     */
    private $message;

    /**
     * @var int
     */
    private $rating;

    /**
     * @var string
     */
    private $status;

    /**
     * @var int
     */
    private $createdAt;

    public function __construct(
        int $threadId,
        int $userId,
        string $userName,
        string $userEmail,
        string $message,
        int $rating,
        string $status,
        string $createdAt
    ) {
        $this->threadId = $threadId;
        $this->userId = $userId;
        $this->userName = $userName;
        $this->userEmail = $userEmail;
        $this->message = $message;
        $this->rating = $rating;
        $this->status = $status;
        $this->createdAt = (int) $createdAt;
    }

    public function getThreadId(): int
    {
        return $this->threadId;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getUserName(): string
    {
        return $this->userName;
    }

    public function getUserEmail(): string
    {
        return $this->userEmail;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getRating(): int
    {
        return $this->rating;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        $datetime = new \DateTimeImmutable;
        return $datetime->setTimestamp($this->createdAt);
    }
}
