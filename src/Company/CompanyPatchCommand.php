<?php

/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Company;

/**
 * Class CompanyPatchCommand
 * @package Wizaplace\SDK\Company
 */
class CompanyPatchCommand
{
    /** @var int */
    protected $companyId;

    /** @var CompanyStatus */
    protected $status;

    public function __construct(int $companyId, CompanyStatus $status)
    {
        $this->companyId = $companyId;
        $this->status = $status;
    }

    public function getCompanyId(): int
    {
        return $this->companyId;
    }

    public function setCompanyId(int $companyId): self
    {
        $this->companyId = $companyId;

        return $this;
    }

    public function getStatus(): CompanyStatus
    {
        return $this->status;
    }

    public function setStatus(CompanyStatus $status): self
    {
        $this->status = $status;

        return $this;
    }
}
