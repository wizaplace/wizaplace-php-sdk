<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\Catalog;

use Wizaplace\Image\Image;

final class AttributeVariant
{
    /** @var int */
    private $id;
    /** @var int */
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
    public function __construct(int $id, int $attributeId, string $name, string $slug, ?Image $image)
    {
        $this->id = $id;
        $this->attributeId = $attributeId;
        $this->name = $name;
        $this->slug = $slug;
        $this->image = $image;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getAttributeId(): int
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
}
