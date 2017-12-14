<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types=1);

namespace Wizaplace\SDK\Pim\Attribute;

final class Attribute extends AbstractAttribute
{
    /** @var AttributeVariant[]|null */
    private $variants;

    /**
     * @internal
     */
    public function __construct(array $data)
    {
        parent::__construct($data);
        if (isset($data['variants'])) {
            $this->variants = array_map(function (array $variantData): AttributeVariant {
                return new AttributeVariant($variantData);
            }, $data['variants']);
        }
    }

    /**
     * @return AttributeVariant[]
     */
    public function getVariants(): ?array
    {
        return $this->variants;
    }
}
