<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types=1);


namespace Wizaplace\SDK\Basket;

use Wizaplace\SDK\Exception\SomeParametersAreInvalid;

class ShippingGroupComment implements Comment
{
    /**
     * @var int
     */
    private $shippingGroupId;

    /**
     * @var string
     */
    private $comment;

    public function __construct(int $shippingGroupId, string $comment)
    {
        if ($comment === '') {
            throw new SomeParametersAreInvalid('Missing comment');
        }

        $this->shippingGroupId = $shippingGroupId;
        $this->comment = $comment;
    }

    public function getShippingGroupId(): int
    {
        return $this->shippingGroupId;
    }

    public function getComment(): string
    {
        return $this->comment;
    }

    public function toArray()
    {
        return [
            'shippingGroupId' => $this->shippingGroupId,
            'comment' => $this->comment,
        ];
    }
}
