<?php

/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Vendor\Order;

class OrderAction
{
    /** @var int */
    private $id;

    /** @var int */
    private $orderId;

    /** @var OrderStatus */
    private $status;

    /** @var string */
    private $description;

    /** @var int|null */
    protected $userId;

    /** @var string|null */
    protected $userName;

    /** @var \DateTimeImmutable */
    protected $date;

    public function __construct($data)
    {
        $this->id = $data['id'];
        $this->orderId = $data['orderId'];
        $this->status = new OrderStatus($data['status']);
        $this->description = $data['description'];
        $this->userId = $data['userId'];
        $this->userName = $data['userName'];
        $this->date = new \DateTimeImmutable($data['date']);
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getOrderId(): int
    {
        return $this->orderId;
    }

    public function getStatus(): OrderStatus
    {
        return $this->status;
    }

    public function getUserId(): ?int
    {
        return $this->userId;
    }

    public function getUserName(): ?string
    {
        return $this->userName;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getDate(): \DateTimeImmutable
    {
        return $this->date;
    }
}
