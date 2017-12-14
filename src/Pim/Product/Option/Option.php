<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types=1);

namespace Wizaplace\SDK\Pim\Product\Option;

final class Option
{
    /** @var int */
    private $id;

    /** @var string */
    private $name;

    /** @var OptionVariant[] */
    private $variants;

    /**
     * @internal
     */
    public function __construct(array $data)
    {
        $this->id = $data['option_id'];
        $this->name = $data['option_name'];
        $this->variants = array_map(function (array $variantData): OptionVariant {
            return new OptionVariant($variantData);
        }, $data['variants']);
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return OptionVariant[]
     */
    public function getVariants(): array
    {
        return $this->variants;
    }
}
