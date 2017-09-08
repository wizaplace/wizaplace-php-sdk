<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\SDK\Catalog;

use Wizaplace\SDK\Image\Image;

final class DeclinationSummary
{
    /** @var string */
    private $id;

    /** @var int */
    private $productId;

    /** @var string */
    private $name;

    /** @var string */
    private $slug;

    /** @var string */
    private $code;

    /** @var float|null  */
    private $crossedOutPrice;

    /** @var float */
    private $priceWithTaxes;

    /** @var float */
    private $priceWithoutVat;

    /** @var float */
    private $vat;

    /** @var int */
    private $amount;

    /** @var string */
    private $affiliateLink;

    /** @var DeclinationOption[] */
    private $options;

    /** @var Image|null */
    private $mainImage;

    /** @var CompanySummary */
    private $company;

    /** @var ProductCategory[] */
    private $categoryPath;

    /**
     * @internal
     */
    public function __construct(array $data)
    {
        $this->id = $data['id'];
        $this->productId = $data['productId'];
        $this->name = $data['name'];
        $this->code = $data['code'];
        $this->priceWithTaxes = $data['prices']['priceWithTaxes'];
        $this->priceWithoutVat = $data['prices']['priceWithoutVat'];
        $this->vat = $data['prices']['vat'];
        $this->crossedOutPrice = $data['crossedOutPrice'] ?? null;
        $this->amount = $data['amount'];
        $this->affiliateLink = $data['affiliateLink'];
        $this->options = array_map(static function (array $data): DeclinationOption {
            return new DeclinationOption($data);
        }, $data['options']);
        $this->mainImage = isset($data['mainImage']) ? new Image($data['mainImage']) : null;
        $this->company = new CompanySummary(
            $data['company']['id'],
            $data['company']['name'],
            $data['company']['slug'],
            isset($data['company']['image']) ? new Image($data['company']['image']) : null
        );
        $this->slug = $data['slug'];
        $this->categoryPath = array_map(static function (array $data): ProductCategory {
            return new ProductCategory($data);
        }, $data['categoryPath']);
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

    public function getCrossedOutPrice(): ?float
    {
        return $this->crossedOutPrice;
    }

    public function getAmount(): int
    {
        return $this->amount;
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

    public function getMainImage(): ?Image
    {
        return $this->mainImage;
    }

    public function getCompany(): CompanySummary
    {
        return $this->company;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    /**
     * @return ProductCategory[]
     */
    public function getCategoryPath(): array
    {
        return $this->categoryPath;
    }
}
