<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types = 1);


namespace Wizaplace\Catalog;

class Option
{
    /** @var int */
    private $id;

    /** @var string */
    private $name;

    /** @var OptionVariant[] */
    private $variants;

    public function __construct(array $data)
    {
        $this->id = $data['id'];
        $this->name = $data['name'];
        $this->variants = array_map(
            function (array $variantData) : OptionVariant {
                return new OptionVariant($variantData);
            },
            $data['variants']
        );
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

    public function expose(): array
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'variants' => array_map(function ($variant) {
                return $variant->expose();
            }, $this->getVariants()),
        ];
    }
}
