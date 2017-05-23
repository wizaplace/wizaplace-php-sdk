<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\Order;

class ShippingAddress
{
    /** @var  string */
    private $firstname;
    /** @var  string */
    private $lastname;
    /** @var  string */
    private $address;
    /** @var  string */
    private $address2;
    /** @var  string */
    private $city;
    /** @var  string */
    private $zipcode;
    /** @var  string */
    private $country;

    public function __construct(array $data)
    {
        $this->firstname = $data['firstname'];
        $this->lastname = $data['lastname'];
        $this->address = $data['address'];
        $this->address2 = $data['address2'];
        $this->city = $data['city'];
        $this->zipcode = $data['zipcode'];
        $this->country = $data['country'];
    }

    public function getFirstname(): string
    {
        return $this->firstname;
    }

    public function getLastname(): string
    {
        return $this->lastname;
    }

    public function getAddress(): string
    {
        return $this->address;
    }

    public function getAddress2(): string
    {
        return $this->address2;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function getZipcode(): string
    {
        return $this->zipcode;
    }

    public function getCountry(): string
    {
        return $this->country;
    }
}
