<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types=1);

namespace Wizaplace\SDK\Pim\Attribute;

/**
 * Class ProductAttributeValue
 * @package Wizaplace\SDK\Pim\Attribute
 */
final class ProductAttributeValue extends ProductAttribute
{
    /** @var string */
    private $value;

    /**
     * @internal
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        parent::__construct($data);
        $this->value = $data['value_str'];
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }
}
