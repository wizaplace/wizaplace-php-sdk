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
    private $content;

    private $author;

    private $date;

    public function __construct(array $data)
    {
        $this->author = $data['author'];
        $this->content = $data['content'];
        $this->date = $data['date'];
    }
}
