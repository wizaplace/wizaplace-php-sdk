<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types=1);

namespace Wizaplace\SDK\Vendor\Order;

/**
 * Class Shipment
 * @package Wizaplace\SDK\Vendor\Order
 */
final class Shipment
{
    /** @var int */
    private $orderId;

    /** @var int */
    private $shipmentId;

    /** @var int */
    private $shippingId;

    /** @var string */
    private $shippingName;

    /** @var string */
    private $trackingNumber;

    /** @var string */
    private $labelUrl;

    /** @var string */
    private $comment;

    /** @var \DateTimeImmutable */
    private $createdAt;

    /** @var int[] map of (int) itemId to (int) quantity shipped */
    private $shippedQuantityByItemId;

    /** @var int */
    private $position;

    /**
     * @internal
     *
     * @param array $data
     *
     * @throws \Exception
     */
    public function __construct(array $data)
    {
        $this->orderId = $data['order_id'];
        $this->shipmentId = $data['shipment_id'];
        $this->shippingId = $data['shipping_id'];
        $this->shippingName = $data['shipping'];
        $this->trackingNumber = $data['tracking_number'];
        $this->labelUrl = $data['label_url'];
        $this->comment = $data['comments'];
        $this->createdAt = new \DateTimeImmutable('@'.$data['shipment_timestamp']);
        $this->shippedQuantityByItemId = $data['products'];
        $this->position = $data['position'];
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
    public function getShipmentId(): int
    {
        return $this->shipmentId;
    }

    /**
     * @return int
     */
    public function getShippingId(): int
    {
        return $this->shippingId;
    }

    /**
     * @return string
     */
    public function getShippingName(): string
    {
        return $this->shippingName;
    }

    /**
     * @return string
     */
    public function getTrackingNumber(): string
    {
        return $this->trackingNumber;
    }

    /**
     * @return string
     */
    public function getLabelUrl(): string
    {
        return $this->labelUrl;
    }

    /**
     * @return string
     */
    public function getComment(): string
    {
        return $this->comment;
    }

    /**
     * @return \DateTimeInterface
     */
    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    /**
     * @return int[] map of (int) itemId to (int) quantity shipped
     */
    public function getShippedQuantityByItemId(): array
    {
        return $this->shippedQuantityByItemId;
    }

    /**
     * @return int
     */
    public function getPosition(): int
    {
        return $this->position;
    }
}
