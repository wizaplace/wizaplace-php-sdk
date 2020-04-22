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

    /**
     * @internal
     * @param array $data
     * @return CategoryTree[]
     */
    public static function buildCollection(array $data): array
    {
        $collection = array_map(
            static function (array $itemData): self {
                return new self($itemData);
            },
            $data
        );

        usort(
            $collection,
            static function (CategoryTree $itemA, CategoryTree $itemB): int {
                return $itemA->getCategory()->getPosition() <=> $itemB->getCategory()->getPosition();
            }
        );

        return $collection;
    }
}
