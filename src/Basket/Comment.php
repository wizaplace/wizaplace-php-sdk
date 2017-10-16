<?php
/**
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types = 1);


namespace Wizaplace\SDK\Basket;

use Wizaplace\SDK\Exception\SomeParametersAreInvalid;

abstract class Comment
{
    /** @var string */
    protected $comment;

    public function __construct(string $comment)
    {
        if ($comment === '') {
            throw new SomeParametersAreInvalid('Missing comment');
        }
        $this->comment = $comment;
    }

    public function getComment(): string
    {
        return $this->comment;
    }

    abstract public function toArray(): array;
}
