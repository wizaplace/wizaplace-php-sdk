<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\SDK\Order;

/**
 * Class OrderReturn
 * @package Wizaplace\SDK\Order
 */
final class OrderReturn
{
    /** @var int */
    private $id;
    /** @var int */
    private $orderId;
    /** @var int */
    private $userId;
    /** @var \DateTimeImmutable */
    private $createdAt;
    /** @var string */
    private $comments;
    /** @var OrderReturnStatus */
    private $status;
    /** @var ReturnItem[] */
    private $items;

    /**
     * @internal
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->id = $data['id'];
        $this->orderId = $data['orderId'];
        $this->userId = $data['userId'];
        $this->createdAt = \DateTimeImmutable::createFromFormat(\DateTime::RFC3339, $data['createdAt']);
        $this->comments = $data['comments'];
        $this->status = new OrderReturnStatus($data['status']);
        $this->items = array_map(static function (array $item) : ReturnItem {
            $item['productName'] = $item['product'];
            unset($item['product']);

            return new ReturnItem($item);
        }, $data['items']);
    }

    /**
     * @return int
     */
    public function getOrderId(): int
    {
        return $this->orderId;
    }

    /**
     * @return int
     */
    public function getUserId(): int
    {
        return $this->userId;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * @return string
     */
    public function getComments(): string
    {
        return $this->comments;
    }

    /**
     * @return OrderReturnStatus
     */
    public function getStatus(): OrderReturnStatus
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
