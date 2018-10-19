<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\SDK\Order;

use Wizaplace\SDK\Catalog\DeclinationId;

final class OrderItem
{
    /** @var DeclinationId */
    private $declinationId;
    /** @var string */
    private $productName;
    /** @var string */
    private $productCode;
    /** @var array */
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

    /**
     * @internal
     */
    public function __construct(array $data)
    {
        $this->declinationId = new DeclinationId($data['declinationId']);
        $this->productName = $data['productName'];
        $this->productCode = $data['productCode'];
        $this->productImageId = array_map(function ($imageId) {
            return (int) $imageId;
        }, $data['detailed_id'] ?? []);
        $this->price = $data['price'];
        $this->amount = $data['amount'];
        $this->declinationOptions = array_map(static function (array $data) : DeclinationOption {
            return new DeclinationOption($data);
        }, $data['options'] ?? []);
        $this->customerComment = $data['customerComment'];
        $this->greenTax = $data['greenTax'];
    }

    public function getDeclinationId(): DeclinationId
    {
        return $this->declinationId;
    }

    public function getProductName(): string
    {
        return $this->productName;
    }

    public function getProductCode(): string
    {
        return $this->productCode;
    }

    public function getProductImageId(): array
    {
        return $this->productImageId;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

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

    public function getCustomerComment(): string
    {
        return $this->customerComment;
    }

    public function getGreenTax(): float
    {
        return $this->greenTax;
    }
}
