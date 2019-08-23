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
use Wizaplace\SDK\Pim\Product\ExtendedPriceTier;
use Wizaplace\SDK\Pim\Product\PriceTier;
use function theodorejb\polycast\to_float;
use function theodorejb\polycast\to_string;

/**
 * Class Product
 * @package Wizaplace\SDK\Catalog
 */
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

    /** @var \DateTimeImmutable|null */
    private $availableSince;

    /**
     * @var Image[]
     */
    private $images;

    /** @var bool */
    private $infiniteStock;

    /** @var null|ProductOffer[] */
    private $offers;

    /** @var null|string */
    private $productTemplateType;

    /** @var bool */
    protected $isMvp;

    /** @var PriceTier[] */
    protected $priceTiers;

    /**
     * @internal
     *
     * @param array        $data
     * @param UriInterface $apiBaseUrl
     */
    public function __construct(array $data, UriInterface $apiBaseUrl)
    {
        $this->id = to_string($data['id']);
        $this->isMvp = false === is_numeric($this->id);
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
        $this->declinations = array_map(function (array $declination) : Declination {
            // Only available for a product
            // For a MVP we're not able to know the shippings
            if (false === $this->isMvp()) {
                $declination['shippings'] = $this->shippings;
            }

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
        $this->availableSince = isset($data['availableSince']) ? \DateTimeImmutable::createFromFormat(\DateTime::RFC3339, $data['availableSince']) : null;
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
        $this->priceTiers = [];

        if (array_key_exists('price_tiers', $data)) {
            foreach ($data['price_tiers'] as $priceTier) {
                $this->addPriceTier($priceTier);
            }
        }

        $this->productTemplateType = $data['productTemplateType'] ?? null;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * @return string
     */
    public function getSupplierReference(): string
    {
        return $this->supplierReference;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getShortDescription(): string
    {
        return $this->shortDescription;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @return string
     */
    public function getSlug(): string
    {
        return $this->slug;
    }

    /**
     * @return float
     */
    public function getMinPrice(): float
    {
        return $this->minPrice;
    }

    /**
     * @return float
     */
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

    /**
     * @return bool
     */
    public function isTransactional(): bool
    {
        return $this->isTransactional;
    }

    /**
     * @return float
     */
    public function getWeight(): float
    {
        return $this->weight;
    }

    /**
     * @return float|null
     */
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
     *
     * @return Declination
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
     *
     * @param Declination $currentOffer
     *
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

    /**
     * @return ProductLocation|null
     */
    public function getGeolocation(): ?ProductLocation
    {
        return $this->geolocation;
    }

    /**
     * @return ProductVideo|null
     */
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

    /**
     * @return string
     */
    public function getSeoTitle(): string
    {
        return $this->seoTitle;
    }

    /**
     * @return string
     */
    public function getSeoDescription(): string
    {
        return $this->seoDescription;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function getAvailableSince(): ?\DateTimeInterface
    {
        return $this->availableSince;
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

    /**
     * @return bool
     */
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

    /**
     * @return null|string
     */
    public function getProductTemplateType(): ?string
    {
        return $this->productTemplateType;
    }

    public function isMvp(): bool
    {
        return $this->isMvp;
    }

    public function addPriceTier(array $priceTier): void
    {
        if (isset($priceTier['price'])) {
            $this->priceTiers[] = new PriceTier($priceTier);
        } else {
            $this->priceTiers[] = new ExtendedPriceTier($priceTier);
        }
    }

    /** @return PriceTier[] */
    public function getPriceTier(): array
    {
        return $this->priceTiers;
    }
}
