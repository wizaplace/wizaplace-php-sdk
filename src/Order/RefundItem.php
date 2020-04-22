<?php

/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Order;

use Wizaplace\SDK\Price;

final class RefundItem
{
    /** @var int */
    private $itemId;

    /** @var int */
    private $quantity;

    /** @var Price */
    private $unitPrice;

    /** @var Price */
    private $totalPrice;

    /** @param mixed[] $data */
    public function __construct(array $data)
    {
        $this->itemId = $data['itemId'];
        $this->quantity = $data['quantity'];
        $this->unitPrice = new Price($data['unitPrice']);
        $this->totalPrice = new Price($data['totalPrice']);
    }

    public function getItemId(): int
    {
        return $this->itemId;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function getUnitPrice(): Price
    {
        return $this->unitPrice;
    }

    public function getTotalPrice(): Price
    {
        return $this->totalPrice;
    }
}
