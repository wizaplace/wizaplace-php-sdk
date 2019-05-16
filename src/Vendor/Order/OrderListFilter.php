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
     * @var \DateTime
     */
    private $lastStatusChangeIsAfter;

    /**
     * @var \DateTime
     */
    private $lastStatusChangeIsBefore;

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

    public function toArray(): array
    {
        $filters = [];

        if (count($this->companyIds) > 0) {
            $filters['company_ids'] = $this->companyIds;
        }

        if (isset($this->lastStatusChangeIsAfter)) {
            $filters['last_status_change_is_after'] = $this->lastStatusChangeIsAfter->format(\DateTime::RFC3339);
        }

        if (isset($this->lastStatusChangeIsBefore)) {
            $filters['last_status_change_is_before'] = $this->lastStatusChangeIsBefore->format(\DateTime::RFC3339);
        }

        return $filters;
    }
}
