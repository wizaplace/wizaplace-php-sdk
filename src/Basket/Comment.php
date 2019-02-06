<?php
/**
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\SDK\Basket;

/**
 * Class Comment
 * @package Wizaplace\SDK\Basket
 */
abstract class Comment
{
    /** @var string */
    protected $comment;

    /**
     * @internal
     *
     * @param string $comment
     */
    public function __construct(string $comment)
    {
        $this->comment = $comment;
    }

    /**
     * @return string
     */
    public function getComment(): string
    {
        return $this->comment;
    }

    /**
     * @internal
     */
    abstract public function toArray(): array;
}
