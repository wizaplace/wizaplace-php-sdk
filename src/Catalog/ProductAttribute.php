<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */

namespace Wizaplace\Catalog;

class ProductAttribute
{
    /** @var  string */
    private $name;
    /** @var  string[] */
    private $value;
    /** @var  ProductAttribute[] */
    private $children;
    /** @var  string[] */
    private $imageUrls;

    public function __construct(array $data)
    {
        $this->name = $data['name'];
        $this->value = $data['value'];
        $this->imageUrls = $data['imageUrls'];
        $this->children = array_map(
            function ($childrenData) {
                return new self($childrenData);
            },
            $data['children']
        );
    }

    /**
     * @return ProductAttribute[]
     */
    public function getChildren(): array
    {
        return $this->children;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getValue(): array
    {
        return $this->value;
    }

    /**
     * @return string[]
     */
    public function getImageUrls(): array
    {
        return $this->imageUrls;
    }
}
