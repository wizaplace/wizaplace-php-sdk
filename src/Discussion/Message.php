<?php

/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Discussion;

/**
 * Class Message
 * @package Wizaplace\SDK\Discussion
 */
final class Message
{
    /** @var string */
    private $content;

    /** @var \DateTimeImmutable */
    private $date;

    /** @var bool */
    private $isAuthor;

    /** @var int */
    private $authorId;

    /**
     * @internal
     *
     * @param array $data
     *
     * @throws \Exception
     */
    public function __construct(array $data)
    {
        $this->content = $data['content'];
        $this->date = new \DateTimeImmutable($data['date']);
        $this->isAuthor = $data['isAuthor'];
        $this->authorId = $data['authorId'];
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getDate(): \DateTimeImmutable
    {
        return $this->date;
    }

    /**
     * @return bool
     */
    public function isAuthor(): bool
    {
        return $this->isAuthor;
    }

    /**
     * @return int
     */
    public function getAuthorId(): int
    {
        return $this->authorId;
    }
}
