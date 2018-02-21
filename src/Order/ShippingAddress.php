<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\SDK\Order;

final class ShippingAddress
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

    /**
     * @internal
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
        return $this->addressSecondLine;
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
