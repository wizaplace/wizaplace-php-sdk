<?php

/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Order;

use Wizaplace\SDK\Exception\SomeParametersAreInvalid;

/**
 * Class AfterSalesServiceRequest
 * @package Wizaplace\SDK\Order
 */
final class AfterSalesServiceRequest
{
    /** @var int */
    private $orderId;

    /** @var string */
    private $comments;

    /** @var string[] */
    private $itemsDeclinationsIds;

    /**
     * @return int
     */
    public function getOrderId(): int
    {
        return $this->orderId;
    }

    /**
     * @param int $orderId
     *
     * @return AfterSalesServiceRequest
     */
    public function setOrderId(int $orderId): self
    {
        $this->orderId = $orderId;

        return $this;
    }

    /**
     * @return string
     */
    public function getComments(): string
    {
        return $this->comments;
    }

    /**
     * @param string $comments
     *
     * @return AfterSalesServiceRequest
     */
    public function setComments(string $comments): self
    {
        $this->comments = $comments;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getItemsDeclinationsIds(): array
    {
        return $this->itemsDeclinationsIds;
    }

    /**
     * @param string[] $itemsDeclinationsIds
     *
     * @return AfterSalesServiceRequest
     */
    public function setItemsDeclinationsIds(array $itemsDeclinationsIds): self
    {
        $this->itemsDeclinationsIds = $itemsDeclinationsIds;

        return $this;
    }

    /**
     * @throws SomeParametersAreInvalid
     */
    public function validate(): void
    {
        if (!isset($this->orderId)) {
            throw new SomeParametersAreInvalid('Missing order ID');
        }

        if (!isset($this->comments)) {
            throw new SomeParametersAreInvalid('Missing comments');
        }

        if (!isset($this->itemsDeclinationsIds)) {
            throw new SomeParametersAreInvalid('Missing items\'  declinations ids');
        }
    }
}
