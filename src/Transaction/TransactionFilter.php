<?php

/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @license     Proprietary
 * @copyright   Copyright (c) Wizacha
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Transaction;

final class TransactionFilter
{
    public const PAGE = 'page';
    public const RESULT_PER_PAGE = 'resultPerPage';
    public const STATUS = 'status[]';
    public const TYPE = 'type[]';
    public const COMPANY_ID = 'company[]';

    /** @var null|int */
    private $page;

    /** @var null|int */
    private $resultPerPage;

    /** @var null|TransactionPeriod */
    private $period;

    /** @var null|TransactionStatus */
    private $status;

    /** @var null|TransactionType */
    protected $type;

    /** @var null|int */
    protected $companyId;

    public function getFilters(): array
    {
        $filters = [
            self::PAGE => $this->getPage(),
            self::RESULT_PER_PAGE => $this->getResultPerPage(),
            self::STATUS => ($this->getStatus() instanceof TransactionStatus === true) ? $this->getStatus()->getValue() : null,
            self::TYPE => ($this->getType() instanceof TransactionType === true) ? $this->getType()->getValue() : null,
            self::COMPANY_ID => $this->getCompanyId(),
        ];

        /** Filter period */
        $filters['period'] = null;
        if (\is_null($this->getPeriod()) === false) {
            $period = $this->getPeriod();

            if (\is_null($period->getFrom()) === false) {
                $filters['period']['from'] = $period->getFrom()->format(\DateTime::RFC3339);
            }

            if (\is_null($period->getTo()) === false) {
                $filters['period']['to'] = $period->getTo()->format(\DateTime::RFC3339);
            }
        }

        return \array_filter(
            $filters,
            static function ($item): bool {
                return \is_null($item) === false;
            }
        );
    }

    /** @return int|null */
    public function getPage(): ?int
    {
        return $this->page;
    }

    public function setPage(?int $page): self
    {
        $this->page = $page;

        return $this;
    }

    /** @return int|null*/
    public function getResultPerPage(): ?int
    {
        return $this->resultPerPage;
    }

    public function setResultPerPage(?int $resultPerPage): self
    {
        $this->resultPerPage = $resultPerPage;

        return $this;
    }

    /** @return null|TransactionPeriod */
    public function getPeriod(): ?TransactionPeriod
    {
        return $this->period;
    }

    public function setPeriod(?TransactionPeriod $period): self
    {
        $this->period = $period;

        return $this;
    }

    /** @return null|TransactionStatus */
    public function getStatus(): ?TransactionStatus
    {
        return $this->status;
    }

    public function setStatus(?TransactionStatus $status): self
    {
        $this->status = $status;

        return $this;
    }

    /** @return null|TransactionType */
    public function getType(): ?TransactionType
    {
        return $this->type;
    }

    public function setType(?TransactionType $type): self
    {
        $this->type = $type;

        return $this;
    }

    /** @return int|null */
    public function getCompanyId(): ?int
    {
        return $this->companyId;
    }

    public function setCompanyId(?int $companyId): self
    {
        $this->companyId = $companyId;

        return $this;
    }
}
