<?php
/**
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types = 1);


namespace Wizaplace\SDK\Basket;

/**
 * @internal
 */
abstract class Comment
{
    /** @var string */
    protected $comment;

    public function __construct(string $comment)
    {
        $this->comment = $comment;
    }

    public function getComment(): string
    {
        return $this->comment;
    }

    /**
     * @internal
     */
    abstract public function toArray(): array;
}
