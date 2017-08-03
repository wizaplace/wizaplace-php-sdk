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
    /** @var string */
    private $author;

    /** @var string */
    private $content;

    /** @var string */
    private $date;

    public function __construct(array $data)
    {
        $this->author = $data['author'];
        $this->content = $data['content'];
        $this->date = $data['date'];
    }

    public function getAuthor(): string
    {
        return $this->author;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getDate(): string
    {
        return $this->date;
    }
}
