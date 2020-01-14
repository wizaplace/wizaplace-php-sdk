<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\SDK\Catalog;

use Wizaplace\SDK\Image\Image;

/**
 * Class DeclinationOption
 * @package Wizaplace\SDK\Catalog
 */
final class DeclinationOption implements \JsonSerializable
{
    /** @var integer */
    private $id;

    /** @var string */
    private $name;

    /** @var integer */
    private $variantId;

    /** @var string */
    private $variantName;

    /** @var null|Image */
    private $image;

    /** @var integer */
    private $position;

    /** @var string */
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
        $this->variantId = $data['variantId'];
        $this->variantName = $data['variantName'];
        $this->image = isset($data['image']) ? new Image($data['image']) : null;
        $this->position = isset($data['position']) ? $data['position'] : 0;
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
     * @return null|string
     */
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
            "id" => $this->getId(),
            "name" => $this->getName(),
            "variantId" => $this->getVariantId(),
            "variantName" => $this->getVariantName(),
            'image' => $this->getImage() ? ['id' => $this->getImage()->getId()] : null,
            'position' => $this->getPosition(),
            'code' => $this->getCode(),
        ];
    }
}
