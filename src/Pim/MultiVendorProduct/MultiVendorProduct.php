<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types=1);

namespace Wizaplace\SDK\Pim\MultiVendorProduct;

use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Validation;
use Wizaplace\SDK\Exception\SomeParametersAreInvalid;
use function theodorejb\polycast\to_int;
use function theodorejb\polycast\to_string;

final class MultiVendorProduct
{
    const CONTEXT_CREATE = 'create';
    const CONTEXT_UPDATE = 'update';

    /** @var string */
    private $id;

    /** @var string */
    private $name;

    /** @var string */
    private $code;

    /** @var string */
    private $productTemplateType;

    /** @var string */
    private $supplierReference;

    /** @var string */
    private $slug;

    /** @var string */
    private $shortDescription;

    /** @var string */
    private $description;

    /** @var string */
    private $seoTitle;

    /** @var string */
    private $seoDescription;

    /** @var string */
    private $seoKeywords;

    /** @var MultiVendorProductStatus */
    private $status;

    /** @var int */
    private $categoryId;

    /** @var array */
    private $attributes;

    /** @var array */
    private $freeAttributes;

    /** @var array */
    private $imageIds;

    /** @var null|MultiVendorProductVideo */
    private $video;

    public function __construct(array $data = [])
    {
        $this->id = isset($data['id']) ? to_string($data['id']) : null;
        $this->name = isset($data['name']) ? to_string($data['name']) : null;
        $this->code = isset($data['code']) ? to_string($data['code']) : null;
        $this->productTemplateType = isset($data['productTemplateType']) ? to_string($data['productTemplateType']) : null;
        $this->supplierReference = isset($data['supplierReference']) ? to_string($data['supplierReference']) : null;
        $this->slug = isset($data['slug']) ? to_string($data['slug']) : null;
        $this->shortDescription = isset($data['shortDescription']) ? to_string($data['shortDescription']) : null;
        $this->description = isset($data['description']) ? to_string($data['description']) : null;
        $this->seoTitle = isset($data['seoTitle']) ? to_string($data['seoTitle']) : null;
        $this->seoDescription = isset($data['seoDescription']) ? to_string($data['seoDescription']) : null;
        $this->seoKeywords = isset($data['seoKeywords']) ? to_string($data['seoKeywords']) : null;
        $this->categoryId = isset($data['categoryId']) ? to_int($data['categoryId']) : null;
        $this->status = isset($data['status']) ? new MultiVendorProductStatus($data['status']) : null;
        $this->freeAttributes = $data['freeAttributes'] ?? null;
        $this->imageIds = $data['imageIds'] ?? null;
        $this->attributes = $data['attributes'] ?? null;
        $this->video = isset($data['video']) ? new MultiVendorProductVideo($data['video']) : null;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(?string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getProductTemplateType(): string
    {
        return $this->productTemplateType;
    }

    public function setProductTemplateType($productTemplateType): self
    {
        $this->productTemplateType = $productTemplateType;

        return $this;
    }

    public function getSupplierReference(): string
    {
        return $this->supplierReference;
    }

    public function setSupplierReference(?string $supplierReference): self
    {
        $this->supplierReference = $supplierReference;

        return $this;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getShortDescription(): string
    {
        return $this->shortDescription;
    }

    public function setShortDescription(?string $shortDescription): self
    {
        $this->shortDescription = $shortDescription;

        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getSeoTitle(): string
    {
        return $this->seoTitle;
    }

    public function setSeoTitle(?string $seoTitle): self
    {
        $this->seoTitle = $seoTitle;

        return $this;
    }

    public function getSeoDescription(): string
    {
        return $this->seoDescription;
    }

    public function setSeoDescription(?string $seoDescription): self
    {
        $this->seoDescription = $seoDescription;

        return $this;
    }

    public function getSeoKeywords(): string
    {
        return $this->seoKeywords;
    }

    public function setSeoKeywords(?string $seoKeywords): self
    {
        $this->seoKeywords = $seoKeywords;

        return $this;
    }

    public function getStatus(): MultiVendorProductStatus
    {
        return $this->status;
    }

    public function setStatus(?MultiVendorProductStatus $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getCategoryId(): int
    {
        return $this->categoryId;
    }

    public function setCategoryId(?int $categoryId): self
    {
        $this->categoryId = $categoryId;

        return $this;
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function setAttributes(?array $attributes): self
    {
        $this->attributes = $attributes;

        return $this;
    }

    public function getFreeAttributes(): array
    {
        return $this->freeAttributes;
    }

    public function setFreeAttributes(?array $freeAttributes): self
    {
        $this->freeAttributes = $freeAttributes;

        return $this;
    }

    public function getImageIds(): array
    {
        return $this->imageIds;
    }

    public function setImageIds(?array $imageIds): self
    {
        $this->imageIds = $imageIds;

        return $this;
    }

    public function getVideo():? MultiVendorProductVideo
    {
        return $this->video;
    }

    public function setVideo(?MultiVendorProductVideo $video): self
    {
        $this->video = $video;

        return $this;
    }

    /**
     * @internal
     */
    public function toArray(): array
    {
        $data = [];

        if (isset($this->code)) {
            $data['code'] = $this->code;
        }

        if (isset($this->name)) {
            $data['name'] = $this->name;
        }

        if (isset($this->supplierReference)) {
            $data['supplierReference'] = $this->supplierReference;
        }

        if (isset($this->slug)) {
            $data['slug'] = $this->slug;
        }

        if (isset($this->shortDescription)) {
            $data['shortDescription'] = $this->shortDescription;
        }

        if (isset($this->description)) {
            $data['description'] = $this->description;
        }

        if (isset($this->seoTitle)) {
            $data['seoTitle'] = $this->seoTitle;
        }

        if (isset($this->seoDescription)) {
            $data['seoDescription'] = $this->seoDescription;
        }

        if (isset($this->seoKeywords)) {
            $data['seoKeywords'] = $this->seoKeywords;
        }

        if (isset($this->status)) {
            $data['status'] = $this->status->getValue();
        }

        if (isset($this->categoryId)) {
            $data['categoryId'] = $this->categoryId;
        }

        if (isset($this->attributes)) {
            $data['attributes'] = $this->attributes;
        }

        if (isset($this->freeAttributes)) {
            $data['freeAttributes'] = $this->freeAttributes;
        }

        if (isset($this->imageIds)) {
            $data['imageIds'] = $this->imageIds;
        }

        return $data;
    }

    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        // "Create" constraints
        $metadata->addPropertyConstraints('name', [new NotNull(['groups' => self::CONTEXT_CREATE])]);
        $metadata->addPropertyConstraints('categoryId', [new NotNull(['groups' => self::CONTEXT_CREATE])]);

        // "Update constraints"
        $metadata->addPropertyConstraints('id', [new NotNull(['groups' => self::CONTEXT_UPDATE])]);
    }

    public function validate(string $context = self::CONTEXT_CREATE)
    {
        $builder = Validation::createValidatorBuilder()
            ->addMethodMapping('loadValidatorMetadata');

        $validator = $builder->getValidator()
            ->startContext();

        $validator->validate($this, null, [$context]);
        $violations = $validator->getViolations();

        if (count($violations) > 0) {
            throw new SomeParametersAreInvalid(
                'Multi Vendor Product data validation failed: '.json_encode(array_map(function (ConstraintViolationInterface $violation): array {
                    return [
                        'field' => $violation->getPropertyPath(),
                        'message' => $violation->getMessage(),
                    ];
                }, iterator_to_array($violations)))
            );
        }
    }
}
