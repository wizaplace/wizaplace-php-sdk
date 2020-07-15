<?php

/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */

declare(strict_types=1);

namespace Wizaplace\SDK\User;

/**
 * Class UpdateAddressBookCommand
 * @package Wizaplace\SDK\User
 */
final class UpdateAddressBookCommand
{
    /** @var string */
    private $id;

    /** @var string|null */
    private $label;

    /** @var UserTitle|null */
    private $title;

    /** @var string|null */
    private $firstName;

    /** @var string|null */
    private $lastName;

    /** @var string|null */
    private $company;

    /** @var string|null */
    private $phone;

    /** @var string|null */
    private $address;

    /** @var string|null */
    private $addressSecondLine;

    /** @var string|null */
    private $zipCode;

    /** @var string|null */
    private $city;

    /** @var string|null */
    private $country;

    /** @var string|null */
    private $divisionCode;

    /** @var string|null */
    private $comment;

    /** @var string|null */
    private $fromUserAddress;

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @param string $id
     *
     * @return UpdateAddressBookCommand
     */
    public function setId(string $id): self
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getLabel(): ?string
    {
        return $this->label;
    }

    /**
     * @param string|null $label
     *
     * @return UpdateAddressBookCommand
     */
    public function setLabel(?string $label): self
    {
        $this->label = $label;

        return $this;
    }

    /**
     * @return UserTitle|null
     */
    public function getTitle(): ?UserTitle
    {
        return $this->title;
    }

    /**
     * @param UserTitle|null $title
     *
     * @return UpdateAddressBookCommand
     */
    public function setTitle(?UserTitle $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    /**
     * @param string|null $firstName
     *
     * @return UpdateAddressBookCommand
     */
    public function setFirstName(?string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    /**
     * @param string|null $lastName
     *
     * @return UpdateAddressBookCommand
     */
    public function setLastName(?string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getCompany(): ?string
    {
        return $this->company;
    }

    /**
     * @param string|null $company
     *
     * @return UpdateAddressBookCommand
     */
    public function setCompany(?string $company): self
    {
        $this->company = $company;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getPhone(): ?string
    {
        return $this->phone;
    }

    /**
     * @param string|null $phone
     *
     * @return UpdateAddressBookCommand
     */
    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

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
     * @return UpdateAddressBookCommand
     */
    public function setAddress(?string $address): self
    {
        $this->address = $address;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getAddressSecondLine(): ?string
    {
        return $this->addressSecondLine;
    }

    /**
     * @param string|null $addressSecondLine
     *
     * @return UpdateAddressBookCommand
     */
    public function setAddressSecondLine(?string $addressSecondLine): self
    {
        $this->addressSecondLine = $addressSecondLine;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getZipCode(): ?string
    {
        return $this->zipCode;
    }

    /**
     * @param string|null $zipCode
     *
     * @return UpdateAddressBookCommand
     */
    public function setZipCode(?string $zipCode): self
    {
        $this->zipCode = $zipCode;

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
     * @return UpdateAddressBookCommand
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
     * @param string $country 2 letters country code
     * @return self
     */
    public function setCountry(?string $country): self
    {
        $this->country = $country;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getDivisionCode(): ?string
    {
        return $this->divisionCode;
    }

    /**
     * @param string|null $divisionCode
     *
     * @return UpdateAddressBookCommand
     */
    public function setDivisionCode(?string $divisionCode): self
    {
        $this->divisionCode = $divisionCode;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getComment(): ?string
    {
        return $this->comment;
    }

    /**
     * @param string|null $comment
     *
     * @return UpdateAddressBookCommand
     */
    public function setComment(?string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getFromUserAddress(): ?string
    {
        return $this->fromUserAddress;
    }

    /**
     * @param string|null $fromUserAddress
     *
     * @return UpdateAddressBookCommand
     */
    public function setFromUserAddress(?string $fromUserAddress): self
    {
        $this->fromUserAddress = $fromUserAddress;

        return $this;
    }
}
