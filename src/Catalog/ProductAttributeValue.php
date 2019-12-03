<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types=1);

namespace Wizaplace\SDK\Catalog;

use Wizaplace\SDK\Image\Image;

/**
 * Class ProductAttributeValue
 * @package Wizaplace\SDK\Catalog
 */
final class ProductAttributeValue
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
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->id = $data['id'];
        $this->attributeId = $data['attributeId'];
        $this->name = $data['name'];
        $this->slug = $data['slug'];
        $this->image = isset($data['image']) ? $data['image'] : null;
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return int|null
     */
    public function getAttributeId(): ?int
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
     * @return Image|null
     */
    public function getImage(): ?Image
    {
        if (null === $this->image) {
            return null;
        }

        return $this->image instanceof Image
            ? $this->image
            : new Image($this->image)
        ;
    }
}
