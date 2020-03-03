<?php

/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Order;

use Wizaplace\SDK\Catalog\DeclinationId;

use function theodorejb\polycast\to_string;

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
    /** @var null|string */
    private $supplierRef;

    /**
     * @internal
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->declinationId = new DeclinationId(to_string($data['declinationId']));
        $this->productName = to_string($data['productName']);
        $this->price = $data['price'];
        $this->reason = $data['reason'];
        $this->amount = $data['amount'];
        $this->supplierRef = \array_key_exists('supplierRef', $data) === true ? $data['supplierRef'] : null;
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

    public function getSupplierRef(): ?string
    {
        return $this->supplierRef;
    }
}
