<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types=1);


namespace Wizaplace\SDK\Basket;

use Wizaplace\SDK\Exception\SomeParametersAreInvalid;

class Comment
{
    /**
     * @var string
     */
    private $declinationId;

    /**
     * @var string
     */
    private $comment;

    public function __construct(string $declinationId, string $comment)
    {
        if ($this->declinationId === '') {
            throw new SomeParametersAreInvalid('Missing declination Id');
        }
        if ($this->comment === '') {
            throw new SomeParametersAreInvalid('Missing comment');
        }

        $this->declinationId = $declinationId;
        $this->comment = $comment;
    }

    public function getDeclinationId(): string
    {
        return $this->declinationId;
    }

    public function getComment(): string
    {
        return $this->comment;
    }
}
