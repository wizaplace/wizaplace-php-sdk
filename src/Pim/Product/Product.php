<?php

/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Pim\Product;

use GuzzleHttp\Psr7\Uri;
use Psr\Http\Message\UriInterface;

/**
 * Class Product
 * @package Wizaplace\SDK\Pim\Product
 */
final class Product extends ProductSummary
{
    /** @var UriInterface */
    private $mainImage;

    /** @var UriInterface[] */
    private $additionalImages;

    /** @var string */
    private $fullDescription;

    /** @var string */
    private $shortDescription;

    /** @var int[] */
    private $taxIds;

    /** @var ProductDeclination[] */
    private $declinations;

    /** @var \DateTimeImmutable */
    private $availibilityDate;

    /** @var bool */
    private $infiniteStock;

    /** @var null|string */
    private $productTemplateType;

    /** @var null|bool */
    private $isSubscription;

    /** @var null|bool */
    private $isRenewable;

    /** @var null|string */
    private $slug;

    /** @var null|string */
    private $seoTitle;

    /** @var null|string */
    private $seoDescription;

    /** @var null|string */
    private $seoKeywords;

    /**
     * @internal
     *
     * @param array $data
     *
     * @throws \Exception
     */
    public function __construct(array $data)
    {
        parent::__construct($data);

        $this->fullDescription = $data['full_description'];
        $this->shortDescription = $data['short_description'];
        $this->taxIds = $data['tax_ids'];
        if (isset($data['main_pair']['detailed']['image_path'])) {
            $this->mainImage = self::unserializeImage($data['main_pair']);
        }
        usort(
            $data['image_pairs'],
            function ($a, $b) {
                return $a['detailed']['position'] <=> $b['detailed']['position'];
            }
        );
        $this->additionalImages = array_map([self::class, 'unserializeImage'], $data['image_pairs'] ?? []);
        $this->declinations = array_map(
            static function (array $declinationData): ProductDeclination {
                return new ProductDeclination($declinationData);
            },
            $data['inventory'] ?? []
        );
        $this->availibilityDate = new \DateTimeImmutable('@' . $data['avail_since']);
        $this->infiniteStock = (bool) $data['infinite_stock'];
        $this->productTemplateType = $data['product_template_type'] ?? null;
        $this->isSubscription = $data['is_subscription'] ?? null;
        $this->isRenewable = $data['is_renewable'] ?? null;
        $this->slug = $data['slug'] ?? null;
        $this->seoTitle = $data['seoTitle'] ?? null;
        $this->seoDescription = $data['seoDescription'] ?? null;
        $this->seoKeywords = $data['seoKeywords'] ?? null;
    }

    /**
     * @return string
     */
    public function getFullDescription(): string
    {
        return $this->fullDescription;
    }

    /**
     * @return string
     */
    public function getShortDescription(): string
    {
        return $this->shortDescription;
    }

    /**
     * @return int[]
     */
    public function getTaxIds(): array
    {
        return $this->taxIds;
    }

    /**
     * @return UriInterface|null
     */
    public function getMainImage(): ?UriInterface
    {
        return $this->mainImage;
    }

    /**
     * @return UriInterface[]
     */
    public function getAdditionalImages(): array
    {
        return $this->additionalImages;
    }

    /**
     * @return ProductDeclination[]
     */
    public function getDeclinations(): array
    {
        return $this->declinations;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getAvailibilityDate(): \DateTimeImmutable
    {
        return $this->availibilityDate;
    }

    /**
     * @return bool
     */
    public function hasInfiniteStock(): bool
    {
        return $this->infiniteStock;
    }

    /**
     * @return null|string
     */
    public function getProductTemplateType(): ?string
    {
        return $this->productTemplateType;
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

    /**
     * @param array $imageData
     *
     * @return UriInterface
     */
    private static function unserializeImage(array $imageData): UriInterface
    {
        return new Uri($imageData['detailed']['image_path']);
    }

    /** @return null|string */
    public function getSlug(): ?string
    {
        return $this->slug;
    }

    /** @return null|string */
    public function getSeoTitle(): ?string
    {
        return $this->seoTitle;
    }

    /** @return null|string */
    public function getSeoDescription(): ?string
    {
        return $this->seoDescription;
    }

    /** @return null|string */
    public function getSeoKeywords(): ?string
    {
        return $this->seoKeywords;
    }
}
