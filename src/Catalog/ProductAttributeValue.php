<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types=1);

namespace Wizaplace\SDK\Catalog;

use Wizaplace\SDK\Image\Image;

final class ProductAttributeValue implements \JsonSerializable
{
    /** @var null|int */
    private $id;
    /** @var null|int */
    private $attributeId;
    /** @var string */
    private $name;
    /** @var string */
    private $slug;
    /** @var null|Image */
    private $image;

    /**
     * @internal
     */
    public function __construct(array $data)
    {
        $this->id = $data['id'];
        $this->attributeId = $data['attributeId'];
        $this->name = $data['name'];
        $this->slug = $data['slug'];
        $this->image = isset($data['image']) ? new Image($data['image']) : null;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAttributeId(): ?int
    {
        return $this->attributeId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getImage(): ?Image
    {
        return $this->image;
    }

    /**
     * @inheritdoc
     */
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->getId(),
            'attributeId' => $this->getAttributeId(),
            'name' => $this->getName(),
            'slug' => $this->getSlug(),
            'image' => $this->getImage(),
        ];
    }
}
