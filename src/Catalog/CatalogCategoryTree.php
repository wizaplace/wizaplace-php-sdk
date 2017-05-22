<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */

namespace Wizaplace\Catalog;

class CatalogCategoryTree
{
    /** @var  CatalogCategory */
    private $category;
    /** @var  CatalogCategoryTree[] */
    private $children;

    public function __construct(array $data)
    {
        $this->category = new CatalogCategory($data['category']);
        $this->children = array_map(
            function ($data) {
                return new self($data);
            },
            $data['children']
        );
    }

    public function getCategory(): CatalogCategory
    {
        return $this->category;
    }

    /**
     * @return CatalogCategoryTree[]
     */
    public function getChildren(): array
    {
        return $this->children;
    }
}
