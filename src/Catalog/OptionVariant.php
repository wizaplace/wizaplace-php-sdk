<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types=1);

namespace Wizaplace\SDK\Catalog;

use Wizaplace\SDK\Image\Image;

/**
 * Class OptionVariant.
 */
final class OptionVariant implements \JsonSerializable
{
    /** @var int */
    private $id;

    /** @var string */
    private $name;

    /** @var Image|null */
    private $image;

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

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        $image = $this->getImage();

        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'image' => $image ? $image->jsonSerialize() : null,
        ];
    }
}
