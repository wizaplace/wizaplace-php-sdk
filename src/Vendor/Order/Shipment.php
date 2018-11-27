<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types=1);

namespace Wizaplace\SDK\Vendor\Order;

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

    /**
     * @internal
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
    }

    public function getOrderId(): int
    {
        return $this->orderId;
    }

    public function getShipmentId(): int
    {
        return $this->shipmentId;
    }

    public function getShippingId(): int
    {
        return $this->shippingId;
    }

    public function getShippingName(): string
    {
        return $this->shippingName;
    }

    public function getTrackingNumber(): string
    {
        return $this->trackingNumber;
    }

    public function getLabelUrl(): string
    {
        return $this->labelUrl;
    }

    public function getComment(): string
    {
        return $this->comment;
    }

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
}
