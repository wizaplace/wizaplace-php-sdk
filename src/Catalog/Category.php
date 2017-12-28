<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\SDK\Catalog;

use Wizaplace\SDK\Image\Image;

final class Category
{
    /** @var int */
    private $id;
    /** @var null|int */
    private $parentId;
    /** @var string */
    private $name;
    /** @var string */
    private $description;
    /** @var string */
    private $slug;
    /** @var null|Image */
    private $image;
    /** @var int */
    private $position;
    /** @var int */
    private $productCount;
    /** @var string */
    private $seoTitle;
    /** @var string */
    private $seoDescription;

    /**
     * @internal
     */
    public function __construct(array $data)
    {
        $this->id = $data['id'];
        $this->parentId = $data['parentId'];
        $this->name = $data['name'];
        $this->description = $data['description'];
        $this->slug = $data['slug'];
        $this->image = isset($data['image']) ? new Image($data['image']) : null;
        $this->position = $data['position'];
        $this->productCount = $data['productCount'];
        $this->seoTitle = $data['seoData']['title'] ?? '';
        $this->seoDescription = $data['seoData']['description'] ?? '';
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getParentId(): ?int
    {
        return $this->parentId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getImage(): ?Image
    {
        return $this->image;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function getProductCount(): int
    {
        return $this->productCount;
    }

    public function getSeoTitle(): string
    {
        return $this->seoTitle;
    }

    public function getSeoDescription(): string
    {
        return $this->seoDescription;
    }
}
