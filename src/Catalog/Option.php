<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types = 1);


namespace Wizaplace\SDK\Catalog;

/**
 * Class Option
 * @package Wizaplace\SDK\Catalog
 */
final class Option implements \JsonSerializable
{
    /** @var int */
    private $id;

    /** @var string */
    private $name;

    /** @var int */
    private $position;

    /** @var OptionVariant[] */
    private $variants;

    /** @var null|string */
    private $code;

    /**
     * @internal
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->id = $data['id'];
        $this->name = $data['name'];
        $this->position = $data['position'] ?? 0;
        $this->variants = array_map(static function (array $variantData) : OptionVariant {
            return new OptionVariant($variantData);
        }, $data['variants']);
        $this->code = $data['code'] ?? null;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getPosition(): int
    {
        return $this->position;
    }

    /**
     * @return OptionVariant[]
     */
    public function getVariants(): array
    {
        return $this->variants;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'position' => $this->getPosition(),
            'variants' => $this->getVariants(),
            'code' => $this->getCode(),
        ];
    }
}
