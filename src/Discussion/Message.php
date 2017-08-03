<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\Discussion;

class Message
{
    /** @var int */
    private $authorId;

    /** @var string */
    private $content;

    /** @var \DateTimeImmutable */
    private $date;

    public function __construct(array $data)
    {
        $this->authorId = $data['author'];
        $this->content = $data['content'];
        $this->date = new \DateTimeImmutable($data['date']);
    }

    public function getAuthorId(): int
    {
        return $this->authorId;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getDate(): \DateTimeImmutable
    {
        return $this->date;
    }
}
