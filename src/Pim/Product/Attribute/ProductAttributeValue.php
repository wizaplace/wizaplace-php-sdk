<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types=1);

namespace Wizaplace\SDK\Pim\Product\Attribute;

final class ProductAttributeValue extends ProductAttribute
{
    /** @var string */
    private $value;

    /**
     * @internal
     */
    public function __construct(array $data)
    {
        parent::__construct($data);
        $this->value = $data['value_str'];
    }

    public function getValue(): string
    {
        return $this->value;
    }
}
