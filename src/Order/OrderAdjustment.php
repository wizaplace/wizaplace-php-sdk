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
    private $oldPriceWithoutTaxes;
    /** @var float */
    private $newPriceWithoutTaxes;
    /** @var float */
    private $oldTotalIncludingTaxes;
    /** @var float */
    private $newTotalIncludingTaxes;
    /** @var null|int */
    private $createdBy;
    /** @var null|\DateTime */
    private $createdAt;

    public function __construct(array $data = [])
    {
        $this->itemId = (int) $data['itemId'] ?? null;
        $this->oldPriceWithoutTaxes = (float) $data['oldPriceWithoutTaxes'] ?? 0.00;
        $this->newPriceWithoutTaxes = (float) $data['newPriceWithoutTaxes'] ?? 0.00;
        $this->oldTotalIncludingTaxes = (float) $data['oldTotalIncludingTaxes'] ?? 0.00;
        $this->newTotalIncludingTaxes = (float) $data['newTotalIncludingTaxes'] ?? 0.00;
        $this->createdBy = (int) $data['createdBy'] ?? null;
        $this->createdAt = $data['createdAt'] ? new \DateTime($data['createdAt']) : null;
    }

    public function getItemId(): ?int
    {
        return $this->itemId;
    }

    public function getOldPriceWithoutTaxes(): float
    {
        return $this->oldPriceWithoutTaxes;
    }

    public function getNewPriceWithoutTaxes(): float
    {
        return $this->newPriceWithoutTaxes;
    }

    public function getOldTotalIncludingTaxes(): float
    {
        return $this->oldTotalIncludingTaxes;
    }

    public function getNewTotalIncludingTaxes(): float
    {
        return $this->newTotalIncludingTaxes;
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
