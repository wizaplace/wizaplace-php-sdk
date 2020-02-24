<?php

/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Pim\Product;

/**
 * Class Shipping
 * @package Wizaplace\SDK\Pim\Product
 */
final class Shipping
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $status;

    /**
     * @var string
     */
    private $shipping;

    /**
     * @var string
     */
    private $deliveryTime;

    /**
     * @var array
     */
    private $rates;

    /**
     * @var bool
     */
    private $specificRate;

    /**
     * @var int
     */
    private $productId;

    /**
     * @var string
     */
    private $description;

    /**
     * Shipping constructor.
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->id           = $data['shipping_id'];
        $this->status       = $data['status'];
        $this->shipping     = $data['shipping'];
        $this->deliveryTime = $data['delivery_time'];
        $this->rates        = $data['rates'];
        $this->specificRate = $data['specific_rate'];
        $this->productId    = $data['product_id'];
        $this->description  = $data['description'];
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
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @return string
     */
    public function getShipping(): string
    {
        return $this->shipping;
    }

    /**
     * @return string
     */
    public function getDeliveryTime(): string
    {
        return $this->deliveryTime;
    }

    /**
     * @return array
     */
    public function getRates(): array
    {
        return $this->rates;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @return bool
     */
    public function isSpecificRate(): bool
    {
        return $this->specificRate;
    }

    /**
     * @return int
     */
    public function getProductId(): int
    {
        return $this->productId;
    }
}
