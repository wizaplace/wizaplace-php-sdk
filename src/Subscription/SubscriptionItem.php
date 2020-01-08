<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @license     Proprietary
 * @copyright   Copyright (c) Wizacha
 */
declare(strict_types=1);

namespace Wizaplace\SDK\Subscription;

use Wizaplace\SDK\Price;

final class SubscriptionItem
{
    /** @var int */
    private $categoryId;

    /** @var int */
    private $productId;

    /** @var string */
    private $productCode;

    /** @var string */
    private $productName;

    /** @var bool */
    private $productIsRenewable;

    /** @var string */
    private $declinationId;

    /** @var Price */
    private $unitPrice;

    /** @var int */
    private $quantity;

    /** @var Price */
    private $totalPrice;

    public function __construct(array $data)
    {
        $this->categoryId = $data["categoryId"];
        $this->productId = $data["productId"];
        $this->productCode = $data["productCode"];
        $this->productName = $data["productName"];
        $this->productIsRenewable = $data["productIsRenewable"];
        $this->declinationId = $data["declinationId"];
        $this->unitPrice = new Price($data["unitPrice"]);
        $this->quantity = $data["quantity"];
        $this->totalPrice = new Price($data["totalPrice"]);
    }

    public function getCategoryId(): int
    {
        return $this->categoryId;
    }

    public function getProductId(): int
    {
        return $this->productId;
    }

    public function getProductCode(): string
    {
        return $this->productCode;
    }

    public function getProductName(): string
    {
        return $this->productName;
    }

    public function isProductIsRenewable(): bool
    {
        return $this->productIsRenewable;
    }

    public function getDeclinationId(): string
    {
        return $this->declinationId;
    }

    public function getUnitPrice(): Price
    {
        return $this->unitPrice;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function getTotalPrice(): Price
    {
        return $this->totalPrice;
    }
}
