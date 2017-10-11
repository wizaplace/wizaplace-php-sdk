<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\SDK\Catalog;

final class SearchProductAttribute
{
    /** @var int|null */
    private $id;
    /** @var string */
    private $name;
    /** @var string */
    private $slug;
    /** @var array */
    private $values;
    /** @var AttributeType */
    private $type;

    /**
     * @internal
     */
    public function __construct($data)
    {
        $this->id = $data['attribute']['id'];
        $this->name = $data['attribute']['name'];
        $this->slug = $data['attribute']['slug'] ?? '';
        $this->values = $data['values'] ?? []; // @TODO : use ProductAttributeValue[]
        $this->type = AttributeType::createFromLegacyMapping($data['attribute']['type']);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getValues(): array
    {
        return $this->values;
    }

    public function getType(): AttributeType
    {
        return $this->type;
    }
}
