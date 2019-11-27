<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types=1);

namespace Wizaplace\SDK\Order;

use Wizaplace\SDK\ArrayableInterface;

final class RefundRequestItem implements ArrayableInterface
{
    /** @var int */
    private $itemId;

    /** @var int */
    private $quantity;

    public function __construct(int $itemId, int $quantity)
    {
        $this->itemId = $itemId;
        $this->quantity = $quantity;
    }

    public function getItemId(): int
    {
        return $this->itemId;
    }

    public function setItemId(int $itemId): self
    {
        $this->itemId = $itemId;

        return $this;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    /** @return mixed[] */
    public function toArray(): array
    {
        return ['itemId' => $this->itemId, 'quantity' => $this->quantity];
    }
}
