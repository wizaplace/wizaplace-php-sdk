<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\Catalog;

class CategoryTree
{
    /** @var Category */
    private $category;
    /** @var CategoryTree[] */
    private $children;

    public function __construct(array $data)
    {
        $this->category = new Category($data['category']);
        $this->children = array_map(
            function ($data) {
                return new self($data);
            },
            $data['children']
        );
    }

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
}
