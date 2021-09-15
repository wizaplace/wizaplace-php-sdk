<?php

/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Catalog;

use Wizaplace\SDK\Image\Image;

use function theodorejb\polycast\to_string;

/**
 * Class ProductSummary
 * @package Wizaplace\SDK\Catalog
 */
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
    /** @var Image[] */
    private $images;
    /** @var DeclinationImages[] */
    private $declinationsImages;
    /** @var float */
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
    /** @var null|DeclinationId */
    private $mainDeclinationId;
    /** @var null|ProductOffer[] */
    private $offers;
    /** @var null|bool */
    private $isSubscription;
    /** @var null|bool */
    private $isRenewable;
    /** @var null|int */
    private $maxPriceAdjustment;
    /** @var null|string */
    private $code;
    /** @var RelatedProduct[] */
    private $relatedOffers;

    /**
     * @internal
     *
     * @param array $data
     *
     * @throws \Exception
     */
    public function __construct(array $data)
    {
        $this->productId = to_string($data['productId']);
        $this->name = to_string($data['name']);
        $this->code = $data['code'] ?? null;
        $this->subtitle = to_string($data['subtitle']);
        $this->shortDescription = to_string($data['shortDescription']);
        $this->minimumPrice = $data['minimumPrice'];
        $this->crossedOutPrice = $data['crossedOutPrice'];
        $this->isAvailable = $data['isAvailable'];
        $this->url = to_string($data['url']);
        $this->createdAt = new \DateTimeImmutable("@{$data['createdAt']}");
        $this->updatedAt = new \DateTimeImmutable("@{$data['updatedAt']}");
        $this->declinationCount = $data['declinationCount'];
        $this->affiliateLink = $data['affiliateLink'] ?? null;
        $this->mainImage = $data['mainImage'] ? new Image($data['mainImage']) : null;
        $this->images = array_map(
            function (array $image): Image {
                return new Image($image);
            },
            $data['images'] ?? []
        );
        $this->declinationsImages = $this->denormalizeDeclinationImages($data['declinationsImages'] ?? []);
        $this->averageRating = $data['averageRatingFloat'] ?? 0;
        $this->conditions = array_map(
            function (string $condition): Condition {
                return new Condition($condition);
            },
            $data['conditions']
        );
        $this->attributes = array_map(
            static function (array $attribute): SearchProductAttribute {
                return new SearchProductAttribute($attribute);
            },
            $data['attributes']
        );
        $this->categoryPath = array_map(
            static function (array $categoryPath): SearchCategoryPath {
                return new SearchCategoryPath($categoryPath);
            },
            $data['categoryPath']
        );
        $this->slug = to_string($data['slug']);
        $this->companies = array_map(
            static function (array $companyData): CompanySummary {
                return new CompanySummary($companyData);
            },
            $data['companies'] ?? []
        );
        $this->geolocation = isset($data['geolocation']) ? new ProductLocation($data['geolocation']) : null;
        if (isset($data['mainDeclination']['id'])) {
            $this->mainDeclinationId = new DeclinationId($data['mainDeclination']['id']);
        }
        if (isset($data['offers'])) {
            $this->offers = array_map(
                function (array $offer): ProductOffer {
                    return new ProductOffer($offer);
                },
                $data['offers']
            );
        }
        $this->isSubscription = $data['isSubscription'] ?? null;
        $this->isRenewable = $data['isRenewable'] ?? null;
        $this->maxPriceAdjustment = \array_key_exists('maxPriceAdjustment', $data) === true ? $data['maxPriceAdjustment'] : null;

        $this->relatedOffers = $this->denormalizeRelatedOffers($data['relatedOffers'] ?? []);
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->productId;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /** @return string|null */
    public function getCode(): ?string
    {
        return $this->code;
    }

    /**
     * @return string
     */
    public function getSubtitle(): string
    {
        return $this->subtitle;
    }

    /**
     * @return string
     */
    public function getShortDescription(): string
    {
        return $this->shortDescription;
    }

    /**
     * @return float
     */
    public function getMinimumPrice(): float
    {
        return $this->minimumPrice;
    }

    /**
     * @return float|null
     */
    public function getCrossedOutPrice(): ?float
    {
        return $this->crossedOutPrice;
    }

    /**
     * @return bool
     */
    public function isAvailable(): bool
    {
        return $this->isAvailable;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getUpdatedAt(): \DateTimeImmutable
    {
        return $this->updatedAt;
    }

    /**
     * @return int
     */
    public function getDeclinationCount(): int
    {
        return $this->declinationCount;
    }

    /**
     * @return string|null
     */
    public function getAffiliateLink(): ?string
    {
        return $this->affiliateLink;
    }

    /**
     * @return Image|null
     */
    public function getMainImage(): ?Image
    {
        return $this->mainImage;
    }

    /**
     * @return Image[]
     */
    public function getImages(): ?array
    {
        return $this->images;
    }

    /**
     * @return DeclinationImages[]
     */
    public function getDeclinationsImages(): ?array
    {
        return $this->declinationsImages;
    }

    /**
     * @return float
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

    /**
     * @return string
     */
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

    /**
     * @return ProductLocation|null
     */
    public function getGeolocation(): ?ProductLocation
    {
        return $this->geolocation;
    }

    /**
     * @return DeclinationId|null
     */
    public function getMainDeclinationId(): ?DeclinationId
    {
        return $this->mainDeclinationId;
    }

    /**
     * @return null|ProductOffer[]
     */
    public function getOffers(): ?array
    {
        return $this->offers;
    }

    /**
     * @return null|bool
     */
    public function isSubscription(): ?bool
    {
        return $this->isSubscription;
    }

    /**
     * @return null|bool
     */
    public function isRenewable(): ?bool
    {
        return $this->isRenewable;
    }

    public function getMaxPriceAdjustment(): ?int
    {
        return $this->maxPriceAdjustment;
    }

    /** @return RelatedProduct[] */
    public function getRelatedOffers(): array
    {
        return $this->relatedOffers;
    }

    /**
     * @param array $data
     * @return DeclinationImages[]
     */
    private function denormalizeDeclinationImages(array $data): array
    {
        return array_map(
            function (array $declinationImage): DeclinationImages {
                return new DeclinationImages([
                    'declinationId' => $declinationImage['declinationId'],
                    'images' => array_map(
                        function (array $image): Image {
                            return new Image($image);
                        },
                        $declinationImage['images'] ?? []
                    )
                ]);
            },
            $data
        );
    }

    /**
     * @param mixed[] $data
     * @return RelatedProduct[]
     */
    private function denormalizeRelatedOffers(array $data): array
    {
        return array_map(
            function (array $relatedProduct): RelatedProduct {
                return new RelatedProduct([
                    'type' => $relatedProduct['type'],
                    'productId' => $relatedProduct['productId'],
                    'description' => $relatedProduct['description'],
                    'extra' => $relatedProduct['extra'],
                    'name' => $relatedProduct['name'],
                    'status' => $relatedProduct['status'],
                    'url' => $relatedProduct['url'],
                    'minPrice' => $relatedProduct['minPrice'],
                    'code' => $relatedProduct['code'],
                    'supplierReference' => $relatedProduct['supplierReference'],
                    'images' => array_map(
                        function (array $image): Image {
                            return new Image($image);
                        },
                        $relatedProduct['images'] ?? []
                    ),
                    'company' => $relatedProduct['company'],
                ]);
            },
            $data
        );
    }
}
