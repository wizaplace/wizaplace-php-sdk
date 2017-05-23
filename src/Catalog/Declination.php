<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\Catalog;

use Wizaplace\Image\Image;

class Declination
{
    /** @var  string */
    private $code;

    /** @var  string */
    private $supplierReference;

    /** @var float */
    private $price;

    /** @var  float */
    private $priceWithTaxes;

    /** @var  float */
    private $priceWithoutVat;

    /** @var  float */
    private $vat;

    /** @var  float */
    private $greenTax;

    /** @var float */
    private $originalPrice;

    /** @var float|null */
    private $crossedOutPrice;

    /** @var int */
    private $amount;

    /** @var string|null */
    private $affiliateLink;

    /** @var Image[] */
    private $images;

    public function __construct(array $data)
    {
        $prices = $data['prices'] ?? [];
        $this->code = $data['code'];
        $this->supplierReference = $data['supplierReference'];
        $this->price = $data['price'];
        $this->originalPrice = $data['originalPrice'];
        $this->crossedOutPrice = $data['crossedOutPrice'];
        $this->priceWithTaxes = $prices['priceWithTaxes'] ?? $this->price;
        $this->priceWithoutVat = $prices['priceWithoutVat'] ?? $this->price;
        $this->vat = $prices['vat'] ?? 0;
        $this->greenTax = $data['greenTax'] ?? 0;
        $this->amount = $data['amount'];
        $this->affiliateLink = $data['affiliateLink'];
        $this->images = array_map(
            function ($imageData) {
                return new Image($imageData);
            },
            $data['images']
        );
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getSupplierReference(): string
    {
        return $this->supplierReference;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function getOriginalPrice(): float
    {
        return $this->originalPrice;
    }

    public function getCrossedOutPrice(): ?float
    {
        return $this->crossedOutPrice;
    }

    public function getPriceWithTaxes(): float
    {
        return $this->priceWithTaxes;
    }

    public function getPriceWithoutVat(): float
    {
        return $this->priceWithoutVat;
    }

    public function getVat(): float
    {
        return $this->vat;
    }

    public function getGreenTax(): float
    {
        return $this->greenTax;
    }

    public function getAmount(): int
    {
        return $this->amount;
    }

    public function getAffiliateLink(): ?string
    {
        return $this->affiliateLink;
    }

    /**
     * @return Image[]
     */
    public function getImages(): array
    {
        return $this->images;
    }
}
