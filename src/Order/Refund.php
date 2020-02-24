<?php

/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Order;

use Wizaplace\SDK\Price;

/**
 * Class OrderReturn
 * @package Wizaplace\SDK\Order
 */
final class Refund
{
    /** @var int */
    private $refundId;

    /** @var int */
    private $orderId;

    /** @var bool */
    private $isPartial;

    /** @var bool */
    private $hasShipping;

    /** @var float */
    private $amount;

    /** @var float */
    private $shippingAmount;

    /** @var RefundStatus */
    private $status;

    /** @var string|null */
    private $message;

    /** @var \DateTimeInterface */
    private $createdAt;

    /** @var \DateTimeInterface */
    private $updatedAt;

    /** @var RefundItem[] */
    private $items;

    /** @var Price */
    private $totalItemsPrice;

    /** @var Price */
    private $totalShippingPrice;

    /** @var Price */
    private $totalGlobalPrice;

    /** @var mixed[] */
    private $totalTaxes;

    /**
     * @internal
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->refundId = $data['refundId'];
        $this->orderId = $data['orderId'];
        $this->isPartial = $data['isPartial'] === true;
        $this->hasShipping = $data['hasShipping'] === true;
        $this->amount = $data['amount'];
        $this->shippingAmount = $data['shippingAmount'];
        $this->status = new RefundStatus($data['status']);
        $this->message = $data['message'];
        $this->createdAt = new \DateTimeImmutable($data['createdAt']);
        $this->updatedAt = new \DateTimeImmutable($data['updatedAt']);
        $this->items = array_map(function (array $item): RefundItem {
            return new RefundItem($item);
        }, $data['items']);
        $this->totalItemsPrice = new Price($data['totalItemsPrice']);
        $this->totalShippingPrice = new Price($data['totalShippingPrice']);
        $this->totalGlobalPrice = new Price($data['totalGlobalPrice']);
        $this->totalTaxes = $data['totalTaxes'];
    }

    public function getRefundId(): int
    {
        return $this->refundId;
    }

    public function getOrderId(): int
    {
        return $this->orderId;
    }

    public function isPartial(): bool
    {
        return $this->isPartial;
    }

    public function hasShipping(): bool
    {
        return $this->hasShipping;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function getShippingAmount(): float
    {
        return $this->shippingAmount;
    }

    public function getStatus(): RefundStatus
    {
        return $this->status;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): \DateTimeInterface
    {
        return $this->updatedAt;
    }

    /** @return RefundItem[] */
    public function getItems(): array
    {
        return $this->items;
    }

    public function getTotalItemsPrice(): Price
    {
        return $this->totalItemsPrice;
    }

    public function getTotalShippingPrice(): Price
    {
        return $this->totalShippingPrice;
    }

    public function getTotalGlobalPrice(): Price
    {
        return $this->totalGlobalPrice;
    }

    /** @return mixed[] */
    public function getTotalTaxes(): array
    {
        return $this->totalTaxes;
    }
}
