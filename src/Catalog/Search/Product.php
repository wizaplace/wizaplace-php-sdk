<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\SDK\Catalog\Search;

use Wizaplace\SDK\Image\Image;

final class Product
{
    /** @var string  */
    private $productId;

    /** @var string */
    private $name;

    /** @var string */
    private $subtitle;

    /** @var string */
    private $shortDescription;

    /** @var float */
    private $minimumPrice;

    /** @var float|null */
    private $crossedOutPrice;

    /** @var bool */
    private $isAvailable;

    /** @var string */
    private $url;

    /** @var \DateTimeImmutable */
    private $createdAt;

    /** @var \DateTimeImmutable */
    private $updatedAt;

    /** @var int */
    private $declinationCount;

    /** @var string|null */
    private $affiliateLink;

    /** @var Image|null */
    private $mainImage;

    /** @var float|null */
    private $averageRating;

    /** @var array */
    private $condition;

    /** @var Attribute[] */
    private $attributes;

    /** @var Category[] */
    private $category;

    /** @var string */
    private $slug;

    /** @var Company[] */
    private $companies;

    /**
     * @internal
     */
    public function __construct(array $data)
    {
        $this->productId = (string) $data['productId'];
        $this->name = (string) $data['name'];
        $this->subtitle = (string) $data['subtitle'];
        $this->shortDescription = (string) $data['shortDescription'];
        $this->minimumPrice = $data['minimumPrice'];
        $this->crossedOutPrice = $data['crossedOutPrice'];
        $this->isAvailable = $data['isAvailable'];
        $this->url = (string) $data['url'];
        $this->createdAt = new \DateTimeImmutable("@{$data['createdAt']}");
        $this->updatedAt = new \DateTimeImmutable("@{$data['updatedAt']}");
        $this->declinationCount = $data['declinationCount'];
        $this->affiliateLink = $data['affiliateLink'] ?? null;
        $this->mainImage = $data['mainImage'] ? new Image($data['mainImage']) : null;
        $this->averageRating = $data['averageRating'] ?? null;
        $this->condition = $data['conditions'];
        $this->attributes = array_map(static function (array $attribute) : Attribute {
            return new Attribute($attribute);
        }, $data['attributes']);
        $this->category = array_map(static function (array $category) : Category {
            return new Category($category);
        }, $data['categoryPath']);
        $this->slug = (string) $data['slug'];
        $this->companies = array_map(static function (array $company) : Company {
            return new Company($company);
        }, $data['companies'] ?? []);
    }

    public function getId(): string
    {
        return $this->productId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getSubtitle(): string
    {
        return $this->subtitle;
    }

    public function getShortDescription(): string
    {
        return $this->shortDescription;
    }

    public function getMinimumPrice(): float
    {
        return $this->minimumPrice;
    }

    public function getCrossedOutPrice(): ?float
    {
        return $this->crossedOutPrice;
    }

    public function isAvailable(): bool
    {
        return $this->isAvailable;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): \DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function getDeclinationCount(): int
    {
        return $this->declinationCount;
    }

    public function getAffiliateLink(): ?string
    {
        return $this->affiliateLink;
    }

    public function getMainImage(): ?Image
    {
        return $this->mainImage;
    }

    /**
     * @return float|null
     */
    public function getAverageRating()
    {
        return $this->averageRating;
    }

    /**
     * @TODO: document
     * @return array
     */
    public function getCondition(): array
    {
        return $this->condition;
    }

    /**
     * @return Attribute[]
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * @return Category[]
     */
    public function getCategoryPath(): array
    {
        return $this->category;
    }

    /**
     * @return string[]
     */
    public function getCategorySlugs(): array
    {
        return array_map(static function (Category $category) : string {
            return $category->getSlug();
        }, $this->category);
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    /**
     * @return Company[]
     */
    public function getCompanies(): array
    {
        return $this->companies;
    }
}
