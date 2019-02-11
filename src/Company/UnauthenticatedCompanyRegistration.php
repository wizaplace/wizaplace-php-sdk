<?php
/**
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\SDK\Company;

/**
 * Class UnauthenticatedCompanyRegistration
 * @package Wizaplace\SDK\Company
 */
final class UnauthenticatedCompanyRegistration extends CompanyRegistration
{
    /**
     * @var string
     */
    private $legalRepresentativeFirstName;

    /**
     * @var string
     */
    private $legalRepresentativeLastName;

    /**
     * UnauthenticatedCompanyRegistration constructor.
     *
     * @param string $name
     * @param string $email
     * @param string $legalRepresentativeFirstName
     * @param string $legalRepresentativeLastName
     */
    public function __construct(string $name, string $email, string $legalRepresentativeFirstName, string $legalRepresentativeLastName)
    {
        parent::__construct($name, $email);
        $this->legalRepresentativeFirstName = $legalRepresentativeFirstName;
        $this->legalRepresentativeLastName = $legalRepresentativeLastName;
    }

    /**
     * @return string
     */
    public function getLegalRepresentativeFirstName(): string
    {
        return $this->legalRepresentativeFirstName;
    }

    /**
     * @param string $legalRepresentativeFirstName
     */
    public function setLegalRepresentativeFirstName(string $legalRepresentativeFirstName): void
    {
        $this->legalRepresentativeFirstName = $legalRepresentativeFirstName;
    }

    /**
     * @return string
     */
    public function getLegalRepresentativeLastName(): string
    {
        return $this->legalRepresentativeLastName;
    }

    /**
     * @param string $legalRepresentativeLastName
     */
    public function setLegalRepresentativeLastName(string $legalRepresentativeLastName): void
    {
        $this->legalRepresentativeLastName = $legalRepresentativeLastName;
    }
}
