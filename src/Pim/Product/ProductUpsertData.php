<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types=1);

namespace Wizaplace\SDK\Pim\Product;

use Psr\Http\Message\UriInterface;
use Symfony\Component\Validator\Constraints;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Validation;
use Wizaplace\SDK\Exception\SomeParametersAreInvalid;
use function theodorejb\polycast\to_string;

/**
 * Class ProductUpsertData
 * @package Wizaplace\SDK\Pim\Product
 *
 * @internal
 */
abstract class ProductUpsertData
{
    /** @var string */
    private $code;

    /** @var string */
    private $supplierReference;

    /** @var string */
    private $name;

    /** @var ProductStatus */
    private $status;

    /** @var int */
    private $mainCategoryId;

    /** @var float */
    private $greenTax;

    /** @var bool */
    private $isBrandNew;

    /** @var null|ProductGeolocationUpsertData */
    private $geolocation;

    /** @var null|array */
    private $freeAttributes;

    /** @var null|bool */
    private $hasFreeShipping;

    /** @var float */
    private $weight;

    /** @var null|bool */
    private $isDownloadable;

    /** @var null|string|UriInterface */
    private $affiliateLink;

    /** @var null|string|UriInterface|ProductImageUpload */
    private $mainImage;

    /** @var null|(UriInterface|ProductImageUpload)[] */
    private $additionalImages;

    /** @var string */
    private $fullDescription;

    /** @var string */
    private $shortDescription;

    /** @var int[] */
    private $taxIds;

    /** @var ProductDeclinationUpsertData[] */
    private $declinations;

    /** @var ProductAttachmentUpload[] */
    private $attachments;

    /** @var \DateTimeImmutable */
    private $availabilityDate;

    /** @var null|bool */
    private $infiniteStock;

    /** @var null|string */
    private $productTemplateType;

    /** @var int */
    private $maxPriceAdjustment;

    /** @var null|bool */
    private $isSubscription;

    /** @var null|bool */
    private $isRenewable;

