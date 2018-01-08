<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\SDK\Catalog;

use Wizaplace\SDK\Image\Image;

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

    /**
     * @internal
     */
    public function __construct(array $data)
    {
        $this->id = $data['id'];
        $this->name = $data['name'];
        $this->variantId = $data['variantId'];
        $this->variantName = $data['variantName'];
        $this->image = isset($data['image']) ? new Image($data['image']) : null;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getVariantId(): int
    {
        return $this->variantId;
    }

    public function getVariantName(): string
    {
        return $this->variantName;
    }

    public function getImage(): ?Image
    {
        return $this->image;
    }

    public function jsonSerialize(): array
    {
        return [
            "id" => $this->getId(),
            "name" => $this->getName(),
            "variantId" => $this->getVariantId(),
            "variantName" => $this->getVariantName(),
            'image' => $this->getImage() ? ['id' => $this->getImage()->getId()] : null,
        ];
    }
}
