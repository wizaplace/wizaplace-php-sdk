<?php

/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */

declare(strict_types=1);

namespace  Wizaplace\SDK\Company;

/**
 * Class CompanyPerson
 * @package Wizaplace\SDK\Company
 */
final class CompanyPerson
{
    /** @var int|null */
    private $id;

    /** @var string */
    private $firstname;

    /** @var string */
    private $lastname;

    /** @var string */
    private $title;

    /** @var string */
    private $address;

    /** @var string|null */
    private $address2;

    /** @var string */
    private $city;

    /** @var string */
    private $state;

    /** @var string */
    private $zipcode;

    /** @var string */
    private $country;

    /** @var string */
    private $birthdate;

    /** @var string */
    private $birthplaceCity;

    /** @var string */
    private $birthplaceCountry;

    /** @var string */
    private $type;

    /** @var float|null */
    private $ownershipPercentage;

    /** @var string[] */
    private $nationalities;

    public function __construct(array $data)
    {
        $this->id = \array_key_exists('id', $data) === true ? $data['id'] : null;
        $this->firstname = \array_key_exists('firstname', $data) === true ? $data['firstname'] : '';
        $this->lastname = \array_key_exists('lastname', $data) === true ? $data['lastname'] : '';
        $this->title = \array_key_exists('title', $data) === true ? $data['title'] : '';
        $this->address = \array_key_exists('address', $data) === true ? $data['address'] : '';
        $this->address2 = \array_key_exists('address2', $data) === true ? $data['address2'] : null;
        $this->city = \array_key_exists('city', $data) === true ? $data['city'] : '';
        $this->state = \array_key_exists('state', $data) === true ? $data['state'] : '';
        $this->zipcode = \array_key_exists('zipcode', $data) === true ? $data['zipcode'] : '';
        $this->country = \array_key_exists('country', $data) === true ? $data['country'] : '';
        $this->nationalities = \array_key_exists('nationalities', $data) === true ? $data['nationalities'] : '';
        $this->birthdate = \array_key_exists('birthdate', $data) === true ? $data['birthdate'] : '';
        $this->birthplaceCity = \array_key_exists('birthplaceCity', $data) === true ? $data['birthplaceCity'] : '';
        $this->birthplaceCountry = \array_key_exists('birthplaceCountry', $data) === true ? $data['birthplaceCountry'] : '';
        $this->type = \array_key_exists('type', $data) === true ? $data['type'] : '';
        $this->ownershipPercentage = \array_key_exists('ownershipPercentage', $data) === true ? $data['ownershipPercentage'] : null;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function setFirstName(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function setLastName(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function setAddress(string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function setAddress2(?string $address2): self
    {
        $this->address2 = $address2;

        return $this;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function setState(string $state): self
    {
        $this->state = $state;

        return $this;
    }

    public function setZipCode(string $zipCode): self
    {
        $this->zipcode = $zipCode;

        return $this;
    }

    public function setCountry(string $country): self
    {
        $this->country = $country;

        return $this;
    }

    public function setBirthdate(string $birthdate): self
    {
        $this->birthdate = $birthdate;

        return $this;
    }

    public function setBirthplaceCity(string $birthplaceCity): self
    {
        $this->birthplaceCity = $birthplaceCity;

        return $this;
    }

    public function setBirthplaceCountry(string $birthplaceCountry): self
    {
        $this->birthplaceCountry = $birthplaceCountry;

        return $this;
    }

    public function setOwnershipPercentage(?float $ownershipPercentage): self
    {
        $this->ownershipPercentage = $ownershipPercentage;

        return $this;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function setNationalities(array $nationalities): self
    {
        foreach ($nationalities as $nationality) {
            $this->addNationality($nationality);
        }

        return $this;
    }

    public function addNationality(string $nationality): self
    {
        $this->nationalities[] = $nationality;

        return $this;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getFirstName(): string
    {
        return $this->firstname;
    }

    public function getLastName(): string
    {
        return $this->lastname;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getAddress(): string
    {
        return $this->address;
    }

    public function getAddress2(): ?string
    {
        return $this->address2;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function getState(): string
    {
        return $this->state;
    }

    public function getZipCode(): string
    {
        return $this->zipcode;
    }

    public function getCountry(): string
    {
        return $this->country;
    }

    public function getBirthdate(): string
    {
        return $this->birthdate;
    }

    public function getBirthplaceCity(): string
    {
        return $this->birthplaceCity;
    }

    public function getBirthplaceCountry(): string
    {
        return $this->birthplaceCountry;
    }

    public function getOwnershipPercentage(): ?float
    {
        return (float) $this->ownershipPercentage;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getNationalities(): array
    {
        return $this->nationalities;
    }

    public function jsonSerialize(): array
    {
        return [
            "firstname"           => $this->getFirstName(),
            "lastname"            => $this->getLastName(),
            "title"               => $this->getTitle(),
            "address"             => $this->getAddress(),
            "address2"            => $this->getAddress2(),
            "city"                => $this->getCity(),
            "state"               => $this->getState(),
            "zipcode"             => $this->getZipCode(),
            "country"             => $this->getCountry(),
            "nationalities"       => $this->getNationalities(),
            "birthdate"           => $this->getBirthdate(),
            "birthplaceCity"      => $this->getBirthplaceCity(),
            "birthplaceCountry"   => $this->getBirthplaceCountry(),
            "type"                => $this->getType(),
            "ownershipPercentage" => $this->getOwnershipPercentage()
        ];
    }
}
