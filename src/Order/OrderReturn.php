<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\Order;

class OrderReturn
{
    /** @var  int */
    private $id;
    /** @var  int */
    private $orderId;
    /** @var  int */
    private $userId;
    /** @var  \DateTime */
    private $timestamp;
    /** @var  string */
    private $comments;
    /** @var  string */
    private $status;
    /** @var ReturnItem[] */
    private $items;

    public function __construct(array $data)
    {
        $this->id = $data['id'];
        $this->orderId = $data['orderId'];
        $this->userId = $data['userId'];
        $this->timestamp = new \DateTime($data['timestamp']);
        $this->comments = $data['comments'];
        $this->status = $data['status'];
        $this->items = array_map(function ($item) {
            return new ReturnItem($item);
        }, $data['items']);
    }

    public function getOrderId(): int
    {
        return $this->orderId;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getTimestamp(): \DateTime
    {
        return $this->timestamp;
    }

    public function getComments(): string
    {
        return $this->comments;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @return ReturnItem[]
     */
    public function getItems(): array
    {
        return $this->items;
    }
}
