<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\Company;

class CompanyRegistration
{
    /** @var string */
    private $name;

    /** @var string */
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

    public function __construct(string $name, string $email)
    {
        $this->name = $name;
        $this->email = $email;
    }


    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name)
    {
        $this->name = $name;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email)
    {
        $this->email = $email;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description)
    {
        $this->description = $description;
    }

    public function getZipcode(): ?string
    {
        return $this->zipcode;
    }

    public function setZipcode(?string $zipcode)
    {
        $this->zipcode = $zipcode;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address)
    {
        $this->address = $address;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city)
    {
        $this->city = $city;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(?string $country)
    {
        $this->country = $country;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(?string $phoneNumber)
    {
        $this->phoneNumber = $phoneNumber;
    }

    public function getFax(): ?string
    {
        return $this->fax;
    }

    public function setFax(?string $fax)
    {
        $this->fax = $fax;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(?string $url)
    {
        $this->url = $url;
    }

    public function getLegalStatus(): ?string
    {
        return $this->legalStatus;
    }

    public function setLegalStatus(?string $legalStatus)
    {
        $this->legalStatus = $legalStatus;
    }

    public function getSiretNumber(): ?string
    {
        return $this->siretNumber;
    }

    public function setSiretNumber(?string $siretNumber)
    {
        $this->siretNumber = $siretNumber;
    }

    public function getVatNumber(): ?string
    {
        return $this->vatNumber;
    }

    public function setVatNumber(?string $vatNumber)
    {
        $this->vatNumber = $vatNumber;
    }

    public function getCapital(): ?string
    {
        return $this->capital;
    }

    public function setCapital(?string $capital)
    {
        $this->capital = $capital;
    }

    public function getRcs(): ?string
    {
        return $this->rcs;
    }

    public function setRcs(?string $rcs)
    {
        $this->rcs = $rcs;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug)
    {
        $this->slug = $slug;
    }
}
