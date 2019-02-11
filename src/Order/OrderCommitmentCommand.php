<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types=1);

namespace Wizaplace\SDK\Order;

use DateTimeImmutable;

/**
 * Class OrderCommitmentCommand
 * @package Wizaplace\SDK\Order
 */
class OrderCommitmentCommand
{
    /**
     * @var int
     */
    private $orderId;
    /**
     * @var DateTimeImmutable
     */
    private $commitDate;
    /**
     * @var string
     */
    private $commitNumber;

    /**
     * OrderCommitmentCommand constructor.
     *
     * @param int               $orderId
     * @param DateTimeImmutable $commitDate
     * @param string            $commitNumber
     */
    public function __construct(int $orderId, DateTimeImmutable $commitDate, string $commitNumber)
    {
        $this->orderId = $orderId;
        $this->commitDate = $commitDate;
        $this->commitNumber = $commitNumber;
    }

    /**
     * @return int
     */
    public function getOrderId(): int
    {
        return $this->orderId;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getCommitDate(): DateTimeImmutable
    {
        return $this->commitDate;
    }

    /**
     * @return string
     */
    public function getCommitNumber(): string
    {
        return $this->commitNumber;
    }
}
