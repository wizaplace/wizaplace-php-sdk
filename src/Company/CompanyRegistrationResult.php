<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\Company;

class CompanyRegistrationResult
{
    /** @var Company */
    private $company;

    /** @var FileUploadResult[] */
    private $fileUploadResults;

    /**
     * @param FileUploadResult[] $fileUploadResults
     * @internal
     */
    public function __construct(Company $company, array $fileUploadResults)
    {
        $this->company = $company;
        $this->fileUploadResults = $fileUploadResults;
    }

    public function getCompany(): Company
    {
        return $this->company;
    }

    public function getFileUploadResult(string $name): ?FileUploadResult
    {
        return $this->fileUploadResults[$name] ?? null;
    }
}
