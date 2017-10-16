<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types=1);


namespace Wizaplace\SDK\Basket;

use Wizaplace\SDK\Exception\SomeParametersAreInvalid;

class BasketComment implements Comment
{
    /**
     * @var string
     */
    private $comment;

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

    public function toArray(): array
    {
        return [
            'comment' => $this->comment,
        ];
    }
}
