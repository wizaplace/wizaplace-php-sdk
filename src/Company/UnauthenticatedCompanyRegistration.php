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

    public function __construct(string $name, string $email, string $legalRepresentativeFirstName, string $legalRepresentativeLastName)
    {
        parent::__construct($name, $email);
        $this->legalRepresentativeFirstName = $legalRepresentativeFirstName;
        $this->legalRepresentativeLastName = $legalRepresentativeLastName;
    }

    public function getLegalRepresentativeFirstName(): string
    {
        return $this->legalRepresentativeFirstName;
    }

    public function setLegalRepresentativeFirstName(string $legalRepresentativeFirstName): self
    {
        $this->legalRepresentativeFirstName = $legalRepresentativeFirstName;

        return $this;
    }

    public function getLegalRepresentativeLastName(): string
    {
        return $this->legalRepresentativeLastName;
    }

    public function setLegalRepresentativeLastName(string $legalRepresentativeLastName): self
    {
        $this->legalRepresentativeLastName = $legalRepresentativeLastName;

        return $this;
    }
}
