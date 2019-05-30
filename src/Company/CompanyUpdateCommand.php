<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types=1);

namespace Wizaplace\SDK\Company;

/**
 * Class CompanyUpdateCommand
 * @package Wizaplace\SDK\Company
 */
class CompanyUpdateCommand
{
    /** @var null|string */
    private $name;

    /** @var null|string */
    private $email;

    /** @var null|string */
    private $description;

    /** @var null|string */
    private $zipcode;

    /** @var null|string */
    private $address;

    /** @var null|string */
    private $city;

    /** @var null|string */
    private $country;

    /** @var null|string */
    private $phoneNumber;

    /** @var null|string */
    private $fax;

    /** @var null|string */
    private $url;

    /** @var null|string */
    private $legalStatus;

    /** @var null|string */
    private $siretNumber;

    /** @var null|string */
    private $vatNumber;

    /** @var null|string */
    private $capital;

    /** @var null|string */
    private $rcs;

    /** @var null|string */
    private $slug;

    /** @var null|array */
    private $extra;

    /** @var int */
    private $companyId;

    /** @var null|string */
    private $nafCode;

    /**
     * CompanyUpdateCommand constructor.
     *
     * @param int $companyId
     */
    public function __construct(int $companyId)
    {
        $this->companyId = $companyId;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     *
     * @return CompanyUpdateCommand
     */
    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param string|null $email
     *
     * @return CompanyUpdateCommand
     */
    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string|null $description
     *
     * @return CompanyUpdateCommand
     */
    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getZipcode(): ?string
    {
        return $this->zipcode;
    }

    /**
     * @param string|null $zipcode
     *
     * @return CompanyUpdateCommand
     */
    public function setZipcode(?string $zipcode): self
    {
        $this->zipcode = $zipcode;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getAddress(): ?string
    {
        return $this->address;
    }

    /**
     * @param string|null $address
     *
     * @return CompanyUpdateCommand
     */
    public function setAddress(?string $address): self
    {
        $this->address = $address;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getCity(): ?string
    {
        return $this->city;
    }

    /**
     * @param string|null $city
     *
     * @return CompanyUpdateCommand
     */
    public function setCity(?string $city): self
    {
        $this->city = $city;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getCountry(): ?string
    {
        return $this->country;
    }

    /**
     * @param string|null $country
     *
     * @return CompanyUpdateCommand
     */
    public function setCountry(?string $country): self
    {
        $this->country = $country;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    /**
     * @param string|null $phoneNumber
     *
     * @return CompanyUpdateCommand
     */
    public function setPhoneNumber(?string $phoneNumber): self
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getFax(): ?string
    {
        return $this->fax;
    }

    /**
     * @param string|null $fax
     *
     * @return CompanyUpdateCommand
     */
    public function setFax(?string $fax): self
    {
        $this->fax = $fax;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getUrl(): ?string
    {
        return $this->url;
    }

    /**
     * @param string|null $url
     *
     * @return CompanyUpdateCommand
     */
    public function setUrl(?string $url): self
    {
        $this->url = $url;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getLegalStatus(): ?string
    {
        return $this->legalStatus;
    }

    /**
     * @param string|null $legalStatus
     *
     * @return CompanyUpdateCommand
     */
    public function setLegalStatus(?string $legalStatus): self
    {
        $this->legalStatus = $legalStatus;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getSiretNumber(): ?string
    {
        return $this->siretNumber;
    }

    /**
     * @param string|null $siretNumber
     *
     * @return CompanyUpdateCommand
     */
    public function setSiretNumber(?string $siretNumber): self
    {
        $this->siretNumber = $siretNumber;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getVatNumber(): ?string
    {
        return $this->vatNumber;
    }

    /**
     * @param string|null $vatNumber
     *
     * @return CompanyUpdateCommand
     */
    public function setVatNumber(?string $vatNumber): self
    {
        $this->vatNumber = $vatNumber;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getCapital(): ?string
    {
        return $this->capital;
    }

    /**
     * @param string|null $capital
     *
     * @return CompanyUpdateCommand
     */
    public function setCapital(?string $capital): self
    {
        $this->capital = $capital;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getRcs(): ?string
    {
        return $this->rcs;
    }

    /**
     * @param string|null $rcs
     *
     * @return CompanyUpdateCommand
     */
    public function setRcs(?string $rcs): self
    {
        $this->rcs = $rcs;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getSlug(): ?string
    {
        return $this->slug;
    }

    /**
     * @param string|null $slug
     *
     * @return CompanyUpdateCommand
     */
    public function setSlug(?string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * @return array|null
     */
    public function getExtra(): ?array
    {
        return $this->extra;
    }

    /**
     * @param array|null $extra
     */
    public function setExtra(?array $extra): void
    {
        $this->extra = $extra;
    }

    /**
     * @return int
     */
    public function getCompanyId(): int
    {
        return $this->companyId;
    }

    /**
     * @param int $companyId
     *
     * @return CompanyUpdateCommand
     */
    public function setCompanyId(int $companyId): self
    {
        $this->companyId = $companyId;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getNafCode(): ?string
    {
        return $this->nafCode;
    }

    /**
     * @param string|null $nafCode
     *
     * @return CompanyUpdateCommand
     */
    public function setNafCode(?string $nafCode): self
    {
        $this->nafCode = $nafCode;

        return $this;
    }
}
