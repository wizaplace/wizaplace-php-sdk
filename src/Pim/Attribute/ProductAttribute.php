<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types=1);

namespace Wizaplace\SDK\Pim\Attribute;

/**
 * Class ProductAttribute
 * @package Wizaplace\SDK\Pim\Attribute
 *
 * @internal
 */
abstract class ProductAttribute extends AbstractAttribute
{
    /**
     * @internal
     *
     * @param array $data
     *
     * @return static
     */
    final public static function build(array $data): self
    {
        if (isset($data['value_str'])) {
            return new ProductAttributeValue($data);
        }

        return new ProductAttributeVariants($data);
    }
}
