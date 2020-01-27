<?php

/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Basket;

/**
 * Class Basket
 * @package Wizaplace\SDK\Basket
 */
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

    /** @var null|float */
    private $totalShippingTax;

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

    /** @var bool */
    private $isEligibleToPickupPointsShipping;

    /** @var bool */
    private $isPickupPointsShipping;

    /** @var null|Address */
    private $shippingAddress;

    /** @var null|Address */
    private $billingAddress;

    /** @var float */
    private $totalMarketplaceDiscount;

    /**
     * @internal
     */
    public function __construct(array $data)
    {
        $this->id = $data['id'];
        $this->coupons = $data['coupons'];
        $this->subtotal = $data['subtotal'];
        $this->totalDiscount = $data['totalDiscount'];
        $this->totalMarketplaceDiscount = $data['totalMarketplaceDiscount'] ?? 0.0;
        $this->totalShipping = $data['totalShipping'];
        $this->totalShippingTax = $data['totalShippingTax'] ?? null ;
        $this->totalTax = $data['totalTax'];
        $this->total = $data['total'];
        $this->totalQuantity = $data['totalQuantity'];
        $this->itemsPrice = new Price($data['totalItemsPrice']);
        $this->shippingPrice = new Price($data['totalShippingsPrice']);
        $this->totalPrice = new Price($data['totalGlobalPrice']);
        $this->comment = $data['comment'];
        $this->isEligibleToPickupPointsShipping = $data['isEligibleToPickupPointsShipping'] ?? false;
        $this->isPickupPointsShipping = $data['isPickupPointsShipping'] ?? false;
        if (isset($data['shippingAddress'])) {
            $this->shippingAddress = new Address($data['shippingAddress']);
            if ($this->shippingAddress->getTitle() === null && $this->shippingAddress->getAddress() === '' && $this->getShippingAddress()->getCity() === '') {
                $this->shippingAddress = null; // The API returns an address with all the values set but empty. We consider this a null address.
            }
        }
        if (\array_key_exists('billingAddress', $data) === true) {
            $this->billingAddress = new Address($data['billingAddress']);
            if ($this->billingAddress->getTitle() === null && $this->billingAddress->getAddress() === '' && $this->getBillingAddress()->getCity() === '') {
                $this->billingAddress = null; // The API returns an address with all the values set but empty. We consider this a null address.
            }
        }

        $this->companyGroups = array_map(
            static function (array $companyGroup): BasketCompanyGroup {
                return new BasketCompanyGroup($companyGroup);
            },
            $data['companyGroups']
        );
    }

    /**
     * @return string
     */
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

    /**
     * @return float
     */
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

    /** @return null|float */
    public function getTotalShippingTax(): ?float
    {
        return $this->totalShippingTax;
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

    /**
     * @return Price
     */
    public function getItemsPrice(): Price
    {
        return $this->itemsPrice;
    }

    /**
     * @return Price
     */
    public function getShippingPrice(): Price
    {
        return $this->shippingPrice;
    }

    /**
     * @return Price
     */
    public function getTotalPrice(): Price
    {
        return $this->totalPrice;
    }

    /**
     * @return int
     */
    public function getTotalQuantity(): int
    {
        return $this->totalQuantity;
    }

    /**
     * @return string
     */
    public function getComment(): string
    {
        return $this->comment;
    }

    /**
     * Returns true if this basket can use pickup-point shipping.
     *
     * @return bool
     */
    public function isEligibleToPickupPointsShipping(): bool
    {
        return $this->isEligibleToPickupPointsShipping;
    }

    /**
     * Returns true if this basket does use pickup-point shipping.
     *
     * @return bool
     */
    public function isPickupPointsShipping(): bool
    {
        return $this->isPickupPointsShipping;
    }

    /**
     * @return Address|null
     */
    public function getShippingAddress(): ?Address
    {
        return $this->shippingAddress;
    }

    /** @return Address|null */
    public function getBillingAddress(): ?Address
    {
        return $this->billingAddress;
    }

    /**
     * @param string $id
     *
     * @return Basket
     */
    public static function createEmpty(string $id): self
    {
        return new self(
            [
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
                'isEligibleToPickupPointsShipping' => false,
                'isPickupPointsShipping' => false,
            ]
        );
    }

    public function getTotalMarketplaceDiscount(): float
    {
        return $this->totalMarketplaceDiscount;
    }
}
