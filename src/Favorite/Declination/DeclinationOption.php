<?php
declare(strict_types = 1);

namespace Wizaplace\Favorite\Declination;

final class DeclinationOption
{
    /** @var int */
    private $id;

    /** @var string */
    private $name;

    /** @var int */
    private $variantId;

    /** @var string */
    private $variantName;

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
}
