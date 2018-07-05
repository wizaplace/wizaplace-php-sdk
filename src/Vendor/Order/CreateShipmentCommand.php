<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types=1);

namespace Wizaplace\SDK\Vendor\Order;

use Wizaplace\SDK\Exception\SomeParametersAreInvalid;

final class CreateShipmentCommand
{
    /** @var int */
    private $orderId;

    /** @var string */
    private $trackingNumber;

    /** @var string */
    private $comment;

    /** @var int[] map of (int) itemId to (int) quantity shipped */
    private $shippedQuantityByItemId;

    public function __construct(int $orderId, string $trackingNumber)
    {
        $this->orderId = $orderId;

        if ($trackingNumber === '') {
            throw new SomeParametersAreInvalid('invoice number should not be empty');
        }
        $this->trackingNumber = $trackingNumber;
    }

    public function setComment(string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * @param int[] $shippedQuantityByItemId map of (int) itemId to (int) quantity shipped
     * @return CreateShipmentCommand
     */
    public function setShippedQuantityByItemId(array $shippedQuantityByItemId): self
    {
        $this->shippedQuantityByItemId = $shippedQuantityByItemId;

        return $this;
    }

    /**
     * @internal
     */
    public function validate(): void
    {
        if (count($this->shippedQuantityByItemId) === 0) {
            throw new SomeParametersAreInvalid('at least 1 order item id is required');
        }
    }

    /**
     * @internal
     */
    public function toArray(): array
    {
        return [
            'order_id' => $this->orderId,
            'tracking_number' => $this->trackingNumber,
            'comments' => $this->comment,
            'products' => $this->shippedQuantityByItemId,
        ];
    }
}
