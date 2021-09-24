<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Vendor\Order;

use Wizaplace\SDK\Vendor\Order\OrderStatus;

/**
 * Class OrderChild
 * @package Wizaplace\SDK\Vendor\OrderChild
 */
final class OrderChild
{
    /** @var int */
    private $id;

    /** @var OrderStatus */
    private $status;

    /**
     * @param array $data
     * @internal
     *
     */
    public function __construct(array $data)
    {
        $this->id = $data['order_id'];
        $this->status = new OrderStatus($data['status']);
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return OrderStatus
     */
    public function getStatus(): OrderStatus
    {
        return $this->status;
    }
}


