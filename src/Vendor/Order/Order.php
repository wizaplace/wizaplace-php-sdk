<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types=1);

namespace Wizaplace\SDK\Vendor\Order;

use Wizaplace\SDK\Shipping\Shipping;
use function theodorejb\polycast\to_int;

/**
 * Class Order
 * @package Wizaplace\SDK\Vendor\Order
 */
final class Order
{
    /** @var int */
    private $orderId;

    /** @var int */
    private $customerUserId;

    /** @var string */
    private $customerEmail;

    /** @var string */
    private $customerFirstName;

    /** @var string */
    private $customerLastName;

    /** @var float */
    private $discountAmount;

    /** @var string */
    private $invoiceNumber;

    /** @var string */
    private $declineReason;

    /** @var bool */
    private $needsShipping;

    /** @var int[] */
    private $shipmentsIds;

    /** @var float */
    private $shippingCost;

    /** @var string */
    private $notes;

    /** @var string */
    private $details;

    /** @var float */
    private $taxSubtotal;

    /** @var OrderTax[] */
    private $taxes;

    /** @var float */
    private $total;

    /** @var OrderStatus */
    private $status;

    /** @var \DateTimeImmutable */
    private $createdAt;

    /** @var OrderAddress */
    private $billingAddress;

    /** @var OrderAddress */
    private $shippingAddress;

    /** @var OrderItem[] */
    private $items;

    /** @var string */
    private $comment;

    /** @var int */
    private $companyId;

    /** @var null|\DateTimeImmutable */
    private $lastStatusChange;

    /** @var AmountsTaxesDetails */
    private $amountsTaxesDetails;

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

