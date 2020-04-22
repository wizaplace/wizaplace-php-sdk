<?php

/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Company;

/**
 * Class CompanyRegistrationResult
 * @package Wizaplace\SDK\Company
 */
final class CompanyRegistrationResult
{
    /** @var Company */
    private $company;

    /** @var FileUploadResult[] */
    private $fileUploadResults;

    /**
     * @param Company            $company
     * @param FileUploadResult[] $fileUploadResults
     *
     * @internal
     */
    public function __construct(Company $company, array $fileUploadResults)
    {
        $this->company = $company;
        $this->fileUploadResults = $fileUploadResults;
    }

    /**
     * @return Company
     */
    public function getCompany(): Company
    {
        return $this->company;
    }

    /**
     * @param string $name
     *
     * @return FileUploadResult|null
     */
    public function getFileUploadResult(string $name): ?FileUploadResult
    {
        return $this->fileUploadResults[$name] ?? null;
    }
}
