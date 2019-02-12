<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\SDK\Catalog;

/**
 * Class SearchProductAttribute
 * @package Wizaplace\SDK\Catalog
 */
final class SearchProductAttribute
{
    /** @var int|null */
    private $id;
    /** @var string */
    private $name;
    /** @var string */
    private $slug;
    /** @var ProductAttributeValue[] */
    private $values;
    /** @var AttributeType */
    private $type;

    /**
     * @internal
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->id = $data['attribute']['id'];
        $this->name = $data['attribute']['name'];
        $this->slug = $data['attribute']['slug'] ?? '';
        $this->values = array_map(static function (array $valueData): ProductAttributeValue {
            return new ProductAttributeValue($valueData);
        }, $data['values'] ?? []);
        $this->type = AttributeType::createFromLegacyMapping($data['attribute']['type']);
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
     * @return string
     */
    public function getSlug(): string
    {
        return $this->slug;
    }

    /**
     * @return ProductAttributeValue[]
     */
    public function getValues(): array
    {
        return $this->values;
    }

    /**
     * @return AttributeType
     */
    public function getType(): AttributeType
    {
        return $this->type;
    }
}
