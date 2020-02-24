<?php

/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Order;

/**
 * Class ShippingAddress
 * @package Wizaplace\SDK\Order
 */
final class ShippingAddress extends Address
{
    /** @var string|null */
    private $pickupPointId;

    /**
     * @internal
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        parent::__construct($data);
        $this->pickupPointId = $data['pickupPointId'];
    }

    /**
     * @return string|null
     */
    public function getPickupPointId(): ?string
    {
        return $this->pickupPointId;
    }
}
