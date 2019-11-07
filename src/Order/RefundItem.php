<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types=1);

namespace Wizaplace\SDK\Order;

final class RefundItem
{
    /** @var int */
    private $itemId;

    /** @var float */
    private $amount;

    /** @var int */
    private $quantity;

    /** @param mixed[] $data */
    public function __construct(array $data)
    {
        $this->itemId = $data['itemId'];
        $this->amount = $data['amount'];
        $this->quantity = $data['quantity'];
    }

    public function getItemId(): int
    {
        return $this->itemId;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }
}
