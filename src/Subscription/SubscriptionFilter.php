<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @license     Proprietary
 * @copyright   Copyright (c) Wizacha
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Subscription;

final class SubscriptionFilter
{
    public const LIMIT = 'limit';
    public const OFFSET = 'offset';
    public const STATUS = 'status';
    public const COMPANY_ID = 'companyId';
    public const USER_ID = 'userId';
    public const PRODUCT_ID = 'productId';
    public const COMMITMENT_END_BEFORE = 'commitmentEndBefore';
    public const COMMITMENT_END_AFTER = 'commitmentEndAfter';
    public const IS_AUTORENEW = 'isAutorenew';

    /** @var null|int */
    private $limit;

    /** @var null|int */
    private $offset;

    /** @var null|SubscriptionStatus */
    private $status;

    /** @var null|int */
    private $companyId;

    /** @var null|int */
    private $userId;

    /** @var null|int */
    private $productId;

    /** @var null|\DateTime */
    private $commitmentEndBefore;

    /** @var null|\DateTime */
    private $commitmentEndAfter;

    /** @var null|bool */
    private $isAutorenew;

    public function getFilters(): array
    {
        $filters = [
            static::LIMIT => $this->getLimit(),
            static::OFFSET => $this->getOffset(),
            static::STATUS => ($this->getStatus() instanceof SubscriptionStatus) ? $this->getStatus()->getValue() : null,
            static::COMPANY_ID => $this->getCompanyId(),
            static::USER_ID => $this->getUserId(),
            static::PRODUCT_ID => $this->getProductId(),
            static::COMMITMENT_END_BEFORE => ($this->getCommitmentEndBefore() instanceof \DateTime) ? $this->getCommitmentEndBefore()->format(\DateTime::RFC3339) : null,
            static::COMMITMENT_END_AFTER => ($this->getCommitmentEndAfter() instanceof \DateTime) ? $this->getCommitmentEndAfter()->format(\DateTime::RFC3339) : null,
            static::IS_AUTORENEW => $this->getIsAutorenew(),
        ];

        return array_filter($filters, function ($item): bool {
            return false === is_null($item);
        });
    }

    public function getLimit(): ?int
    {
        return $this->limit;
    }

    public function setLimit(int $limit): self
    {
        $this->limit = $limit;

        return $this;
    }

    public function getOffset(): ?int
    {
        return $this->offset;
    }

    public function setOffset(int $offset): self
    {
        $this->offset = $offset;

        return $this;
    }

    public function getStatus(): ?SubscriptionStatus
    {
        return $this->status;
    }

    public function setStatus(SubscriptionStatus $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getCompanyId(): ?int
    {
        return $this->companyId;
    }

    public function setCompanyId(int $companyId): self
    {
        $this->companyId = $companyId;

        return $this;
    }

    public function getUserId(): ?int
    {
        return $this->userId;
    }

    public function setUserId(int $userId): self
    {
        $this->userId = $userId;

        return $this;
    }

    public function getProductId(): ?int
    {
        return $this->productId;
    }

    public function setProductId(int $productId): self
    {
        $this->productId = $productId;

        return $this;
    }

    public function getCommitmentEndBefore(): ?\DateTime
    {
        return $this->commitmentEndBefore;
    }

    public function setCommitmentEndBefore(\DateTime $commitmentEndBefore): self
    {
        $this->commitmentEndBefore = $commitmentEndBefore;

        return $this;
    }

    public function getCommitmentEndAfter(): ?\DateTime
    {
        return $this->commitmentEndAfter;
    }

    public function setCommitmentEndAfter(\DateTime $commitmentEndAfter): self
    {
        $this->commitmentEndAfter = $commitmentEndAfter;

        return $this;
    }

    public function getIsAutorenew(): ?bool
    {
        return $this->isAutorenew;
    }

    public function setIsAutorenew(bool $isAutorenew): self
    {
        $this->isAutorenew = $isAutorenew;

        return $this;
    }
}
