<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\SDK\Catalog;

use Psr\Http\Message\UriInterface;
use Wizaplace\SDK\Exception\NotFound;
use Wizaplace\SDK\Image\Image;
use function theodorejb\polycast\to_float;
use function theodorejb\polycast\to_string;

final class Product
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

    /** @var bool */
    private $isTransactional;

    /** @var float */
    private $weight;

    /** @var null|float */
    private $averageRating;

    /** @var Shipping[] */
    private $shippings;

    /** @var CompanySummary[] */
    private $companies;

    /** @var array */
    private $categoryPath;

    /** @var Declination[] */
    private $declinations;

    /** @var Option[] */
    private $options;

    /** @var null|ProductLocation */
    private $geolocation;

    /** @var null|ProductVideo */
    private $video;

    /** @var ProductAttachment[] */
    private $attachments;

    /** @var string */
    private $seoTitle;

    /** @var string */
    private $seoDescription;

    /** @var string */
    private $seoKeywords;

    /** @var \DateTimeImmutable|null */
    private $createdAt;

    /** @var \DateTimeImmutable|null */
    private $updatedAt;

    /**
     * @var Image[]
     */
    private $images;

    /** @var bool */
    private $infiniteStock;

    /** @var null|ProductOffer[] */
    private $offers;

    /**
     * @internal
     */
    public function __construct(array $data, UriInterface $apiBaseUrl)
    {
        $this->id = to_string($data['id']);
        $this->code = $data['code'];
        $this->supplierReference = $data['supplierReference'];
        $this->name = $data['name'];
        $this->url = $data['url'];
        $this->shortDescription = $data['shortDescription'];
        $this->description = $data['description'];
        $this->slug = to_string($data['slug']);
        $this->minPrice = to_float($data['minPrice']);
        $this->greenTax = to_float($data['greenTax']);
        $this->attributes = array_map(static function (array $attributeData) : ProductAttribute {
            return new ProductAttribute($attributeData);
        }, $data['attributes']);
        $this->isTransactional = $data['isTransactional'];
        $this->weight = $data['weight'];
        if (isset($data['averageRating'])) {
            $this->averageRating = $data['averageRating'];
        }
        $this->shippings = array_map(static function (array $shippingData) : Shipping {
            return new Shipping($shippingData);
        }, $data['shippings']);
        $this->companies = array_map(static function (array $companyData) : CompanySummary {
            return new CompanySummary($companyData);
        }, $data['companies']);
        $this->categoryPath = array_map(static function (array $category) : ProductCategory {
            return new ProductCategory($category);
        }, $data['categoryPath']);
        $this->declinations = array_map(static function (array $declination) : Declination {
            return new Declination($declination);
        }, $data['declinations']);
        $this->options = array_map(static function (array $option) : Option {
            return new Option($option);
        }, $data['options']);
        $this->geolocation = isset($data['geolocation']) ? new ProductLocation($data['geolocation']) : null;
        $this->video = isset($data['video']) ? new ProductVideo($data['video']) : null;
        $this->attachments = array_map(static function (array $attachmentData) use ($apiBaseUrl) : ProductAttachment {
            return new ProductAttachment($attachmentData, $apiBaseUrl);
        }, $data['attachments'] ?? []);
        $this->seoTitle = $data['seoData']['title'] ?? '';
        $this->seoDescription = $data['seoData']['description'] ?? '';
        $this->seoKeywords = $data['seoData']['keywords'] ?? '';
        $this->createdAt = isset($data['createdAt']) ? \DateTimeImmutable::createFromFormat(\DateTime::RFC3339, $data['createdAt']) : null;
        $this->updatedAt = isset($data['updatedAt']) ? \DateTimeImmutable::createFromFormat(\DateTime::RFC3339, $data['updatedAt']) : null;
        $this->infiniteStock = $data['infiniteStock'];

        if (!isset($data['images'])) {
            $this->images = [];
        } else {
            $this->images = array_map(static function (array $imageData) : Image {
                return new Image($imageData);
            }, $data['images']);
        }

        if (isset($data['offers'])) {
            $this->offers = array_map(function (array $offer): ProductOffer {
                return new ProductOffer($offer);
            }, $data['offers']);
        }
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
     * @return CompanySummary[]
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
    public function getDeclination(DeclinationId $declinationId): Declination
    {
        $declinations = $this->getDeclinations();
        foreach ($declinations as $declination) {
            if ($declination->getId()->equals($declinationId)) {
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

    /**
     * Get offers from other vendors for a given declination (in the context of multi-vendor products).
     * @return Declination[]
     */
    public function getOtherOffers(Declination $currentOffer): array
    {
        $options = $currentOffer->getOptions();
        /** @var DeclinationOption[] $optionsMap */
        $optionsMap = [];
        foreach ($options as $option) {
            $optionsMap[$option->getId()] = $option;
        }

        $givenDeclinationFound = false;
        /** @var Declination[] $result */
        $result = array_values(array_filter($this->declinations, static function (Declination $declination) use ($currentOffer, $optionsMap, &$givenDeclinationFound): bool {
            if ($currentOffer->getId() === $declination->getId()) {
                $givenDeclinationFound = true;
                // Skip the given declination, as we only want *other* offers
                return false;
            }

            $declinationOptions = $declination->getOptions();
            if (count($optionsMap) !== count($declinationOptions)) {
                // Number of options doesn't match, skip this declination
                return false;
            }

            // Search for other declinations with options 100% matching those of the given offer
            foreach ($declinationOptions as $declinationOption) {
                $referenceOption = $optionsMap[$declinationOption->getId()] ?? null;
                if ($referenceOption === null) {
                    return false;
                }

                if ($referenceOption->getVariantId() !== $declinationOption->getVariantId()) {
                    return false;
                }
            }

            return true;
        }));

        if (!$givenDeclinationFound) {
            throw new \InvalidArgumentException("The given declination does not belong to this product");
        }

        return $result;
    }

    public function getGeolocation(): ?ProductLocation
    {
        return $this->geolocation;
    }

    public function getVideo(): ?ProductVideo
    {
        return $this->video;
    }

    /**
     * @return ProductAttachment[]
     */
    public function getAttachments(): array
    {
        return $this->attachments;
    }

    public function getSeoTitle(): string
    {
        return $this->seoTitle;
    }

    public function getSeoDescription(): string
    {
        return $this->seoDescription;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    /**
     * @return Image[]
     */
    public function getImages(): array
    {
        return $this->images;
    }

    /**
     * @return string
     */
    public function getSeoKeywords(): string
    {
        return $this->seoKeywords;
    }

    public function hasInfiniteStock(): bool
    {
        return $this->infiniteStock;
    }

    /**
     * @return null|ProductOffer[]
     */
    public function getOffers(): ?array
    {
        return $this->offers;
    }
}
