<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types=1);

namespace Wizaplace\SDK\Order;

class OrderAdjustment
{
    /** @var null|int */
    private $itemId;
    /** @var float */
    private $oldPrice;
    /** @var float */
    private $newPrice;
    /** @var null|int */
    private $createdBy;
    /** @var null|\DateTime */
    private $createdAt;

    public function __construct(array $data = [])
    {
        $this->itemId = (int) $data['itemId'] ?? null;
        $this->oldPrice = (float) $data['oldPrice'] ?? 0.00;
        $this->newPrice = (float) $data['newPrice'] ?? 0.00;
        $this->createdBy = (int) $data['createdBy'] ?? null;
        $this->createdAt = $data['createdAt'] ? new \DateTime($data['createdAt']) : null;
    }

    public function getItemId(): ?int
    {
        return $this->itemId;
    }

    public function getOldPrice(): float
    {
        return $this->oldPrice;
    }

    public function getNewPrice(): float
    {
        return $this->newPrice;
    }

    public function getCreatedBy(): ?int
    {
        return $this->createdBy;
    }

    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }
}
