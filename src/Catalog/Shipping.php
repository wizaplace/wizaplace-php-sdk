<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\SDK\Catalog;

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

    /** @var string|null  */
    private $deliveryTime;

    /**
     * @internal
     */
    public function __construct(array $data)
    {
        $this->name = $data['name'];
        $this->image = $data['image'];
        $this->firstRate = $data['firstRate'];
        $this->nextRate = $data['nextRate'];
        $this->deliveryTime = $data['deliveryTime'];
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function getFirstRate(): float
    {
        return $this->firstRate;
    }

    public function getNextRate(): float
    {
        return $this->nextRate;
    }

    public function getDeliveryTime(): ?string
    {
        return $this->deliveryTime;
    }
}
