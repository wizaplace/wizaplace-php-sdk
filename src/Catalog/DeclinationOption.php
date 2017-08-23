<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\Catalog;

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

    /**
     * @internal
     */
    public function __construct(array $data)
    {
        $this->id = $data['id'];
        $this->name = $data['name'];
        $this->variantId = $data['variantId'];
        $this->variantName = $data['variantName'];
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

    public function jsonSerialize(): array
    {
        return [
            "id" => $this->getId(),
            "name" => $this->getName(),
            "variantId" => $this->getVariantId(),
            "variantName" => $this->getVariantName(),
        ];
    }
}
