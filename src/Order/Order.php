<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\SDK\Order;

/**
 * Class Order
 * @package Wizaplace\SDK\Order
 */
final class Order
{
    /** @var int */
    private $id;
    /** @var int */
    private $companyId;
    /** @var string */
    private $companyName;
    /** @var float */
    private $total;
    /** @var float */
    private $subtotal;
    /** @var float */
    private $taxtotal;
    /** @var \DateTimeImmutable */
    private $timestamp;
    /** @var OrderStatus */
    private $status;
    /** @var string */
    private $shippingName;
    /** @var ShippingAddress */
    private $shippingAddress;
    /** @var BillingAddress */
    private $billingAddress;
    /** @var OrderItem[] */
    private $orderItems;
    /** @var string */
    private $customerComment;
    /** @var Payment */
    private $payment;

    /**
     * @internal
     *
     * @param array $data
     *
     * @throws \Exception
     */
    public function __construct(array $data)
    {
        $this->id = $data['id'];
        $this->companyId = $data['companyId'];
        $this->companyName = $data['companyName'];
        $this->total = $data['total'];
        $this->subtotal = $data['subtotal'];
        $this->taxtotal = $data['taxTotal'];
        $this->timestamp = new \DateTimeImmutable('@'.$data['timestamp']);
        $this->status = new OrderStatus($data['status']);
        $this->shippingName = $data['shippingName'];
        $this->shippingAddress = new ShippingAddress($data['shippingAddress']);
        $this->billingAddress = new BillingAddress($data['billingAddress']);
        $this->orderItems = array_map(static function (array $orderItemData) : OrderItem {
            return new OrderItem($orderItemData);
        }, $data['items']);
        $this->customerComment = $data['customerComment'];
        $this->payment = new Payment($data['payment']);
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getCompanyId(): int
    {
        return $this->companyId;
    }

    /**
     * @return string
     */
    public function getCompanyName(): string
    {
        return $this->companyName;
    }

    /**
     * @return float
     */
    public function getTotal(): float
    {
        return $this->total;
    }

    /**
     * @return float
     */
    public function getSubtotal(): float
    {
        return $this->subtotal;
    }

    /**
     * @return float
     */
    public function getTaxtotal(): float
    {
        return $this->taxtotal;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getTimestamp(): \DateTimeImmutable
    {
        return $this->timestamp;
    }

    /**
     * @return OrderStatus
     */
    public function getStatus(): OrderStatus
    {
        return $this->status;
    }

    /**
     * @return string
     */
    public function getShippingName(): string
    {
        return $this->shippingName;
    }

    /**
     * @return ShippingAddress
     */
    public function getShippingAddress(): ShippingAddress
    {
        return $this->shippingAddress;
    }

    /**
     * @return BillingAddress
     */
    public function getBillingAddress(): BillingAddress
    {
        return $this->billingAddress;
    }

    /**
     * @return OrderItem[]
     */
    public function getOrderItems(): array
    {
        return $this->orderItems;
    }

    /**
     * @return string
     */
    public function getCustomerComment(): string
    {
        return $this->customerComment;
    }

    /**
     * @return Payment
     */
    public function getPayment(): Payment
    {
        return $this->payment;
    }
}
