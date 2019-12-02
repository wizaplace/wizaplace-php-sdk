<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Commission;

/**
 * Class Commission
 *
 * @package Wizaplace\SDK\Commission
 */
final class Commission
{
    /** @var string */
    private $id;

    /** @var int */
    private $categoryId;

    /** @var int */
    private $companyId;

    /** @var float */
    private $percentAmount;

    /** @var float */
    private $fixedAmount;

    /** @var float|null */
    private $maximumAmount;

    /** @var string */
    private $commissionType;

    public function __construct(array $data)
    {
        $this->id = $data['id'] ?? '';
        $this->categoryId = $data['category'] ?? 0;
        $this->companyId = $data['company'] ?? 0;
        $this->percentAmount = $data['percent'] ?? 0;
        $this->fixedAmount = $data['fixed'] ?? 0;
        $this->maximumAmount = $data['maximum'] ?? null;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getCategoryId(): int
    {
        return $this->categoryId;
    }

    public function getCompanyId(): int
    {
        return $this->companyId;
    }

    public function getPercentAmount(): float
    {
        return $this->percentAmount;
    }

    public function getFixedAmount(): float
    {
        return $this->fixedAmount;
    }

    public function getMaximumAmount(): ?float
    {
        return $this->maximumAmount;
    }

    public function getCommissionType(): string
    {
        return $this->commissionType;
    }
}
