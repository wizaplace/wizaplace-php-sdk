<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\SDK\Organisation;

use function theodorejb\polycast\to_string;
use Wizaplace\SDK\ArrayableInterface;

class OrganisationAddress implements ArrayableInterface
{
    /** @var string */
    private $address;

    /** @var string */
    private $additionalAddress;

    /** @var string */
    private $zipCode;

    /** @var string */
    private $city;

    /** @var string */
    private $state;

    /** @var string */
    private $country;

    public function __construct(array $data)
    {
        $this->address = to_string($data['address']);
        $this->additionalAddress = to_string($data['additionalAddress']);
        $this->zipCode = to_string($data['zipCode']);
        $this->city = to_string($data['city']);
        $this->state = to_string($data['state']);
        $this->country = to_string($data['country']);
    }

    public function toArray() : array
    {
        $data = [];

        $data['address'] = $this->address;
        $data['additionalAddress'] = $this->additionalAddress;
        $data['zipCode'] = $this->zipCode;
        $data['city'] = $this->city;
        $data['state'] = $this->state;
        $data['country'] = $this->country;

        return $data;
    }

    /**
     * @return string
     */
    public function getAddress(): string
    {
        return $this->address;
    }

    /**
     * @param string $address
     */
    public function setAddress(string $address): void
    {
        $this->address = $address;
    }

    /**
     * @return string
     */
    public function getAdditionalAddress(): string
    {
        return $this->additionalAddress;
    }

    /**
     * @param string $additionalAddress
     */
    public function setAdditionalAddress(string $additionalAddress): void
    {
        $this->additionalAddress = $additionalAddress;
    }

    /**
     * @return string
     */
    public function getZipCode(): string
    {
        return $this->zipCode;
    }

    /**
     * @param string $zipCode
     */
    public function setZipCode(string $zipCode): void
    {
        $this->zipCode = $zipCode;
    }

    /**
     * @return string
     */
    public function getCity(): string
    {
        return $this->city;
    }

    /**
     * @param string $city
     */
    public function setCity(string $city): void
    {
        $this->city = $city;
    }

    /**
     * @return string
     */
    public function getState(): string
    {
        return $this->state;
    }

    /**
     * @param string $state
     */
    public function setState(string $state): void
    {
        $this->state = $state;
    }

    /**
     * @return string
     */
    public function getCountry(): string
    {
        return $this->country;
    }

    /**
     * @param string $country
     */
    public function setCountry(string $country): void
    {
        $this->country = $country;
    }
}
