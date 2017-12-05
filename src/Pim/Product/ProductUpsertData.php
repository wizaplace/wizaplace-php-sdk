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

    /** @var array */
    private $freeAttributes;

    /** @var bool */
    private $hasFreeShipping;

    /** @var float */
    private $weight;

    /** @var bool */
    private $isDownloadable;

    /** @var null|string|UriInterface */
    private $affiliateLink;

    /** @var null|string|UriInterface|ProductImageUpload */
    private $mainImage;

    /** @var (UriInterface|ProductImageUpload)[] */
    private $additionalImages = [];

    /** @var string */
    private $fullDescription;

    /** @var string */
    private $shortDescription;

    /** @var int[] */
    private $taxIds;

    /** @var ProductDeclinationUpsertData[] */
    private $declinations = [];

    /** @var ProductAttachmentUpload[] */
    private $attachments = [];

    /**
     * @param string $code
     * @return static
     */
    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    /**
     * @param string $supplierReference
     * @return static
     */
    public function setSupplierReference(string $supplierReference): self
    {
        $this->supplierReference = $supplierReference;

        return $this;
    }

    /**
     * @param string $name
     * @return static
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @param ProductStatus $status
     * @return static
     */
    public function setStatus(ProductStatus $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @param int $mainCategoryId
     * @return static
     */
    public function setMainCategoryId(int $mainCategoryId): self
    {
        $this->mainCategoryId = $mainCategoryId;

        return $this;
    }

    /**
     * @param float $greenTax
     * @return static
     */
    public function setGreenTax(float $greenTax): self
    {
        $this->greenTax = $greenTax;

        return $this;
    }

    /**
     * @param bool $isBrandNew
     * @return static
     */
    public function setIsBrandNew(bool $isBrandNew): self
    {
        $this->isBrandNew = $isBrandNew;

        return $this;
    }

    /**
     * @param null|ProductGeolocationUpsertData $geolocation
     * @return static
     */
    public function setGeolocation(?ProductGeolocationUpsertData $geolocation): self
    {
        $this->geolocation = $geolocation;

        return $this;
    }

    /**
     * @param array $freeAttributes
     * @return static
     */
    public function setFreeAttributes(array $freeAttributes): self
    {
        $this->freeAttributes = $freeAttributes;

        return $this;
    }

    /**
     * @param bool $hasFreeShipping
     * @return static
     */
    public function setHasFreeShipping(bool $hasFreeShipping): self
    {
        $this->hasFreeShipping = $hasFreeShipping;

        return $this;
    }

    /**
     * @param float $weight
     * @return static
     */
    public function setWeight(float $weight): self
    {
        $this->weight = $weight;

        return $this;
    }

    /**
     * @param bool $isDownloadable
     * @return static
     */
    public function setIsDownloadable(bool $isDownloadable): self
    {
        $this->isDownloadable = $isDownloadable;

        return $this;
    }

    /**
     * @param null|string|UriInterface $affiliateLink
     * @return static
     */
    public function setAffiliateLink($affiliateLink): self
    {
        $this->affiliateLink = $affiliateLink;

        return $this;
    }

    /**
     * @param null|string|UriInterface|ProductImageUpload $mainImage
     * @return static
     */
    public function setMainImage($mainImage): self
    {
        $this->mainImage = $mainImage;

        return $this;
    }

    /**
     * @param (string|UriInterface|ProductImageUpload)[] $additionalImages
     * @return static
     */
    public function setAdditionalImages(array $additionalImages): self
    {
        $this->additionalImages = $additionalImages;

        return $this;
    }

    /**
     * @param string $fullDescription
     * @return static
     */
    public function setFullDescription(string $fullDescription): self
    {
        $this->fullDescription = $fullDescription;

        return $this;
    }

    /**
     * @param string $shortDescription
     * @return static
     */
    public function setShortDescription(string $shortDescription): self
    {
        $this->shortDescription = $shortDescription;

        return $this;
    }

    /**
     * @param int[] $taxIds
     * @return static
     */
    public function setTaxIds(array $taxIds): self
    {
        $this->taxIds = $taxIds;

        return $this;
    }

    /**
     * @param ProductDeclinationUpsertData[] $declinations
     * @return static
     */
    public function setDeclinations(array $declinations): self
    {
        $this->declinations = $declinations;

        return $this;
    }

    /**
     * @param ProductAttachmentUpload[] $attachments
     * @return static
     */
    public function setAttachments(array $attachments): self
    {
        $this->attachments = $attachments;

        return $this;
    }

    /**
     * @internal
     * @throws SomeParametersAreInvalid
     */
    public function validate(): void
    {
        $validator = Validation::createValidatorBuilder()->addMethodMapping('loadValidatorMetadata')->getValidator()->startContext();

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
     * @internal
     */
    public static function loadValidatorMetadata(ClassMetadata $metadata): void
    {
        // @TODO: find something more maintainable than this array of strings...
        $nullableProperties = [
            'geolocation',
            'affiliateLink',
            'mainImage',
        ];
        $selfValidatingProperties = [
            'geolocation',
            'mainImage',
            'declinations',
        ];
        foreach ($metadata->getReflectionClass()->getProperties() as $prop) {
            if (!in_array($prop->getName(), $nullableProperties)) {
                $metadata->addPropertyConstraint($prop->getName(), new Constraints\NotNull());
            }
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
        $data = [
            'product_code' => $this->code,
            'supplier_ref' => $this->supplierReference,
            'product' => $this->name,
            'status' => $this->status->getValue(),
            'main_category' => $this->mainCategoryId,
            'green_tax' => $this->greenTax,
            'condition' => $this->isBrandNew ? 'N' : 'U',
            'free_shipping' => $this->hasFreeShipping ? 'Y' : 'N',
            'weight' => $this->weight,
            'is_edp' => $this->isDownloadable ? 'Y' : 'N',
            'full_description' => $this->fullDescription,
            'short_description' => $this->shortDescription,
            'tax_ids' => $this->taxIds,
            'free_features' => $this->freeAttributes,
            'inventory' => array_map(function (ProductDeclinationUpsertData $data): array {
                return $data->toArray();
            }, $this->declinations),
            'image_pairs' => array_map([self::class, 'imageToArray'], $this->additionalImages),
            'attachments' => array_map(function (ProductAttachmentUpload $data): array {
                return $data->toArray();
            }, $this->attachments),
        ];

        if ($this->affiliateLink !== null) {
            $data['affiliate_link'] = to_string($this->affiliateLink);
        }

        if ($this->geolocation !== null) {
            $data['geolocation'] = $this->geolocation->toArray();
        }

        if ($this->mainImage !== null) {
            $data['main_pair'] = self::imageToArray($this->mainImage);
        }

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

        return $data;
    }

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
