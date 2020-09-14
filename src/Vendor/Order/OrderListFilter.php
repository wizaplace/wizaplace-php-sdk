<?php

/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Vendor\Order;

use Wizaplace\SDK\ArrayableInterface;

final class OrderListFilter implements ArrayableInterface
{
    /**
     * @var int[]
     */
    private $companyIds = [];

    /**
     * @var null|\DateTime
     */
    private $lastStatusChangeIsAfter;

    /**
     * @var null|\DateTime
     */
    private $lastStatusChangeIsBefore;

    /**
     * @var null|int
     */
    private $itemsPerPage;

    /**
     * @var null|int
     */
    private $page;

    /**
     * @param int[] $companyIds
     *
     * @return OrderListFilter
     */
    public function byCompanyIds(array $companyIds): self
    {
        $this->companyIds = $companyIds;

        return $this;
    }

    /**
     * @param \DateTime $lastStatusChangeIsAfter
     *
     * @return OrderListFilter
     */
    public function byLastStatusChangeIsAfter(\DateTime $lastStatusChangeIsAfter): self
    {
        $this->lastStatusChangeIsAfter = $lastStatusChangeIsAfter;

        return $this;
    }

    /**
     * @param \DateTime $lastStatusChangeIsBefore
     *
     * @return OrderListFilter
     */
    public function byLastStatusChangeIsBefore(\DateTime $lastStatusChangeIsBefore): self
    {
        $this->lastStatusChangeIsBefore = $lastStatusChangeIsBefore;

        return $this;
    }

    /**
     * @param int $itemPerPage
     *
     * @return OrderListFilter
     */
    public function byItemPerPage(int $itemPerPage): self
    {
        $this->itemsPerPage = $itemPerPage;

        return $this;
    }

    /**
     * @param int $page
     *
     * @return OrderListFilter
     */
    public function byPage(int $page): self
    {
        $this->page = $page;

        return $this;
    }

    public function toArray(): array
    {
        $filters = [];

        if (\count($this->companyIds) > 0) {
            $filters['company_ids'] = $this->companyIds;
        }

        if ($this->lastStatusChangeIsAfter instanceof \DateTime) {
            $filters['last_status_change_is_after'] = $this->lastStatusChangeIsAfter->format(\DateTime::RFC3339);
        }

        if ($this->lastStatusChangeIsBefore instanceof \DateTime) {
            $filters['last_status_change_is_before'] = $this->lastStatusChangeIsBefore->format(\DateTime::RFC3339);
        }

        if (\is_int($this->itemsPerPage) === true
            && $this->itemsPerPage > 0
            && $this->itemsPerPage <= 100
        ) {
            $filters['items_per_page'] = $this->itemsPerPage;
        }

        if (\is_int($this->page) === true
            && $this->page > 0
        ) {
            $filters['page'] = $this->page;
        }

        return $filters;
    }
}
