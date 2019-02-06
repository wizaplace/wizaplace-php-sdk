<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types=1);

namespace Wizaplace\SDK\Pim\Attribute;

/**
 * Class ProductAttributeVariants
 * @package Wizaplace\SDK\Pim\Attribute
 */
final class ProductAttributeVariants extends ProductAttribute
{
    /** @var AttributeVariant[] */
    private $selectedVariants;

    /**
     * @internal
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        parent::__construct($data);
        $this->selectedVariants = array_map(function (array $variantData): AttributeVariant {
            return new AttributeVariant($variantData);
        }, $data['variants']);
    }

    /**
     * @return AttributeVariant[]
     */
    public function getSelectedVariants(): array
    {
        return $this->selectedVariants;
    }
}
