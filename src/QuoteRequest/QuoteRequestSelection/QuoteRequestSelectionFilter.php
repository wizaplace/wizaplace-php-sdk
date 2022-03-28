<?php

/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @license     Proprietary
 * @copyright   Copyright (c) Wizacha
 */

declare(strict_types=1);

namespace Wizaplace\SDK\QuoteRequest\QuoteRequestSelection;

final class QuoteRequestSelectionFilter
{
    public const LIMIT = 'limit';
    public const OFFSET = 'offset';
    public const IDS = 'ids';
    public const USER_IDS = 'userIds';
    public const ACTIVE = 'active';
    public const DECLINATION_IDS = 'declinationIds';
    public const QUOTE_REQUEST_IDS = 'quoteRequestIds';
    public const CREATION_PERIOD = 'creationPeriod';
    public const UPDATE_PERIOD = 'updatePeriod';

    /** @var null|int */
    private $limit;

    /** @var null|int */
    private $offset;

    /** @var null|int[] */
    private $ids;

    /** @var null|int[] */
    private $userIds;

    /** @var null|bool */
    private $active;

    /** @var null|string[] */
    private $declinationIds;

    /** @var null|int[] */
    private $quoteRequestIds;

    /** @var null|\DateTime */
    private $creationPeriodFrom;

    /** @var null|\DateTime */
    private $creationPeriodTo;

    /** @var null|\DateTime */
    private $updatePeriodFrom;

    /** @var null|\DateTime */
    private $updatePeriodTo;

    public function getFilters(): array
    {
        $creationPeriod = [];
        if ($this->getCreationPeriodFrom() instanceof \DateTime) {
            $creationPeriod['from'] = $this->getCreationPeriodFrom()->format(\DateTime::RFC3339);
        }
        if ($this->getCreationPeriodTo() instanceof \DateTime) {
            $creationPeriod['to'] = $this->getCreationPeriodTo()->format(\DateTime::RFC3339);
        }

        $updatePeriod = [];
        if ($this->getUpdatePeriodFrom() instanceof \DateTime) {
            $updatePeriod['from'] = $this->getCreationPeriodFrom()->format(\DateTime::RFC3339);
        }
        if ($this->getUpdatePeriodTo() instanceof \DateTime) {
            $updatePeriod['to'] = $this->getCreationPeriodTo()->format(\DateTime::RFC3339);
        }

        $filters = [
            static::LIMIT => $this->getLimit(),
            static::OFFSET => $this->getOffset(),
            static::IDS => $this->getIds(),
            static::USER_IDS => $this->getUserIds(),
            static::ACTIVE => $this->isActive(),
            static::DECLINATION_IDS => $this->getDeclinationIds(),
            static::QUOTE_REQUEST_IDS => $this->getQuoteRequestIds(),
            static::CREATION_PERIOD => $creationPeriod,
            static::UPDATE_PERIOD => $updatePeriod,
        ];

        return array_filter(
            $filters,
            function ($item): bool {
                return false === \is_null($item);
            }
        );
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

    public function getIds(): ?array
    {
        return $this->ids;
    }

    public function setIds(array $ids): self
    {
        $this->ids = $ids;

        return $this;
    }

    public function getUserIds(): ?array
    {
        return $this->userIds;
    }

    public function setUserIds(array $userIds): self
    {
        $this->userIds = $userIds;

        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;

        return $this;
    }

    public function getDeclinationIds(): ?array
    {
        return $this->declinationIds;
    }

    public function setDeclinationIds(array $declinationIds): self
    {
        $this->declinationIds = $declinationIds;

        return $this;
    }

    public function getQuoteRequestIds(): ?array
    {
        return $this->quoteRequestIds;
    }

    public function setQuoteRequestIds(array $quoteRequestIds): self
    {
        $this->quoteRequestIds = $quoteRequestIds;

        return $this;
    }

    public function getCreationPeriodFrom(): ?\DateTime
    {
        return $this->creationPeriodFrom;
    }

    public function setCreationPeriodFrom(\DateTime $creationPeriodFrom): self
    {
        $this->creationPeriodFrom = $creationPeriodFrom;

        return $this;
    }

    public function getCreationPeriodTo(): ?\DateTime
    {
        return $this->creationPeriodTo;
    }

    public function setCreationPeriodTo(\DateTime $creationPeriodTo): self
    {
        $this->creationPeriodTo = $creationPeriodTo;

        return $this;
    }

    public function getUpdatePeriodFrom(): ?\DateTime
    {
        return $this->updatePeriodFrom;
    }

    public function setUpdatePeriodFrom(\DateTime $updatePeriodFrom): self
    {
        $this->creationPeriodFrom = $updatePeriodFrom;

        return $this;
    }

    public function getUpdatePeriodTo(): ?\DateTime
    {
        return $this->updatePeriodTo;
    }

    public function setUpdatePeriodTo(\DateTime $updatePeriodTo): self
    {
        $this->updatePeriodTo = $updatePeriodTo;

        return $this;
    }
}
