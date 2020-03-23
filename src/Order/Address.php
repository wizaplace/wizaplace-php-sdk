<?php

/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Order;

/**
 * Class Address
 * @package Wizaplace\SDK\Order
 */
abstract class Address
{
    /** @var string */
    private $firstname;
    /** @var string */
    private $lastname;
    /** @var string */
    private $address;
    /** @var string */
    private $addressSecondLine;
    /** @var string */
    private $city;
    /** @var string */
    private $zipcode;
    /** @var string */
    private $country;
    /** @var ?string */
    private $phone;

    /**
     * @internal
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->firstname = $data['firstname'];
        $this->lastname = $data['lastname'];
        $this->address = $data['address'];
        $this->addressSecondLine = $data['address2'];
        $this->city = $data['city'];
        $this->zipcode = $data['zipcode'];
        $this->country = $data['country'];
        $this->phone = $data['phone'] ?? null;
    }

    /**
     * @return string
     */
    public function getFirstname(): string
    {
        return $this->firstname;
    }

    /**
     * @return string
     */
    public function getLastname(): string
    {
        return $this->lastname;
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
    public function getAddress2(): string
    {
        return $this->addressSecondLine;
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
    public function getZipcode(): string
    {
        return $this->zipcode;
    }

    /**
     * @return string
     */
    public function getCountry(): string
    {
        return $this->country;
    }

    /** @return string */
    public function getPhone(): ?string
    {
        return $this->phone;
    }
}
