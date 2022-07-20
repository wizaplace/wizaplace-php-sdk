<?php

/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Catalog;

use Wizaplace\SDK\Image\Image;
use Wizaplace\SDK\Image\ImagesDataTrait;

class RelatedProduct
{
    use ImagesDataTrait;
    
    /** @var int */
    private $parentProductId;

    /** @var string */
    private $type;

    /** @var int */
    private $productId;

    /** @var string */
    private $description;

    /** @var string */
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

    /** @var Image[] */
    private $images;

    /** @var string */
    private $company;

    public function __construct(array $data)
    {
        $this->parentProductId = $data['parentProductId'] ?? null;
        $this->type = $data['type'];
        $this->productId = $data['productId'];
        $this->description = $data['description'];
        $this->extra = $data['extra'];
        $this->name = $data['name'] ?? null;
        $this->status = $data['status'] ?? null;
        $this->url = $data['url'] ?? null;
        $this->minPrice = $data['minPrice'] ?? null;
        $this->code = $data['code'] ?? null;
        $this->supplierReference = $data['supplierReference'] ?? null;
        if (\array_key_exists('images', $data) === true) {
            $this->images = $this->getImagesWithAltText($data);
        } else {
            $this->images = [];
        }
        $this->company = $data['company'] ?? null;
    }

    public function getParentProductId(): ?int
    {
        return $this->parentProductId;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getProductId(): int
    {
        return $this->productId;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getExtra(): string
    {
        return $this->extra;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function getMinPrice(): ?float
    {
        return $this->minPrice;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function getSupplierReference(): ?string
    {
        return $this->supplierReference;
    }

    /**
     * @return Image[]
     */
    public function getImages(): ?array
    {
        return $this->images;
    }

    public function getCompany(): ?string
    {
        return $this->company;
    }
}
