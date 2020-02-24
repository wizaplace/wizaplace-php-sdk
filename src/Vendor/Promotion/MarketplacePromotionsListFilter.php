<?php

/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Vendor\Promotion;

use Wizaplace\SDK\ArrayableInterface;

/**
 * Manage filters on get marketplace promotions list
 */
class MarketplacePromotionsListFilter implements ArrayableInterface
{
    /** @var null|string */
    protected $coupon;

    /** @var null|bool */
    protected $isActive;

    /** @var null|bool */
    protected $isValid;

    public function __construct(?string $coupon, bool $isActive = null, bool $isValid = null)
    {
        $this->coupon = $coupon;
        $this->isActive = $isActive;
        $this->isValid = $isValid;
    }

    public function getCoupon(): ?string
    {
        return $this->coupon;
    }

    public function setCoupon(?string $coupon): self
    {
        $this->coupon = $coupon;

        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(?bool $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }

    public function isValid(): ?bool
    {
        return $this->isValid;
    }

    public function setIsValid(?bool $isValid): self
    {
        $this->isValid = $isValid;

        return $this;
    }

    /**
     * @return mixed[]
     */
    public function toArray(): array
    {
        $filters = [];

        if (\is_string($this->getCoupon())) {
            $filters['coupon'] = $this->getCoupon();
        }

        if (\is_bool($this->isActive())) {
            $filters['active'] = $this->isActive();
        }

        if (\is_bool($this->isValid())) {
            $filters['isValid'] = $this->isValid();
        }

        return $filters;
    }
}
