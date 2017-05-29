<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\Order;

class ReturnItem
{
    /** @var string */
    private $declinationId;
    /** @var string */
    private $product;
    /** @var float */
    private $price;
    /** @var int */
    private $reason;
    /** @var int */
    private $amount;

    public function __construct(array $data)
    {
        $this->declinationId = $data['declinationId'];
        $this->product = $data['product'];
        $this->price = $data['price'];
        $this->reason = $data['reason'];
        $this->amount = $data['amount'];
    }

    public function getProduct(): string
    {
        return $this->product;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function getDeclinationId(): string
    {
        return $this->declinationId;
    }

    public function getReason(): int
    {
        return $this->reason;
    }

    public function getAmount(): int
    {
        return $this->amount;
    }
}
