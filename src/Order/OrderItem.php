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
    /** @var DeclinationId */
    private $declinationId;
    /** @var string */
    private $productName;
    /** @var string */
    private $productCode;
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

    /**
     * @internal
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->declinationId = new DeclinationId($data['declinationId']);
        $this->productName = $data['productName'];
        $this->productCode = $data['productCode'];
        $this->productImageId = $data['productImageId'] ?? null;
        $this->price = $data['price'];
        $this->amount = $data['amount'];
        $this->declinationOptions = array_map(static function (array $data) : DeclinationOption {
            return new DeclinationOption($data);
        }, $data['options'] ?? []);
        $this->customerComment = $data['customerComment'];
        $this->greenTax = $data['greenTax'];
        $this->supplierRef = $data['supplierRef'];
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
}
