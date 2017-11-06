<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\SDK\Order;

use Wizaplace\SDK\Catalog\DeclinationId;

final class ReturnItem
{
    /** @var DeclinationId */
    private $declinationId;
    /** @var string */
    private $productName;
    /** @var float */
    private $price;
    /** @var int */
    private $reason;
    /** @var int */
    private $amount;

    /**
     * @internal
     */
    public function __construct(array $data)
    {
        $this->declinationId = new DeclinationId((string) $data['declinationId']);
        $this->productName = (string) $data['productName'];
        $this->price = $data['price'];
        $this->reason = $data['reason'];
        $this->amount = $data['amount'];
    }

    public function getProductName(): string
    {
        return $this->productName;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function getDeclinationId(): DeclinationId
    {
        return $this->declinationId;
    }

    /**
     * @see \Wizaplace\SDK\Order\OrderService::getReturnReasons
     * @return int the reason's ID
     */
    public function getReason(): int
    {
        return $this->reason;
    }

    public function getAmount(): int
    {
        return $this->amount;
    }
}
