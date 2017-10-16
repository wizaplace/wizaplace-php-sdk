<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types=1);


namespace Wizaplace\SDK\Basket;

use Wizaplace\SDK\Exception\SomeParametersAreInvalid;

class ShippingGroupComment extends Comment
{
    /**
     * @var int
     */
    private $shippingGroupId;

    public function __construct(int $shippingGroupId, string $comment)
    {
        parent::__construct($comment);

        $this->shippingGroupId = $shippingGroupId;
    }

    public function getShippingGroupId(): int
    {
        return $this->shippingGroupId;
    }

    public function toArray(): array
    {
        return [
            'shippingGroupId' => $this->shippingGroupId,
            'comment' => $this->comment,
        ];
    }
}
