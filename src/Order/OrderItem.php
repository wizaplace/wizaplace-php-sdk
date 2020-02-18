<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\SDK\Order;

use Wizaplace\SDK\Catalog\DeclinationId;

/**
 * Class OrderItem
 * @package Wizaplace\SDK\Order
 */
final class OrderItem
{
    /** @var null|string */
    private $itemId;
    /** @var DeclinationId */
    private $declinationId;
    /** @var string */
    private $productName;
    /** @var string */
    private $productCode;
    /** @var int|null */
    private $maxPriceAdjustment;
    /** @var string|null */
    private $productImageId;
    /** @var float */
    private $price;
    /** @var int */
    private $amount;
    /** @var DeclinationOption[] */
    private $declinationOptions;
    /** @var string */
    private $customerComment;
    /** @var float */
    private $greenTax;
    /** @var null|string */
    private $supplierRef;
    /** @var bool */
    private $isSubscription;
    /** @var bool */
    private $isRenewable;

    /**
     * @internal
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->itemId = $data['itemId'] ?? null;
        $this->declinationId = new DeclinationId($data['declinationId']);
        $this->productName = $data['productName'];
        $this->productCode = $data['productCode'];
        $this->maxPriceAdjustment = $data['maxPriceAdjustment'] ?? null;
        $this->productImageId = $data['productImageId'] ?? null;
        $this->price = $data['price'];
        $this->amount = $data['amount'];
        $this->declinationOptions = array_map(static function (array $data) : DeclinationOption {
            return new DeclinationOption($data);
        }, $data['options'] ?? []);
        $this->customerComment = $data['customerComment'];
        $this->greenTax = $data['greenTax'];
        $this->supplierRef = $data['supplierRef'];
        $this->isSubscription = $data['isSubscription'] ?? false;
        $this->isRenewable = $data['isRenewable'] ?? false;
    }

    /** @return null|string */
    public function getItemId(): ?string
    {
        return $this->itemId;
    }

    /**
     * @return DeclinationId
     */
    public function getDeclinationId(): DeclinationId
    {
        return $this->declinationId;
    }

    /**
     * @return string
     */
    public function getProductName(): string
    {
        return $this->productName;
    }

    /**
     * @return string
     */
    public function getProductCode(): string
    {
        return $this->productCode;
    }

    /**
     * @return string|null
     */
    public function getProductImageId(): ?string
    {
        return $this->productImageId;
    }

    /**
     * @return int|null
     */
    public function getMaxPriceAdjustment(): ?int
    {
        return $this->maxPriceAdjustment;
    }

    /**
     * @return float
     */
    public function getPrice(): float
    {
        return $this->price;
    }

    /**
     * @return int
     */
    public function getAmount(): int
    {
        return $this->amount;
    }

    /**
     * @return DeclinationOption[]
     */
    public function getDeclinationOptions(): array
    {
        return $this->declinationOptions;
    }

    /**
     * @return string
     */
    public function getCustomerComment(): string
    {
        return $this->customerComment;
    }

    /**
     * @return float
     */
    public function getGreenTax(): float
    {
        return $this->greenTax;
    }

    public function getSupplierRef(): ?string
    {
        return $this->supplierRef;
    }

    /** @return bool */
    public function isSubscription(): bool
    {
        return $this->isSubscription;
    }

    /** @return bool */
    public function isRenewable(): bool
    {
        return $this->isRenewable;
    }
}
