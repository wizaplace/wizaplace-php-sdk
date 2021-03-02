<?php

/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */

declare(strict_types=1);

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
    /** @var float */
    private $shippingCost;
    /** @var float */
    private $discount;
    /** @var float */
    private $marketplaceDiscountTotal;
    /** @var float */
    private $customerTotal;
    /** @var null|string */
    private $subscriptionId;
    /** @var bool */
    private $isSubscriptionInitiator;
    /** @var null|bool */
    private $isPaid;
    /** @var bool */
    private $carriagePaid;
    /** @var null|bool */
    private $refunded;
    /** @var float */
    private $balance;
    /** @var string|null */
    private $basketId;
    /** @var string[] */
    protected $extra;

    /** @var AttachmentsOrder[] */
    private $orderAttachments;

    /** @var null|string */
    private $bankWireTransactionReference;
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
        $this->timestamp = new \DateTimeImmutable('@' . $data['timestamp']);
        $this->status = new OrderStatus($data['status']);
        $this->shippingName = $data['shippingName'];
        $this->shippingAddress = new ShippingAddress($data['shippingAddress']);
        $this->billingAddress = new BillingAddress($data['billingAddress']);
        $this->orderItems = array_map(
            static function (array $orderItemData): OrderItem {
                return new OrderItem($orderItemData);
            },
            $data['items']
        );
        $this->customerComment = $data['customerComment'];
        $this->payment = new Payment($data['payment']);
        $this->shippingCost = $data['shippingCost'] ?? 0;
        $this->discount = $data['discount'] ?? 0;
        $this->marketplaceDiscountTotal = $data['marketplaceDiscountTotal'] ?? 0.0;
        $this->customerTotal = $data['customerTotal'] ?? $data['total'];
        $this->subscriptionId = $data['subscriptionId'] ?? null;
        $this->isSubscriptionInitiator = $data['isSubscriptionInitiator'] ?? false;
        $this->isPaid = \array_key_exists('isPaid', $data) ? (bool) $data['isPaid'] : null;
        $this->carriagePaid = $data['carriagePaid'] ?? false;
        $this->refunded = \array_key_exists('refunded', $data) ? (bool) $data['refunded'] : null;
        $this->balance = (\array_key_exists('balance', $data) === true) ? $data['balance'] : 0;
        $this->basketId = (\array_key_exists('basketId', $data) === true) ? $data['basketId'] : null;
        $this->extra = (\array_key_exists('extra', $data) === true) ? $data['extra'] : [];
        $this->orderAttachments = \array_map(
            static function (array $orderAttachment): AttachmentsOrder {
                return new AttachmentsOrder($orderAttachment);
            },
            $data['attachments'] ?? []
        );
        $this->bankWireTransactionReference = $data['transaction_reference'] ?? null;
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

    /**
     * @return float
     */
    public function getShippingCost(): float
    {
        return $this->shippingCost;
    }

    /**
     * @return float
     */
    public function getDiscount(): float
    {
        return $this->discount;
    }

    public function getMarketplaceDiscountTotal(): float
    {
        return $this->marketplaceDiscountTotal;
    }

    public function getCustomerTotal(): float
    {
        return $this->customerTotal;
    }

    /**
     * @return null|string
     */
    public function getSubscriptionId(): ?string
    {
        return $this->subscriptionId;
    }

    /**
     * @return bool
     */
    public function isSubscriptionInitiator(): bool
    {
        return $this->isSubscriptionInitiator;
    }

    public function isPaid(): ?bool
    {
        return $this->isPaid;
    }

    public function isCarriagePaid(): bool
    {
        return $this->carriagePaid;
    }

    /** @return bool|null  */
    public function isRefunded(): ?bool
    {
        return $this->refunded;
    }

    /** @return float */
    public function getBalance(): float
    {
        return $this->balance;
    }

    public function getBasketId(): ?string
    {
        return $this->basketId;
    }

    /** @return string[] */
    public function getExtra(): array
    {
        return $this->extra;
    }

    /** @return AttachmentsOrder[] */
    public function getOrderAttachments(): array
    {
        return $this->orderAttachments;
    }

    /** @return null|string */
    public function getBankWireTransactionReference(): ?string
    {
        return $this->bankWireTransactionReference;
    }
}
