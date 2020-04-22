<?php

/**
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */

namespace Wizaplace\SDK\Company;

/**
 * Class CompanyFile
 * @package Wizaplace\SDK\Company
 */
final class CompanyFile
{
    /** @var int */
    private $companyId;

    /** @var string */
    private $filename;

    /**
     * @internal
     *
     * @param int    $companyId
     * @param string $filename
     */
    public function __construct(int $companyId, string $filename)
    {
        $this->companyId = $companyId;
        $this->filename = $filename;
    }

    /**
     * @return int
     */
    public function getCompanyId(): int
    {
        return $this->companyId;
    }

    /**
     * @return string
     */
    public function getFilename(): string
    {
        return $this->filename;
    }
}
