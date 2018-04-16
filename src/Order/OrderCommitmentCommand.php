<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types=1);

namespace Wizaplace\SDK\Order;

use DateTimeImmutable;

class OrderCommitmentCommand
{
    private $orderId;
    private $commitDate;
    private $commitNumber;

    public function __construct(int $orderId, DateTimeImmutable $commitDate, string $commitNumber)
    {
        $this->orderId = $orderId;
        $this->commitDate = $commitDate;
        $this->commitNumber = $commitNumber;
    }

    public function getOrderId(): int
    {
        return $this->orderId;
    }

    public function getCommitDate(): DateTimeImmutable
    {
        return $this->commitDate;
    }

    public function getCommitNumber(): string
    {
        return $this->commitNumber;
    }
}
