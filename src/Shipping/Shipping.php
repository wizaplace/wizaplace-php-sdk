<?php

/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Shipping;

/**
 * Class Shipping
 * @package Wizaplace\SDK\Shipping
 */
final class Shipping
{
    /** @var int */
    private $id;

    /** @var string */
    private $name;

    /** @var string */
    private $deliveryTime;

    /** @var boolean */
    private $isEnabled;

    /** @var ShippingRate[]|null */
    private $rates;

    /** @var int|null */
    private $position;

    /** @var string|null */
    private $description;

    /** @var float|null */
    private $carriagePaidThreshold;

    /**
     * @internal
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->id = $data['shipping_id'] ?? null;
        $this->name = $data['shipping'] ?? null;
        $this->deliveryTime = $data['delivery_time'] ?? null;
        $this->isEnabled = \array_key_exists('status', $data) ? $data['status'] === "A" : null;

        if (isset($data['rates'])) {
            $this->rates = array_map(
                function ($rate) {
                    return new ShippingRate($rate);
                },
                $data['rates']
            );
        }
        $this->position = $data['position'] ?? null;
        $this->description = $data['description'] ?? null;
        $this->carriagePaidThreshold = $data['carriage_paid_threshold'] ?? null;
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
     * @return string example: "24h"
     */
    public function getDeliveryTime(): string
    {
        return $this->deliveryTime;
    }

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->isEnabled;
    }

    /**
     * @return ShippingRate[]|null
     */
    public function getRates(): ?array
    {
        return $this->rates;
    }

    /** @return int|null */
    public function getPosition(): ?int
    {
        return $this->position;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getCarriagePaidThreshold(): ?float
    {
        return $this->carriagePaidThreshold;
    }
}
