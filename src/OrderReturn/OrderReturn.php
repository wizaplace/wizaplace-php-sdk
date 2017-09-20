<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\SDK\OrderReturn;

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
    /** @var string */
    private $status;
    /** @var ReturnItem[] */
    private $items;

    /**
     * @internal
     */
    public function __construct(array $data)
    {
        $this->id = $data['id'];
        $this->orderId = $data['orderId'];
        $this->userId = $data['userId'];
        $this->createdAt = \DateTimeImmutable::createFromFormat(\DateTime::RFC3339, $data['createdAt']);
        $this->comments = $data['comments'];
        $this->status = $data['status'];
        $this->items = array_map(static function (array $item) : ReturnItem {
            $item['productName'] = $item['product'];
            unset($item['product']);

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

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
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
