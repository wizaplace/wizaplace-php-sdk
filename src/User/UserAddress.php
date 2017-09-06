<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\User;

final class UserAddress
{
    /** @var string */
    private $title;

    /** @var string */
    private $firstName;

    /** @var string */
    private $lastName;

    /** @var string */
    private $company;

    /** @var string */
    private $phone;

    /** @var string */
    private $address;

    /** @var string */
    private $addressSecondLine;

    /** @var string */
    private $zipCode;

    /** @var string */
    private $city;

    /** @var string */
    private $country;

    /** @var array */
    private $extraFields;

    /**
     * @internal
     */
    public function __construct(array $data)
    {
        $this->title = $data['title'];
        unset($data['title']);

        $this->firstName = $data['firstname'];
        unset($data['firstname']);

        $this->lastName = $data['lastname'];
        unset($data['lastname']);

        $this->company = $data['company'];
        unset($data['company']);

        $this->phone = $data['phone'];
        unset($data['phone']);

        $this->address = $data['address'];
        unset($data['address']);

        $this->addressSecondLine = $data['address_2'];
        unset($data['address_2']);

        $this->zipCode = $data['zipcode'];
        unset($data['zipcode']);

        $this->city = $data['city'];
        unset($data['city']);

        $this->country = $data['country'];
        unset($data['country']);

        $this->extraFields = $data;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function getCompany(): string
    {
        return $this->company;
    }

    public function getPhone(): string
    {
        return $this->phone;
    }

    public function getAddress(): string
    {
        return $this->address;
    }

    public function getAddressSecondLine(): string
    {
        return $this->addressSecondLine;
    }

    public function getZipCode(): string
    {
        return $this->zipCode;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function getCountry(): string
    {
        return $this->country;
    }

    /**
     * @deprecated
     * @return array name => value of extra fields
     */
    public function getExtraFields(): array
    {
        return $this->extraFields;
    }
}
