<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\Catalog;

use Wizaplace\Exception\NotFound;

class Product
{
    /** @var string */
    private $id;

    /** @var string */
    private $code;

    /** @var string */
    private $supplierReference;

    /** @var string */
    private $name;

    /** @var string */
    private $url;

    /** @var string */
    private $shortDescription;

    /** @var string */
    private $description;

    /** @var string */
    private $slug;

    /** @var float */
    private $minPrice;

    /** @var float */
    private $greenTax;

    /** @var ProductAttribute[] */
    private $attributes;

    /** @var \DateTimeImmutable */
    private $creationDate;

    /** @var bool */
    private $isTransactional;

    /** @var float */
    private $weight;

    /** @var null|float */
    private $averageRating;

    /** @var Shipping[] */
    private $shippings;

    /** @var Company[] */
    private $companies;

    /** @var array */
    private $categoryPath;

    /** @var Declination[] */
    private $declinations;

    /** @var Option[] */
    private $options;

    public function __construct(array $data)
    {
        $this->id = (string) $data['id'];
        $this->code = $data['code'];
        $this->supplierReference = $data['supplierReference'];
        $this->name = $data['name'];
        $this->url = $data['url'];
        $this->shortDescription = $data['shortDescription'];
        $this->description = $data['description'];
        $this->slug = (string) $data['slug'];
        $this->minPrice = (float) $data['minPrice'];
        $this->greenTax = (float) $data['greenTax'];
        $this->attributes = array_map(
            function (array $attributeData) {
                return new ProductAttribute($attributeData);
            },
            $data['attributes']
        );
        $this->creationDate = new \DateTimeImmutable($data['creationDate'] ?? '-6days');
        $this->isTransactional = $data['isTransactional'];
        $this->weight = $data['weight'];
        if (isset($data['averageRating'])) {
            $this->averageRating = $data['averageRating'];
        }
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
        $this->options = array_map(
            function (array $option) : Option {
                return new Option($option);
            },
            $data['options']
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

    public function getGreenTax(): float
    {
        return $this->greenTax;
    }

    /**
     * @return ProductAttribute[]
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function getCreationDate(): \DateTimeImmutable
    {
        return $this->creationDate;
    }

    public function isTransactional(): bool
    {
        return $this->isTransactional;
    }

    public function getWeight(): float
    {
        return $this->weight;
    }

    public function getAverageRating(): ?float
    {
        return $this->averageRating;
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
     * @return string[]
     */
    public function getCategorySlugs(): array
    {
        return array_map(function (ProductCategory $category) : string {
            return $category->getSlug();
        }, $this->categoryPath);
    }

    /**
     * @return Declination[]
     */
    public function getDeclinations(): array
    {
        return $this->declinations;
    }

    /**
     * @return Option[]
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * @throws NotFound
     */
    public function getDeclination(string $declinationId): Declination
    {
        $declinations = $this->getDeclinations();
        foreach ($declinations as $declination) {
            if ($declination->getId() === $declinationId) {
                return $declination;
            }
        }

        throw new NotFound('Declination '.$declinationId.' was not found.');
    }

    /**
     * @param int[] $variantIds
     * @throws NotFound
     */
    public function getDeclinationFromOptions(array $variantIds): Declination
    {
        foreach ($this->declinations as $declination) {
            if ($declination->hasVariants($variantIds)) {
                return $declination;
            }
        }

        throw new NotFound('Declination was not found.');
    }
}
