<?php
declare(strict_types = 1);

namespace Wizaplace\Favorite;

use Wizaplace\Favorite\Declination\DeclinationOption;
use Wizaplace\Favorite\Declination\DeclinationCompany;
use Wizaplace\Image\Image;

final class Declination
{
    /** @var string */
    private $id;

    /** @var int */
    private $productId;

    /** @var string */
    private $name;

    /** @var string */
    private $code;

    /** @var string */
    private $supplierReference;

    /** @var float */
    private $priceWithTaxes;

    /** @var float */
    private $priceWithoutVat;

    /** @var float */
    private $greenTax;

    /** @var float|null  */
    private $crossedOutPrice;

    /** @var float|null  */
    private $reductionPercentage;

    /** @var float */
    private $vat;

    /** @var int */
    private $quantity;

    /** @var string */
    private $affiliateLink;

    /** @var DeclinationOption[] */
    private $options;

    /** @var Image[] */
    private $images;

    /** @var bool */
    private $isUsed;

    /** @var string */
    private $description;

    /** @var string */
    private $shortDescription;

    /** @var Company */
    private $company;

    /** @var string */
    private $slug;

    /** @var string */
    private $categorySlugPath;

    public function __construct(array $data)
    {
        $this->id = $data['id'];
        $this->productId = $data['productId'];
        $this->name = $data['name'];
        $this->code = $data['code'];
        $this->supplierReference = $data['supplierReference'];
        $this->priceWithTaxes = $data['prices']['priceWithTaxes'];
        $this->priceWithoutVat = $data['prices']['priceWithoutVat'];
        $this->greenTax = $data['greenTax'];
        $this->vat = $data['prices']['vat'];
        $this->crossedOutPrice = $data['crossedOutPrice'] ?? null;
        $this->reductionPercentage = $data['reductionPercentage'] ?? null;
        $this->quantity = $data['quantity'];
        $this->affiliateLink = $data['affiliateLink'];
        $this->options = array_map(static function (array $data): DeclinationOption {
            return new DeclinationOption($data);
        }, $data['options']);
        $this->images = array_map(static function (array $data): Image {
            return new Image($data);
        }, $data['images']);
        $this->isUsed = (bool) $data['isUsed'];
        $this->description = $data['description'];
        $this->shortDescription = $data['shortDescription'];
        $this->company = new DeclinationCompany($data['company']);
        $this->slug = $data['slug'];
        $this->categorySlugPath = $data['categorySlugPath'];
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getProductId(): int
    {
        return $this->productId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getSupplierReference(): string
    {
        return $this->supplierReference;
    }

    public function getPriceWithTaxes(): float
    {
        return $this->priceWithTaxes;
    }

    public function getPriceWithoutVat(): float
    {
        return $this->priceWithoutVat;
    }

    public function getGreenTax(): float
    {
        return $this->greenTax;
    }

    public function getVat(): float
    {
        return $this->vat;
    }

    public function getCrossedOutPrice(): ?float
    {
        return $this->crossedOutPrice;
    }

    public function getReductionPercentage(): ?float
    {
        return $this->reductionPercentage;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function getAffiliateLink(): string
    {
        return $this->affiliateLink;
    }

    /**
     * @return DeclinationOption[]
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * @return Image[]
     */
    public function getImages(): array
    {
        return $this->images;
    }

    public function isUsed(): bool
    {
        return $this->isUsed;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getShortDescription(): ?string
    {
        return $this->shortDescription;
    }

    public function getCompany(): DeclinationCompany
    {
        return $this->company;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getCategorySlugPath(): string
    {
        return $this->categorySlugPath;
    }
}
