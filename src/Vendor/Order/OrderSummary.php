<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types=1);

namespace Wizaplace\SDK\Vendor\Order;

final class OrderSummary
{
    /** @var string */
    private $customerEmail;

    /** @var int */
    private $orderId;

    /** @var OrderStatus */
    private $status;

    /** @var \DateTimeImmutable */
    private $createdAt;

    /** @var int */
    private $customerUserId;

    /**
     * @internal
     */
    public function __construct(array $data)
    {
        $this->customerEmail = $data['email'];
        $this->orderId = $data['order_id'];
        $this->status = new OrderStatus($data['status']);
        $this->createdAt = new \DateTimeImmutable('@'.$data['timestamp']);
        $this->customerUserId = $data['user_id'];
    }

    public function getCustomerEmail(): string
    {
        return $this->customerEmail;
    }

    public function getOrderId(): int
    {
        return $this->orderId;
    }

    public function getStatus(): OrderStatus
    {
        return $this->status;
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    public function getCustomerUserId(): int
    {
        return $this->customerUserId;
    }
}
