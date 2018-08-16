<?php
/**
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */

namespace Wizaplace\SDK\Company;

final class CompanyFile
{
    /** @var int */
    private $companyId;

    /** @var string */
    private $filename;

    /**
     * @internal
     */
    public function __construct(int $companyId, string $filename)
    {
        $this->companyId = $companyId;
        $this->filename = $filename;
    }

    public function getCompanyId(): int
    {
        return $this->companyId;
    }

    public function getFilename(): string
    {
        return $this->filename;
    }
}
