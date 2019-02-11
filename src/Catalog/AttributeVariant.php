<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\SDK\Catalog;

use Wizaplace\SDK\Image\Image;

/**
 * Class AttributeVariant
 * @package Wizaplace\SDK\Catalog
 */
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
    /** @var string */
    private $description;
    /** @var null|Image */
    private $image;
    /** @var string */
    private $seoTitle;
    /** @var string */
    private $seoDescription;

    /**
     * @internal
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->id = $data['id'];
        $this->attributeId = $data['attributeId'];
        $this->name = $data['name'];
        $this->slug = $data['slug'];
        $this->description = $data['description'];
        $this->image = isset($data['image']) ? new Image($data['image']) : null;
        $this->seoTitle = $data['seoData']['title'] ?? '';
        $this->seoDescription = $data['seoData']['description'] ?? '';
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getAttributeId(): int
    {
        return $this->attributeId;
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
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @return Image|null
     */
    public function getImage(): ?Image
    {
        return $this->image;
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
}
