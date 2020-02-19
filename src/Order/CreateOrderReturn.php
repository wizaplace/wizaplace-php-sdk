<?php

/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Order;

use Wizaplace\SDK\Catalog\DeclinationId;

use function theodorejb\polycast\to_string;

/**
 * Class CreateOrderReturn
 * @package Wizaplace\SDK\Order
 *
 * @see \Wizaplace\SDK\Order\OrderService::createOrderReturn
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
     * @param DeclinationId $declinationId The ID of a product declination which is part of the order.
     * @param int $reason The rason ID. {@see \Wizaplace\SDK\Order\OrderService::getReturnReasons}
     * @param int $amount The quantity of items to be returned.
     */
    public function addItem(DeclinationId $declinationId, int $reason, int $amount): void
    {
        $this->items[] = [
            'declinationId' => to_string($declinationId),
            'reason' => $reason,
            'amount' => $amount,
        ];
    }

    /**
     * @return int
     */
    public function getOrderId(): int
    {
        return $this->orderId;
    }

    /**
     * @return string
     */
    public function getComments(): string
    {
        return $this->comments;
    }

    /**
     * @see \Wizaplace\SDK\Order\CreateOrderReturn::addItem()
     */
    public function getItems(): array
    {
        return $this->items;
    }
}
