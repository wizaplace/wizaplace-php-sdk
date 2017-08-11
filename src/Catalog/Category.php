<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\Catalog;

use Wizaplace\Image\Image;

class Category
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
}
