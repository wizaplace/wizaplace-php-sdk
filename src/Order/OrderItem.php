<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\SDK\Order;

final class OrderItem
{
    /** @var string */
    private $declinationId;
    /** @var string */
    private $productName;
    /** @var string */
    private $productCode;
    /** @var float */
    private $price;
    /** @var int */
    private $amount;

    /**
     * @internal
     */
    public function __construct(array $data)
    {
        $this->declinationId = $data['declinationId'];
        $this->productName = $data['productName'];
        $this->productCode = $data['productCode'];
        $this->price = $data['price'];
        $this->amount = $data['amount'];
    }

    public function getDeclinationId(): string
    {
        return $this->declinationId;
    }

    public function getProductName(): string
    {
        return $this->productName;
    }

    public function getProductCode(): string
    {
        return $this->productCode;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function getAmount(): int
    {
        return $this->amount;
    }
}
