<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types=1);

namespace Wizaplace\SDK\Catalog;

use Wizaplace\SDK\Image\Image;

/**
 * Class DeclinationOption.
 */
final class DeclinationOption implements \JsonSerializable
{
    /** @var int */
    private $id;

    /** @var string */
    private $name;

    /** @var int */
    private $variantId;

    /** @var string */
    private $variantName;

    /** @var Image|null */
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
        $this->variantId = $data['variantId'];
        $this->variantName = $data['variantName'];
        $this->image = isset($data['image']) ? new Image($data['image']) : null;
        $this->position = isset($data['position']) ? $data['position'] : 0;
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
    public function getVariantId(): int
    {
        return $this->variantId;
    }

    /**
     * @return string
     */
    public function getVariantName(): string
    {
        return $this->variantName;
    }

    /**
     * @return Image|null
     */
    public function getImage(): ?Image
    {
        return $this->image;
    }

    /**
     * @return int|null
     */
    public function getPosition(): ?int
    {
        return $this->position;
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        $image = $this->getImage();

        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'variantId' => $this->getVariantId(),
            'variantName' => $this->getVariantName(),
            'image' => $image ? $image->jsonSerialize() : null,
            'position' => $this->getPosition(),
        ];
    }
}
