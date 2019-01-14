<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\SDK\Basket;

use Wizaplace\SDK\Catalog\DeclinationId;
use Wizaplace\SDK\Image\Image;

final class BasketItem
{
    /** @var DeclinationId */
    private $declinationId;

    /** @var int */
    private $productId;

    /** @var string */
    private $productName;

    /** @var string */
    private $productCode;

    /** @var float */
    private $individualPrice;

    /** @var float */
    private $crossedOutPrice;

    /** @var Image */
    private $mainImage;

    /** @var int */
    private $quantity;

    /** @var float */
    private $total;

    /** @var DeclinationOption[] */
    private $declinationOptions;

    /** @var string */
    private $comment;

    /** @var Price */
    private $unitPrice;

    /** @var Price */
    private $totalPrice;

    /** @var float */
    private $greenTax;

    /** @var array  */
    private $divisions;

    /**
     * @internal
     */
    public function __construct(array $data)
    {
        $this->declinationId = new DeclinationId($data['declinationId']);
        $this->productId = $data['productId'];
        $this->productName = $data['productName'];
        $this->productCode = $data['productCode'];
        $this->individualPrice = $data['individualPrice'];
        $this->crossedOutPrice = $data['crossedOutPrice'];
        if ($data['mainImage']) {
            $this->mainImage = new Image($data['mainImage']);
        }
        $this->quantity = $data['quantity'];
        $this->total = $data['total'];
        $this->declinationOptions = array_map(static function (array $data) : DeclinationOption {
            return new DeclinationOption($data);
        }, $data['options'] ?? []);
        $this->comment = $data['comment'];
        $this->unitPrice = new Price($data['unitPrice']);
        $this->totalPrice = new Price($data['totalPrice']);
        $this->greenTax = $data['greenTax'];
        $this->divisions = $data['divisions'] ?? [];
    }

    public function getDeclinationId(): DeclinationId
    {
        return $this->declinationId;
    }

    /**
     * @return DeclinationOption[]
     */
    public function getDeclinationOptions(): array
    {
        return $this->declinationOptions;
    }

    public function getProductId(): int
    {
        return $this->productId;
    }

    public function getProductName(): string
    {
        return $this->productName;
    }

    public function getProductCode(): string
    {
        return $this->productCode;
    }

    /**
     * @deprecated {@see \Wizaplace\SDK\Basket\BasketItem::getUnitPrice} instead
     */
    public function getIndividualPrice(): float
    {
        return $this->individualPrice;
    }

    public function getCrossedOutPrice(): ?float
    {
        return $this->crossedOutPrice;
    }

    public function getMainImage(): ?Image
    {
        return $this->mainImage;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    /**
     * @deprecated {@see \Wizaplace\SDK\Basket\BasketItem::getTotalPrice} instead
     */
    public function getTotal(): float
    {
        return $this->total;
    }

    public function getComment(): string
    {
        return $this->comment;
    }

    public function getUnitPrice(): Price
    {
        return $this->unitPrice;
    }

    public function getTotalPrice(): Price
    {
        return $this->totalPrice;
    }

    public function getGreenTax(): float
    {
        return $this->greenTax;
    }

    public function getDivisions(): array
    {
        return $this->divisions;
    }
}
