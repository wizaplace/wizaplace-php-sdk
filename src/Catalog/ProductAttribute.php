<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\SDK\Catalog;

final class ProductAttribute
{
    /** @var int|null */
    private $id;
    /** @var string */
    private $name;
    /** @var null|string|array */
    private $value;
    /** @var int[] */
    private $valueIds;
    /** @var ProductAttribute[] */
    private $children;
    /** @var string[] */
    private $imageUrls;
    /** @var AttributeType */
    private $type;

    /**
     * @internal
     */
    public function __construct(array $data)
    {
        $this->id = $data['id'];
        $this->name = (string) $data['name'];
        $this->value = $data['value'];
        $this->valueIds = $data['valueIds'];
        $this->imageUrls = $data['imageUrls'] ?? [];
        $this->children = array_map(static function (array $childrenData) : self {
            return new self($childrenData);
        }, $data['children']);
        $this->type = new AttributeType($data['type']);
    }

    /**
     * @return ProductAttribute[]
     */
    public function getChildren(): array
    {
        return $this->children;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return null|string|array
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return int[]
     */
    public function getValueIds(): array
    {
        return $this->valueIds;
    }

    /**
     * @return string[]
     */
    public function getImageUrls(): array
    {
        return $this->imageUrls;
    }

    public function getType(): AttributeType
    {
        return $this->type;
    }
}
