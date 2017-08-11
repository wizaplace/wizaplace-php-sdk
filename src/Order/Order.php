<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\Order;

class Order
{
    /** @var int */
    private $id;
    /** @var int */
    private $companyId;
    /** @var float */
    private $total;
    /** @var float */
    private $subtotal;
    /** @var \DateTimeImmutable */
    private $timestamp;
    /** @var string */
    private $status;
    /** @var string */
    private $shippingName;
    /** @var ShippingAddress */
    private $shippingAddress;
    /** @var OrderItem[] */
    private $orderItems;

    /**
     * @internal
     */
    public function __construct(array $data)
    {
        $this->id = $data['id'];
        $this->companyId = $data['companyId'];
        $this->total = $data['total'];
        $this->subtotal = $data['subtotal'];
        $this->timestamp = new \DateTimeImmutable('@'.$data['timestamp']);
        $this->status = $data['status'];
        $this->shippingName = $data['shippingName'];
        $this->shippingAddress = new ShippingAddress($data['shippingAddress']);
        $this->orderItems = array_map(function ($orderItemData) {
            return new OrderItem($orderItemData);
        }, $data['items']);
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getCompanyId(): int
    {
        return $this->companyId;
    }

    public function getTotal(): float
    {
        return $this->total;
    }

    public function getSubtotal(): float
    {
        return $this->subtotal;
    }

    public function getTimestamp(): \DateTimeImmutable
    {
        return $this->timestamp;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getShippingName(): string
    {
        return $this->shippingName;
    }

    public function getShippingAddress(): ShippingAddress
    {
        return $this->shippingAddress;
    }

    /**
     * @return OrderItem[]
     */
    public function getOrderItems(): array
    {
        return $this->orderItems;
    }
}
