<?php

/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Basket;

use Wizaplace\SDK\User\UserTitle;

/**
 * Class Address
 * @package Wizaplace\SDK\Basket
 */
final class Address
{
    /** @var null|UserTitle */
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

    /** @var null|string */
    private $email;

    /** @var null|string */
    private $state;

    /** @var null|string */
    private $division;

    /**
     * @internal
     */
    public function __construct(array $data)
    {
        $this->title = empty($data['title']) ? null : new UserTitle($data['title']);
        $this->firstName = $data['firstname'];
        $this->lastName = $data['lastname'];
        $this->company = $data['company'];
        $this->phone = $data['phone'];
        $this->address = $data['address'];
        $this->addressSecondLine = $data['address2'];
        $this->zipCode = $data['zipcode'];
        $this->city = $data['city'];
        $this->country = $data['country'];
        $this->email = $data['email'] ?? null;
        $this->state = $data['state'] ?? null;
        $this->division = $data['division'] ?? null;
    }

    /**
     * @return UserTitle|null
     */
    public function getTitle(): ?UserTitle
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getFirstName(): string
    {
        return $this->firstName;
    }

    /**
     * @return string
     */
    public function getLastName(): string
    {
        return $this->lastName;
    }

    /**
     * @return string
     */
    public function getCompany(): string
    {
        return $this->company;
    }

    /**
     * @return string
     */
    public function getPhone(): string
    {
        return $this->phone;
    }

    /**
     * @return string
     */
    public function getAddress(): string
    {
        return $this->address;
    }

    /**
     * @return string
     */
    public function getAddressSecondLine(): string
    {
        return $this->addressSecondLine;
    }

    /**
     * @return string
     */
    public function getZipCode(): string
    {
        return $this->zipCode;
    }

    /**
     * @return string
     */
    public function getCity(): string
    {
        return $this->city;
    }

    /**
     * @return string
     */
    public function getCountry(): string
    {
        return $this->country;
    }

    /** @return null|string */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /** @return null|string */
    public function getState(): ?string
    {
        return $this->state;
    }

    /** @return null|string */
    public function getDivision(): ?string
    {
        return $this->division;
    }
}
