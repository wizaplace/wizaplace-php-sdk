<?php

/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Vendor\Order;

use Wizaplace\SDK\Vendor\Order\Order as VendorOrder;

/**
 * Class OrderSummary
 * @package Wizaplace\SDK\Vendor\Order
 */
final class OrderSummary
{
    /** @var string */
    private $customerEmail;

    /** @var string */
    private $customerFirstName;

    /** @var string */
    private $customerLastName;

    /** @var int */
    private $orderId;

    /** @var OrderStatus */
    private $status;

    /** @var \DateTimeImmutable */
    private $createdAt;

    /** @var int */
    private $customerUserId;

    /** @var int */
    private $basketId;

    /** @var float */
    private $total;

    /** @var int  */
    private $companyId;

    /** @var string|null  */
    private $companyName;

    /** @var \DateTimeImmutable|null */
    private $lastStatusChange;

    /** @var AmountsTaxesDetails */
    private $amountsTaxesDetails;

    /** @var null|string */
    private $subscriptionId;

    /** @var null|bool */
    private $isPaid;

    /** @var null|bool */
    private $refunded;

    /** @var float */
    private $balance;

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
        $this->customerEmail = $data['email'];
        $this->customerFirstName = $data['customer_firstname'];
        $this->customerLastName = $data['customer_lastname'];
        $this->orderId = $data['order_id'];
        $this->companyId = $data['company_id'];
        $this->companyName = (\array_key_exists('company_name', $data) === true) ? $data['company_name'] : null;
        $this->status = new OrderStatus($data['status']);
        $this->createdAt = new \DateTimeImmutable('@' . $data['timestamp']);
        $this->customerUserId = $data['user_id'];
        $this->basketId = $data['basket_id'];
        $this->total = $data['total'];
        $this->lastStatusChange = VendorOrder::denormalizeLastStatusChange($data['last_status_change'] ?? null);
        $this->amountsTaxesDetails = Order::denormalizeAmountsTaxesDetails($data);
        $this->subscriptionId = $data['subscription_id'] ?? null;
        $this->isPaid = \array_key_exists('is_paid', $data) ? (bool) $data['is_paid'] : null;
        $this->refunded = \array_key_exists('refunded', $data) ? (bool) $data['refunded'] : null;
        $this->balance = (\array_key_exists('balance', $data) === true) ? $data['balance'] : 0;
        $this->bankWireTransactionReference = $data['transaction_reference'] ?? null;
    }

    /**
     * @return string
     */
    public function getCustomerEmail(): string
    {
        return $this->customerEmail;
    }

    /**
     * @return string
     */
    public function getCustomerFirstName(): string
    {
        return $this->customerFirstName;
    }

    /**
     * @return string
     */
    public function getCustomerLastName(): string
    {
        return $this->customerLastName;
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
    public function getCompanyId(): int
    {
        return $this->companyId;
    }

    /*** @return string */
    public function getCompanyName(): ?string
    {
        return $this->companyName;
    }

    /**
     * @return OrderStatus
     */
    public function getStatus(): OrderStatus
    {
        return $this->status;
    }

    /**
     * @return \DateTimeInterface
     */
    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    /**
     * @return int
     */
    public function getCustomerUserId(): int
    {
        return $this->customerUserId;
    }

    /**
     * @return int
     */
    public function getBasketId(): int
    {
        return $this->basketId;
    }

    /**
     * @return float
     */
    public function getTotal(): float
    {
        return $this->total;
    }

    /**
     * @return \DateTimeImmutable|null
     */
    public function getLastStatusChange(): ?\DateTimeImmutable
    {
        return $this->lastStatusChange;
    }

    public function getAmountsTaxesDetails(): AmountsTaxesDetails
    {
        return $this->amountsTaxesDetails;
    }

    public function getTotalsTaxesDetail(): ?AmountTaxesDetail
    {
        return $this->amountsTaxesDetails->get(AmountsTaxesDetails::TOTALS);
    }

    public function getShippingCostsTaxesDetail(): ?AmountTaxesDetail
    {
        return $this->amountsTaxesDetails->get(AmountsTaxesDetails::SHIPPING_COSTS);
    }

    public function getCommissionsTaxesDetail(): ?AmountTaxesDetail
    {
        return $this->amountsTaxesDetails->get(AmountsTaxesDetails::COMMISSIONS);
    }

    public function getVendorShareTaxesDetail(): ?AmountTaxesDetail
    {
        return $this->amountsTaxesDetails->get(AmountsTaxesDetails::VENDOR_SHARE);
    }

    public function getSubscriptionId(): ?string
    {
        return $this->subscriptionId;
    }

    public function isPaid(): ?bool
    {
        return $this->isPaid;
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

    /** @return null|string */
    public function getBankWireTransactionReference(): ?string
    {
        return $this->bankWireTransactionReference;
    }
}
