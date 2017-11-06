<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\SDK\Catalog;

use Wizaplace\SDK\Image\Image;

final class ProductSummary
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
    /** @var Condition[] */
    private $conditions;
    /** @var SearchProductAttribute[] */
    private $attributes;
    /** @var SearchCategoryPath[] */
    private $categoryPath;
    /** @var string */
    private $slug;
    /** @var CompanySummary[] */
    private $companies;
    /** @var ProductLocation|null */
    private $geolocation;

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
        $this->conditions = array_map(function (string $condition): Condition {
            return new Condition($condition);
        }, $data['conditions']);
        $this->attributes = array_map(static function (array $attribute) : SearchProductAttribute {
            return new SearchProductAttribute($attribute);
        }, $data['attributes']);
        $this->categoryPath = array_map(static function (array $categoryPath) : SearchCategoryPath {
            return new SearchCategoryPath($categoryPath);
        }, $data['categoryPath']);
        $this->slug = (string) $data['slug'];
        $this->companies = array_map(static function (array $companyData) : CompanySummary {
            return new CompanySummary($companyData);
        }, $data['companies'] ?? []);
        $this->geolocation = isset($data['geolocation']) ? new ProductLocation($data['geolocation']) : null;
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
     * Returns the various conditions the product is available in.
     * @return Condition[]
     */
    public function getConditions(): array
    {
        return $this->conditions;
    }

    /**
     * @return SearchProductAttribute[]
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * @return SearchCategoryPath[]
     */
    public function getCategoryPath(): array
    {
        return $this->categoryPath;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    /**
     * @return CompanySummary[]
     */
    public function getCompanies(): array
    {
        return $this->companies;
    }

    public function getGeolocation(): ?ProductLocation
    {
        return $this->geolocation;
    }

    public function getMainDeclinationId(): ?DeclinationId
    {
        if ($this->getDeclinationCount() > 1) {
            // There is more than one declination, so the product ID can't be used as a declination ID
            return null;
        }
        if (!ctype_digit($this->productId)) {
            // an MVP id currently can't be used as a declination ID
            return null;
        }

        return new DeclinationId($this->productId);
    }
}
