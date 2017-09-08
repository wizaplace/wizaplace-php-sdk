<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types = 1);


namespace Wizaplace\SDK\Catalog;

final class Option implements \JsonSerializable
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
        $this->id = $data['id'];
        $this->name = $data['name'];
        $this->variants = array_map(static function (array $variantData) : OptionVariant {
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

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'variants' => $this->getVariants(),
        ];
    }
}
