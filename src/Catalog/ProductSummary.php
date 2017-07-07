<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\Catalog;

use Wizaplace\Image\Image;

class ProductSummary
{
    /** @var string  */
    private $productId;
    /** @var string */
    private $name;
    /** @var string */
    private $subtitle;
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
    /** @var SearchProductAttribute[] */
    private $attributes;
    /** @var SearchCategoryPath[] */
    private $categoryPath;
    /** @var string */
    private $slug;

    public function __construct(array $data)
    {
        $this->productId = (string) $data['productId'];
        $this->name = (string) $data['name'];
        $this->subtitle = (string) $data['subtitle'];
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
        $this->attributes = array_map(
            function ($attribute) {
                return new SearchProductAttribute($attribute);
            },
            $data['attributes']
        );
        $this->categoryPath = array_map(
            function ($categoryPath) {
                return new SearchCategoryPath($categoryPath);
            },
            $data['categoryPath']
        );
        $this->slug = (string) $data['slug'];
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

    public function getUrl(): string
    {
        return $this->url;
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
     * @return array
     */
    public function getCondition(): array
    {
        return $this->condition;
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

    /**
     * @return string[]
     */
    public function getCategorySlugs(): array
    {
        return array_map(function (SearchCategoryPath $category) : string {
            return $category->getSlug();
        }, $this->categoryPath);
    }

    public function getSlug(): string
    {
        return $this->slug;
    }
}
