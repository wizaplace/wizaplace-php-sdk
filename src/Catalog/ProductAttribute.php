<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\SDK\Catalog;

use function theodorejb\polycast\to_string;

/**
 * Class ProductAttribute
 * @package Wizaplace\SDK\Catalog
 */
final class ProductAttribute
{
    /** @var int|null */
    private $id;

    /** @var string */
    private $name;

    /**
     * @deprecated
     * @var null|string|array
     */
    private $value;

    /**
     * @deprecated
     * @var int[]
     */
    private $valueIds;

    /** @var ProductAttribute[] */
    private $children;

    /**
     * @deprecated
     * @var string[]
     */
    private $imageUrls;

    /** @var AttributeType */
    private $type;

    /** @var null|ProductAttributeValue[] */
    private $values;

    /**
     * @internal
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->id = $data['id'];
        $this->name = to_string($data['name']);
        $this->value = $data['value'];
        $this->valueIds = $data['valueIds'];
        $this->imageUrls = $data['imageUrls'] ?? [];
        $this->children = array_map(static function (array $childrenData) : self {
            return new self($childrenData);
        }, $data['children']);
        $this->type = new AttributeType($data['type']);
        if (isset($data['values'])) {
            $this->values = array_map(function (array $valueData): ProductAttributeValue {
                return new ProductAttributeValue($valueData);
            }, $data['values'] ?? []);
        }
    }

    /**
     * @return ProductAttribute[]
     */
    public function getChildren(): array
    {
        return $this->children;
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @deprecated
     * @return null|string|array
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @deprecated
     * @return int[]
     */
    public function getValueIds(): array
    {
        return $this->valueIds;
    }

    /**
     * @deprecated
     * @return string[]
     */
    public function getImageUrls(): array
    {
        return $this->imageUrls;
    }

    /**
     * @return AttributeType
     */
    public function getType(): AttributeType
    {
        return $this->type;
    }

    /**
     * @return null|ProductAttributeValue[]
     */
    public function getValues(): ?array
    {
        return $this->values;
    }
}
