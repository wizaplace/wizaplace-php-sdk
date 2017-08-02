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

        //Tri du JSON par VariantId (string)
        usort($this->variants, function (OptionVariant $a, OptionVariant $b) : int {
            return strcmp($a->getId(), $b->getId());
        });
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
