<?php

/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Catalog;

use Wizaplace\SDK\Image\Image;

/**
 * Class Category
 * @package Wizaplace\SDK\Catalog
 */
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
    /** @var string */
    private $seoKeywords;
    /** @var CategorySummary[]  */
    private $categoryPath;

    /**
     * @internal
     *
     * @param array $data
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
        $this->seoKeywords = $data['seoData']['keywords'] ?? '';
        $this->categoryPath = array_map(
            static function (array $path): CategorySummary {
                return new CategorySummary($path);
            },
            $data['categoryPath'] ?? []
        );
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return int|null
     */
    public function getParentId(): ?int
    {
        return $this->parentId;
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
    public function getDescription(): string
    {
        return $this->description;
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
        return $this->image;
    }

    /**
     * @return int
     */
    public function getPosition(): int
    {
        return $this->position;
    }

    /**
     * @return int
     */
    public function getProductCount(): int
    {
        return $this->productCount;
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

    /**
     * @return string
     */
    public function getSeoKeywords(): string
    {
        return $this->seoKeywords;
    }

    /**
     * @return CategorySummary[]
     */
    public function getCategoryPath(): array
    {
        return $this->categoryPath;
    }
}
