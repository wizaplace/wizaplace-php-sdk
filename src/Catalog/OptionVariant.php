<?php

/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Catalog;

use Wizaplace\SDK\Image\Image;

/**
 * Class OptionVariant
 * @package Wizaplace\SDK\Catalog
 */
final class OptionVariant implements \JsonSerializable
{
    /** @var int */
    private $id;

    /** @var string */
    private $name;

    /** @var null|Image */
    private $image;

    /** @var int */
    private $position;

    /**
     * @internal
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->id = $data['id'];
        $this->name = $data['name'];
        $this->image = isset($data['image']) ? new Image($data['image']) : null;
        $this->position = \array_key_exists('position', $data) === true ? $data['position'] : 0;
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
     * @return Image|null
     */
    public function getImage(): ?Image
    {
        return $this->image;
    }

    /** @return int */
    public function getPosition(): int
    {
        return $this->position;
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'image' => $this->getImage() ? ['id' => $this->getImage()->getId()] : null,
            'position' => $this->getPosition(),
        ];
    }
}
