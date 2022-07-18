<?php

/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Pim\Product\RelatedProduct;

use Nette\Utils\Image;
use Wizacha\Marketplace\Catalog\Company\Company;
use Wizacha\Marketplace\Catalog\Company\CompanySummary;
use Wizacha\Marketplace\Company\Company as CompanyCompany;

class RelatedProduct
{
    /** @var string */
    private $type;

    /** @var int */
    private $productId;

    /** @var null|string */
    private $description;

    /** @var null|string */
    private $extra;

    /** @var string */
    private $name;

    /** @var string */
    private $status;

    /** @var string */
    private $url;

    /** @var float */
    private $minPrice;

    /** @var string */
    private $code;

    /** @var string */
    private $supplierReference;

    /**
     * @var Image[]
     */
    private $images;

     /** @var CompanySummary */
     private $company;

    public function __construct(array $data)
    {
        $this->productId = $data['productId'];
        $this->type = $data['type'];
        $this->description = $data['description'];
        $this->extra = $data['extra'];
        $this->name = $data['name'];
        $this->status = $data['status'];
        $this->url = $data['url'];
        $this->minPrice = $data['minPrice'];
        $this->code = $data['code'];
        $this->supplierReference = $data['supplierReference'];
        $this->images = $data['images'];
        $this->company = $data['company'];
    }

    public function getProductId(): int
    {
        return $this->productId;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getExtra(): ?string
    {
        return $this->extra;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getMinPrice(): float
    {
        return $this->minPrice;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getSupplierReference(): string
    {
        return $this->supplierReference;
    }

    public function getImages(): array
    {
        return $this->images;
    }

    public function getCompany(): CompanySummary
    {
        return $this->company;
    }

    public function jsonSerialize(): array
    {
        return [
            'type' => $this->getType(),
            'productId' => $this->getProductId(),
            'description' => $this->getDescription(),
            'extra' => $this->getExtra(),
            'name' => $this->getName(),
            'status' => $this->getStatus(),
            'url' => $this->getUrl(),
            'minPrice' => $this->getMinPrice(),
            'code' => $this->getCode(),
            'supplierReference' => $this->getSupplierReference(),
            'images' => $this->getImages(),
            'company' => $this->getCompany()
        ];
    }
}
