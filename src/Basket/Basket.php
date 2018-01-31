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

    /** @var bool */
    private $isEligibleToPickupPointsShipping;

    /** @var bool */
    private $isPickupPointsShipping;

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
        $this->comment = $data['comment'];
        $this->isEligibleToPickupPointsShipping = $data['isEligibleToPickupPointsShipping'] ?? false;
        $this->isPickupPointsShipping = $data['isPickupPointsShipping'] ?? false;

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

    public function getSubtotal(): float
    {
        return $this->subtotal;
    }

    public function getTotalDiscount(): float
    {
        return $this->totalDiscount;
    }

    public function getTotalShipping(): float
    {
        return $this->totalShipping;
    }

    public function getTotalTax(): float
    {
        return $this->totalTax;
    }

    public function getTotal(): float
    {
        return $this->total;
    }

    public function getTotalQuantity(): int
    {
        return $this->totalQuantity;
    }

    public function getComment(): string
    {
        return $this->comment;
    }

    public function isEligibleToPickupPointsShipping(): bool
    {
        return $this->isEligibleToPickupPointsShipping;
    }

    public function isPickupPointsShipping(): bool
    {
        return $this->isPickupPointsShipping;
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
            'isEligibleToPickupPointsShipping' => false,
            'isPickupPointsShipping' => false,
        ]);
    }
}
