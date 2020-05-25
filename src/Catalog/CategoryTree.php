<?php

/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Catalog;

/**
 * Class CategoryTree
 * @package Wizaplace\SDK\Catalog
 */
final class CategoryTree
{
    /** @var Category */
    private $category;
    /** @var CategoryTree[] */
    private $children;

    /**
     * @internal
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->category = new Category($data['category']);
        $this->children = self::buildCollection($data['children']);
    }

    /**
     * @return Category
     */
    public function getCategory(): Category
    {
        return $this->category;
    }

    /**
     * @return CategoryTree[]
     */
    public function getChildren(): array
    {
        return $this->children;
    }

    public function setChildren(array $children): void
    {
        $this->children = $children;
    }

    /**
     * @internal
     * @param array $data
     * @return CategoryTree[]
     */
    public static function buildCollection(array $data): array
    {
        return array_map(
            static function (array $itemData): self {
                return new self($itemData);
            },
            $data
        );
    }
}
