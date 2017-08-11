<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\Order;

/**
 * @see \Wizaplace\Order\OrderService::createOrderReturn
 */
final class CreateOrderReturn
{
    /** @var int */
    private $orderId;
    /** @var string */
    private $comments;
    /** @var array */
    private $items;

    /**
     * @param int $orderId ID of the order to return.
     * @param string $comments Buyer's comments on why they are returning the order.
     */
    public function __construct(int $orderId, string $comments)
    {
        $this->orderId = $orderId;
        $this->comments = $comments;
    }

    /**
     * @param string $declinationId The ID of a product declination which is part of the order.
     * @param int $reason The rason ID. {@see \Wizaplace\Order\OrderService::getReturnReasons}
     * @param int $amount The quantity of items to be returned.
     */
    public function addItem(string $declinationId, int $reason, int $amount): void
    {
        $this->items[] = [
            'declinationId' => $declinationId,
            'reason' => $reason,
            'amount' => $amount,
        ];
    }

    public function getOrderId(): int
    {
        return $this->orderId;
    }

    public function getComments(): string
    {
        return $this->comments;
    }

    /**
     * @see \Wizaplace\Order\CreateOrderReturn::addItem()
     */
    public function getItems(): array
    {
        return $this->items;
    }
}
