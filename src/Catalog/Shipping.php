<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\SDK\Catalog;

/**
 * Class Shipping
 * @package Wizaplace\SDK\Catalog
 */
final class Shipping
{
    /** @var string */
    private $name;

    /** @var string|null */
    private $image;

    /** @var float */
    private $firstRate;

    /** @var float */
    private $nextRate;

    /** @var int */
    private $position;

    /** @var string|null  */
    private $deliveryTime;

    /**
     * @internal
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->name = $data['name'];
        $this->image = $data['image'];
        $this->firstRate = $data['firstRate'];
        $this->nextRate = $data['nextRate'];
        $this->deliveryTime = $data['deliveryTime'];
        $this->position = $data['position'] ?? null;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string|null
     */
    public function getImage(): ?string
    {
        return $this->image;
    }

    /**
     * @return float
     */
    public function getFirstRate(): float
    {
        return $this->firstRate;
    }

    /**
     * @return float
     */
    public function getNextRate(): float
    {
        return $this->nextRate;
    }

    /** @return int|null */
    public function getPosition(): ?int
    {
        return $this->position;
    }

    /**
     * @return string|null
     */
    public function getDeliveryTime(): ?string
    {
        return $this->deliveryTime;
    }
}
