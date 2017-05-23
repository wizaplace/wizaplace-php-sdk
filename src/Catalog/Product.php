<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */

namespace Wizaplace\Catalog;

class Product
{
    /** @var  string */
    private $id;

    /** @var  string */
    private $code;

    /** @var string */
    private $supplierReference;

    /** @var  string */
    private $name;

    /** @var  string */
    private $url;

    /** @var  string */
    private $shortDescription;

    /** @var  string */
    private $description;

    /** @var  string */
    private $slug;

    /** @var  float */
    private $minPrice;

    /** @var  ProductAttribute[] */
    private $attributes;

    /** @var  \DateTime */
    private $creationDate;

    /** @var  bool */
    private $isTransactional;

    /** @var  Shipping[] */
    private $shippings;

    /** @var  Company[] */
    private $companies;

    /** @var  array */
    private $categoryPath;

    /** @var  array */
    private $declinations;

    public function __construct(array $data)
    {
        $this->id = (string) $data['id'];
        $this->code = $data['code'];
        $this->supplierReference = $data['supplierReference'];
        $this->name = $data['name'];
        $this->url = $data['url'];
        $this->shortDescription = $data['shortDescription'];
        $this->description = $data['description'];
        $this->slug = $data['slug'];
        $this->minPrice = (float) $data['minPrice'];
        $this->attributes = array_map(
            function (array $attributeData) {
                return new ProductAttribute($attributeData);
            },
            $data['attributes']
        );
        $this->creationDate = new \DateTime($data['creationDate'] ?? '-6days');
        $this->isTransactional = $data['isTransactional'];
        $this->shippings = array_map(
            function ($shippingData) {
                return new Shipping($shippingData);
            },
            $data['shippings']
        );
        $this->companies = array_map(
            function ($companyData) {
                return new Company($companyData);
            },
            $data['companies']
        );
        $this->categoryPath = array_map(
            function ($category) {
                return new ProductCategory($category);
            },
            $data['categoryPath']
        );
        $this->declinations = array_map(
            function ($declination) {
                return new Declination($declination);
            },
            $data['declinations']
        );
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getSupplierReference(): string
    {
        return $this->supplierReference;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getShortDescription(): string
    {
        return $this->shortDescription;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getMinPrice(): float
    {
        return $this->minPrice;
    }

    /**
     * @return ProductAttribute[]
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function getCreationDate(): \DateTime
    {
        return $this->creationDate;
    }

    public function isTransactional(): bool
    {
        return $this->isTransactional;
    }

    /**
     * @return Shipping[]
     */
    public function getShippings(): array
    {
        return $this->shippings;
    }

    /**
     * @return Company[]
     */
    public function getCompanies(): array
    {
        return $this->companies;
    }

    /**
     * @return ProductCategory[]
     */
    public function getCategoryPath(): array
    {
        return $this->categoryPath;
    }

    /**
     * @return array
     */
    public function getDeclinations(): array
    {
        return $this->declinations;
    }
}