    /**
     * @param string $code
     * @return $this
     */
    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    /**
     * @param string $supplierReference
     * @return $this
     */
    public function setSupplierReference(string $supplierReference): self
    {
        $this->supplierReference = $supplierReference;

        return $this;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @param ProductStatus $status
     * @return $this
     */
    public function setStatus(ProductStatus $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @param int $mainCategoryId
     * @return $this
     */
    public function setMainCategoryId(int $mainCategoryId): self
    {
        $this->mainCategoryId = $mainCategoryId;

        return $this;
    }

    /**
     * @param float $greenTax
     * @return $this
     */
    public function setGreenTax(float $greenTax): self
    {
        $this->greenTax = $greenTax;

        return $this;
    }

    /**
     * @param bool $isBrandNew
     * @return $this
     */
    public function setIsBrandNew(bool $isBrandNew): self
    {
        $this->isBrandNew = $isBrandNew;

        return $this;
    }

    /**
     * @param null|ProductGeolocationUpsertData $geolocation
     * @return $this
     */
    public function setGeolocation(?ProductGeolocationUpsertData $geolocation): self
    {
        $this->geolocation = $geolocation;

        return $this;
    }

    /**
     * @param null|array $freeAttributes
     * @return $this
     */
    public function setFreeAttributes(?array $freeAttributes): self
    {
        $this->freeAttributes = $freeAttributes;

        return $this;
    }

    /**
     * @param null|bool $hasFreeShipping
     * @return $this
     */
    public function setHasFreeShipping(?bool $hasFreeShipping): self
    {
        $this->hasFreeShipping = $hasFreeShipping;

        return $this;
    }

    /**
     * @param float $weight
     * @return $this
     */
    public function setWeight(float $weight): self
    {
        $this->weight = $weight;

        return $this;
    }

    /**
     * @param null|bool $isDownloadable
     * @return $this
     */
    public function setIsDownloadable(?bool $isDownloadable): self
    {
        $this->isDownloadable = $isDownloadable;

        return $this;
    }

    /**
     * @param null|bool $infiniteStock
     * @return $this
     */
    public function setInfiniteStock(?bool $infiniteStock): self
    {
        $this->infiniteStock = $infiniteStock;

        return $this;
    }

    /**
     * @param null|string|UriInterface $affiliateLink
     * @return $this
     */
    public function setAffiliateLink($affiliateLink): self
    {
        $this->affiliateLink = $affiliateLink;

        return $this;
    }

    /**
     * @param null|string|UriInterface|ProductImageUpload $mainImage
     * @return $this
     */
    public function setMainImage($mainImage): self
    {
        $this->mainImage = $mainImage;

        return $this;
    }

    /**
     * @param (null|string|UriInterface|ProductImageUpload)[] $additionalImages
     * @return $this
     */
    public function setAdditionalImages(?array $additionalImages): self
    {
        $this->additionalImages = $additionalImages;

        return $this;
    }

    /**
     * @param string $fullDescription
     * @return $this
     */
    public function setFullDescription(string $fullDescription): self
    {
        $this->fullDescription = $fullDescription;

        return $this;
    }

    /**
     * @param string $shortDescription
     * @return $this
     */
    public function setShortDescription(string $shortDescription): self
    {
        $this->shortDescription = $shortDescription;

        return $this;
    }

    /**
     * @param int[] $taxIds
     * @return $this
     */
    public function setTaxIds(array $taxIds): self
    {
        $this->taxIds = $taxIds;

        return $this;
    }

    /**
     * @param ProductDeclinationUpsertData[] $declinations
     * @return $this
     */
    public function setDeclinations(array $declinations): self
    {
        $this->declinations = $declinations;

        return $this;
    }

    /**
     * @param ProductAttachmentUpload[] $attachments
     * @return $this
     */
    public function setAttachments(array $attachments): self
    {
        $this->attachments = $attachments;

        return $this;
    }

    /**
     * @param \DateTimeInterface $availabilityDate
     *
     * @return $this
     * @throws \Exception
     */
    public function setAvailabilityDate(\DateTimeInterface $availabilityDate): self
    {
        if ($availabilityDate instanceof \DateTimeImmutable) {
            $this->availabilityDate = $availabilityDate;
        } else {
            $this->availabilityDate = new \DateTimeImmutable('@'.$availabilityDate->getTimestamp());
        }

        return $this;
    }

    /**
     * @param int $maxPriceAdjustment
     * @return $this
     */
    public function setMaxPriceAdjustment(int $maxPriceAdjustment): self
    {
        $this->maxPriceAdjustment = $maxPriceAdjustment;

        return $this;
    }

    /**
     * @param null|string $productTemplateType
     * @return $this
     */
    public function setProductTemplateType(?string $productTemplateType): self
    {
        $this->productTemplateType = $productTemplateType;

        return $this;
    }

    /**
     * @param bool $isSubscription
     *
     * @return ProductUpsertData
     */
    public function setIsSubscription(bool $isSubscription): self
    {
        $this->isSubscription = $isSubscription;

        return $this;
    }

    /**
     * @param bool $isRenewable
     *
     * @return ProductUpsertData
     */
    public function setIsRenewable(bool $isRenewable): self
    {
        $this->isRenewable = $isRenewable;

        return $this;
    }

    /**
     * @internal
     * @throws SomeParametersAreInvalid
     */
    public function validate(): void
    {
        $builder = Validation::createValidatorBuilder()
            ->addMethodMapping('loadValidatorMetadata');

        if (!static::allowsPartialData()) {
            $builder->addMethodMapping('loadNullChecksValidatorMetadata');
        }

        $validator = $builder->getValidator()->startContext();

        $validator->validate($this);

        $violations = $validator->getViolations();

        if (count($violations) > 0) {
            throw new SomeParametersAreInvalid('Product data validation failed: '.json_encode(array_map(function (ConstraintViolationInterface $violation): array {
                return [
                    'field' => $violation->getPropertyPath(),
                    'message' => $violation->getMessage(),
                ];
            }, iterator_to_array($violations))));
        }
    }

    /**
     * Adds NotNull constraints on most properties.
     * @internal
     *
     * @param ClassMetadata $metadata
     */
    public static function loadNullChecksValidatorMetadata(ClassMetadata $metadata): void
    {
        // @TODO: find something more maintainable than this array of strings...
        $nullableProperties = [
            'geolocation',
            'affiliateLink',
            'mainImage',
            'attachments',
            'availabilityDate',
            'freeAttributes',
            'hasFreeShipping',
            'isDownloadable',
            'additionalImages',
            'infiniteStock',
            'productTemplateType',
            'maxPriceAdjustment',
            'isSubscription',
            'isRenewable',
        ];

        foreach ($metadata->getReflectionClass()->getProperties() as $prop) {
            if (!in_array($prop->getName(), $nullableProperties)) {
                $metadata->addPropertyConstraint($prop->getName(), new Constraints\NotNull());
            }
        }
    }

    /**
     * @internal
     *
     * @param ClassMetadata $metadata
     */
    public static function loadValidatorMetadata(ClassMetadata $metadata): void
    {
        // @TODO: find something more maintainable than this array of strings...
        $selfValidatingProperties = [
            'geolocation',
            'mainImage',
            'declinations',
            'additionalImages',
        ];

        foreach ($metadata->getReflectionClass()->getProperties() as $prop) {
            if (in_array($prop->getName(), $selfValidatingProperties)) {
                $metadata->addPropertyConstraint($prop->getName(), new Constraints\Valid());
            }
        }
    }

    /**
     * @internal
     * @return array
     */
    public function toArray(): array
    {
        $data = [];

        if (isset($this->code)) {
            $data['product_code'] = $this->code;
        }

        if (isset($this->supplierReference)) {
            $data['supplier_ref'] = $this->supplierReference;
        }

        if (isset($this->name)) {
            $data['product'] = $this->name;
        }

        if (isset($this->status)) {
            $data['status'] = $this->status->getValue();
        }

        if (isset($this->mainCategoryId)) {
            $data['main_category'] = $this->mainCategoryId;
        }

        if (isset($this->greenTax)) {
            $data['green_tax'] = $this->greenTax;
        }

        if (isset($this->isBrandNew)) {
            $data['condition'] = $this->isBrandNew ? 'N' : 'U';
        }

        if (isset($this->hasFreeShipping)) {
            $data['free_shipping'] = $this->hasFreeShipping ? 'Y' : 'N';
        }

        if (isset($this->weight)) {
            $data['weight'] = $this->weight;
        }

        if (isset($this->isDownloadable)) {
            $data['is_edp'] = $this->isDownloadable ? 'Y' : 'N';
        }

        if (isset($this->fullDescription)) {
            $data['full_description'] = $this->fullDescription;
        }

        if (isset($this->shortDescription)) {
            $data['short_description'] = $this->shortDescription;
        }

        if (isset($this->taxIds)) {
            $data['tax_ids'] = $this->taxIds;
        }

        if (isset($this->freeAttributes)) {
            $data['free_features'] = $this->freeAttributes;
        }

        if (isset($this->infiniteStock)) {
            $data['infinite_stock'] = $this->infiniteStock;
        }

        if (isset($this->maxPriceAdjustment)) {
            $data['max_price_adjustment'] = $this->maxPriceAdjustment;
        }

        if (isset($this->declinations)) {
            $data['inventory'] = array_map(function (ProductDeclinationUpsertData $data): array {
                return $data->toArray();
            }, $this->declinations);

            $allowedOptionsVariants = [];
            foreach ($data['inventory'] as $inventory) {
                foreach ($inventory['combination'] as $optionId => $variantId) {
                    $allowedOptionsVariants[$optionId][] = $variantId;
                }
            }
            $data['allowed_options_variants'] = [];
            foreach ($allowedOptionsVariants as $optionId => $variantIds) {
                $data['allowed_options_variants'][] = [
                    'option_id' => $optionId,
                    'variants' => array_unique($variantIds),
                ];
            }
        }

        if (isset($this->additionalImages)) {
            $data['image_pairs'] = array_map([self::class, 'imageToArray'], $this->additionalImages);
        }

        if (isset($this->attachments)) {
            $data['attachments'] = array_map(function (ProductAttachmentUpload $data): array {
                return $data->toArray();
            }, $this->attachments);
        }

        if (isset($this->affiliateLink)) {
            $data['affiliate_link'] = to_string($this->affiliateLink);
        }

        if (isset($this->geolocation)) {
            $data['geolocation'] = $this->geolocation->toArray();
        }

        if (isset($this->mainImage)) {
            $data['main_pair'] = self::imageToArray($this->mainImage);
        }

        if (isset($this->availabilityDate)) {
            $data['avail_since'] = $this->availabilityDate->getTimestamp();
        }

        if (isset($this->productTemplateType)) {
            $data['product_template_type'] = to_string($this->productTemplateType);
        }

        if (is_bool($this->isSubscription)) {
            $data['is_subscription'] = $this->isSubscription;
        }

        if (is_bool($this->isRenewable)) {
            $data['is_renewable'] = $this->isRenewable;
        }

        return $data;
    }

    /**
     * @return bool
     */
    abstract protected static function allowsPartialData(): bool;

    /**
     * @param ProductImageUpload|string $image
     *
     * @return array
     */
    private static function imageToArray($image): array
    {
        if ($image instanceof ProductImageUpload) {
            return [
                'detailed' => $image->toArray(),
            ];
        }

        // direct link
        return [
            'detailed' => [
                'image_path' => to_string($image),
            ],
        ];
    }
}
