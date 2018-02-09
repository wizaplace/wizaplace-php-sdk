<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\SDK\Basket;

final class Basket
{
    /** @var string */
    private $id;

    /** @var string[] */
    private $coupons;

    /** @var BasketCompanyGroup[] */
    private $companyGroups;

    /** @var float */
    private $subtotal;

    /** @var float */
    private $totalDiscount;

    /** @var float */
    private $totalShipping;

    /** @var float */
    private $totalTax;

    /** @var float */
    private $total;

    /** @var int */
    private $totalQuantity;

    /** @var string */
    private $comment;

    /** @var Price */
    private $itemsPrice;

    /** @var Price */
    private $shippingPrice;

    /** @var Price */
    private $totalPrice;

    /**
     * @internal
     */
    public function __construct(array $data)
    {
        $this->id = $data['id'];
        $this->coupons = $data['coupons'];
        $this->subtotal = $data['subtotal'];
        $this->totalDiscount = $data['totalDiscount'];
        $this->totalShipping = $data['totalShipping'];
        $this->totalTax = $data['totalTax'];
        $this->total = $data['total'];
        $this->totalQuantity = $data['totalQuantity'];
        $this->itemsPrice = new Price($data['totalItemsPrice']);
        $this->shippingPrice = new Price($data['totalShippingsPrice']);
        $this->totalPrice = new Price($data['totalGlobalPrice']);
        $this->comment = $data['comment'];

        $this->companyGroups = array_map(static function (array $companyGroup) : BasketCompanyGroup {
            return new BasketCompanyGroup($companyGroup);
        }, $data['companyGroups']);
    }

    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return string[] a list of applied coupon codes
     */
    public function getCoupons(): array
    {
        return $this->coupons;
    }

    /**
     * @return BasketCompanyGroup[]
     */
    public function getCompanyGroups(): array
    {
        return $this->companyGroups;
    }

    /**
     * @deprecated {@see \Wizaplace\SDK\Basket\Basket::getItemsPrice} instead
     */
    public function getSubtotal(): float
    {
        return $this->subtotal;
    }

    public function getTotalDiscount(): float
    {
        return $this->totalDiscount;
    }

    /**
     * @deprecated {@see \Wizaplace\SDK\Basket\Basket::getShippingPrice} instead
     */
    public function getTotalShipping(): float
    {
        return $this->totalShipping;
    }

    /**
     * @deprecated {@see \Wizaplace\SDK\Basket\Basket::getTotalPrice} instead
     */
    public function getTotalTax(): float
    {
        return $this->totalTax;
    }

    /**
     * @deprecated {@see \Wizaplace\SDK\Basket\Basket::getTotalPrice} instead
     */
    public function getTotal(): float
    {
        return $this->total;
    }

    public function getItemsPrice(): Price
    {
        return $this->itemsPrice;
    }

    public function getShippingPrice(): Price
    {
        return $this->shippingPrice;
    }

    public function getTotalPrice(): Price
    {
        return $this->totalPrice;
    }

    public function getTotalQuantity(): int
    {
        return $this->totalQuantity;
    }

    public function getComment(): string
    {
        return $this->comment;
    }

    public static function createEmpty(string $id): self
    {
        return new self([
            'id' => $id,
            'coupons' => [],
            'subtotal' => 0.0,
            'totalDiscount' => 0.0,
            'totalShipping' => 0.0,
            'totalTax' => 0.0,
            'total' => 0.0,
            'totalQuantity' => 0,
            'comment' => '',
            'companyGroups' => [],
            'totalItemsPrice' => [
                'priceWithoutVat' => 0.0,
                'priceWithTaxes' => 0.0,
                'vat' => 0.0,
            ],
            'totalShippingsPrice' => [
                'priceWithoutVat' => 0.0,
                'priceWithTaxes' => 0.0,
                'vat' => 0.0,
            ],
            'totalGlobalPrice' => [
                'priceWithoutVat' => 0.0,
                'priceWithTaxes' => 0.0,
                'vat' => 0.0,
            ],
        ]);
    }
}
