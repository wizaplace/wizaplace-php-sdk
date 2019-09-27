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

    public function __construct(array $data)
    {
        $this->id = $data['id'] ?? '';
        $this->categoryId = $data['category'] ?? 0;
        $this->companyId = $data['company'] ?? 0;
        $this->percentAmount = $data['percent'] ?? 0;
        $this->fixedAmount = $data['fixed'] ?? 0;
        $this->maximumAmount = $data['maximum'] ?? null;
    }

    /** @return string */
    public function getId(): string
    {
        return $this->id;
    }

    /** @return int */
    public function getCategoryId(): int
    {
        return $this->categoryId;
    }

    /** @return int */
    public function getCompanyId(): int
    {
        return $this->companyId;
    }

    /** @return float */
    public function getPercentAmount(): float
    {
        return $this->percentAmount;
    }

    /** @return float */
    public function getFixedAmount(): float
    {
        return $this->fixedAmount;
    }

    /** @return float|null */
    public function getMaximumAmount(): ?float
    {
        return $this->maximumAmount;
    }
}