    /** @var Shipping[]|null */
    private $shipping;

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
        $this->companyId = $data['company_id'];
        $this->customerUserId = $data['user_id'];
        $this->customerEmail = $data['email'];
        $this->customerFirstName = $data['customer_firstname'];
        $this->customerLastName = $data['customer_lastname'];
        $this->discountAmount = $data['discount'];
        $this->invoiceNumber = $data['invoice_number'];
        $this->declineReason = $data['decline_reason'] ?? '';
        $this->needsShipping = $data['need_shipping'];
        $this->shipmentsIds = array_map(static function ($v): int {
            return to_int($v);
        }, $data['shipment_ids']);
        $this->shippingCost = $data['shipping_cost'];
        $this->notes = $data['notes'];
        $this->details = $data['details'] ?? null;
        $this->taxSubtotal = $data['tax_subtotal'];
        $this->total = $data['total'];
        $this->status = new OrderStatus($data['status']);
        $this->createdAt = new \DateTimeImmutable('@'.$data['timestamp']);
        $this->billingAddress = OrderAddress::extractBillingAddressData($data);
        $this->shippingAddress = OrderAddress::extractShippingAddressData($data);
        $this->taxes = array_map(function (array $itemData): OrderTax {
            return new OrderTax($itemData);
        }, $data['taxes']);
        $this->items = array_map(function (array $itemData): OrderItem {
            return new OrderItem($itemData);
        }, $data['products']);
        $this->comment = $data['notes'] ?? '';
        $this->lastStatusChange = static::denormalizeLastStatusChange($data['last_status_change'] ?? null);
        $this->billingAddress = OrderAddress::extractBillingAddressData($data);
        $this->amountsTaxesDetails = static::denormalizeAmountsTaxesDetails($data);
        $this->marketplaceDiscountTotal = $data['marketplace_discount_total'] ?? 0.0;
        $this->customerTotal = $data['customer_total'] ?? $data['total'];
        $this->subscriptionId = $data['subscription_id'] ?? null;
        $this->isSubscriptionInitiator = $data['is_subscription_initiator'] ?? false;
        $this->isPaid = \array_key_exists('is_paid', $data) ? (bool) $data['is_paid'] : null;
        $this->shipping = \array_key_exists('shipping', $data) ? array_map(function (array $itemData): Shipping {
            return new Shipping($itemData);
        }, $data['shipping']) : null;
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
    public function getCustomerUserId(): int
    {
        return $this->customerUserId;
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
     * @return float
     */
    public function getDiscountAmount(): float
    {
        return $this->discountAmount;
    }

    /**
     * @return string
     */
    public function getInvoiceNumber(): string
    {
        return $this->invoiceNumber;
    }

    /**
     * @return string
     */
    public function getDeclineReason(): string
    {
        return $this->declineReason;
    }

    /**
     * @return bool
     */
    public function needsShipping(): bool
    {
        return $this->needsShipping;
    }

    /**
     * @return int[]
     */
    public function getShipmentsIds(): array
    {
        return $this->shipmentsIds;
    }

    /**
     * @return float
     */
    public function getShippingCost(): float
    {
        return $this->shippingCost;
    }

    /**
     * @return string
     */
    public function getNotes(): string
    {
        return $this->notes;
    }

    /**
     * @return null|string
     */
    public function getDetails(): ?string
    {
        return $this->details;
    }

    /**
     * @return float
     */
    public function getTaxSubtotal(): float
    {
        return $this->taxSubtotal;
    }

    /**
     * @return float
     */
    public function getTotal(): float
    {
        return $this->total;
    }

    /**
     * @return \DateTimeInterface
     */
    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    /**
     * @return OrderStatus
     */
    public function getStatus(): OrderStatus
    {
        return $this->status;
    }

    /**
     * @return OrderAddress
     */
    public function getBillingAddress(): OrderAddress
    {
        return $this->billingAddress;
    }

    /**
     * @return OrderAddress
     */
    public function getShippingAddress(): OrderAddress
    {
        return $this->shippingAddress;
    }

    /**
     * @return OrderTax[]
     */
    public function getTaxes(): array
    {
        return $this->taxes;
    }

    /**
     * @return OrderItem[]
     */
    public function getItems(): array
    {
        return $this->items;
    }

    /**
     * @return string
     */
    public function getComment(): string
    {
        return $this->comment;
    }

    /**
     * @return \DateTimeImmutable|null
     */
    public function getLastStatusChange(): ?\DateTimeImmutable
    {
        return $this->lastStatusChange;
    }

    /**
     * @param string|null $value
     * @return \DateTimeImmutable|null
     */
    public static function denormalizeLastStatusChange(?string $value): ?\DateTimeImmutable
    {
        if (is_null($value) === true || trim($value) === "") {
            return null;
        }

        try {
            return new \DateTimeImmutable($value);
        } catch (\Throwable $e) {
            throw new \Exception("lastStatusChange property should be DateTime RFC3339 format, '$value' found");
        }
    }

    public static function denormalizeAmountsTaxesDetails(array $data): AmountsTaxesDetails
    {
        $amountsTaxesDetails = new AmountsTaxesDetails();
        foreach (AmountsTaxesDetails::getKeys() as $key) {
            if (array_key_exists($key, $data)) {
                if (array_key_exists('excluding_taxes', $data[$key]) === false
                    || array_key_exists('taxes', $data[$key]) === false
                    || array_key_exists('including_taxes', $data[$key])  === false
                ) {
                    throw new \Exception("Bad format for '$key' property");
                }

                $amountsTaxesDetails->add(new AmountTaxesDetail(
                    $key,
                    $data[$key]['excluding_taxes'],
                    $data[$key]['taxes'],
                    $data[$key]['including_taxes']
                ));
            }
        }

        return $amountsTaxesDetails;
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

    public function getMarketplaceDiscountTotal(): float
    {
        return $this->marketplaceDiscountTotal;
    }

    public function getCustomerTotal(): float
    {
        return $this->customerTotal;
    }

    public function getSubscriptionId(): ?string
    {
        return $this->subscriptionId;
    }

    public function isSubscriptionInitiator(): bool
    {
        return $this->isSubscriptionInitiator;
    }

    public function isPaid(): ?bool
    {
        return $this->isPaid;
    }

    /**
     * @return Shipping[]|null
     */
    public function getShipping(): ?array
    {
        return $this->shipping;
    }
}
