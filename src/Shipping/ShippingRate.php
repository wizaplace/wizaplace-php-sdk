<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\SDK\Shipping;

final class ShippingRate
{
    /** @var int */
    private $amount;

    /** @var float|null */
    private $value;

    /**
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->amount = $data['amount'];
        $this->value = $data['value'];
    }

    /**
     * @return int
     */
    public function getAmount(): int
    {
        return $this->amount;
    }

    /**
     * @return float|null
     */
    public function getValue(): ?float
    {
        return $this->value;
    }
}
