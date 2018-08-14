<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\SDK\Organisation;


use function theodorejb\polycast\to_string;

class OrganisationAddress
{
    /** @var string */
    private $address;

    /** @var string */
    private $additional_address;

    /** @var string */
    private $zip_code;

    /** @var string */
    private $city;

    /** @var string */
    private $state;

    /** @var string */
    private $country;

    public function __construct(array $data)
    {
        $this->address = to_string($data['address']);
        $this->additional_address = to_string($data['additional_address']);
        $this->zip_code = to_string($data['zip_code']);
        $this->city = to_string($data['city']);
        $this->state = to_string($data['state']);
        $this->country = to_string($data['country']);
    }

    public function toArray()
    {
        $data = [];

        $data['address'] = isset($this->address) ? $this->address : '';
        $data['additionalAddress'] = isset($this->additional_address) ? $this->additional_address : '';
        $data['zipCode'] = isset($this->zip_code) ? $this->zip_code : '';
        $data['city'] = isset($this->city) ? $this->city : '';
        $data['state'] = isset($this->state) ? $this->state : '';
        $data['country'] = isset($this->country) ? $this->country : '';

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
        return $this->additional_address;
    }

    /**
     * @param string $additional_address
     */
    public function setAdditionalAddress(string $additional_address): void
    {
        $this->additional_address = $additional_address;
    }

    /**
     * @return string
     */
    public function getZipCode(): string
    {
        return $this->zip_code;
    }

    /**
     * @param string $zip_code
     */
    public function setZipCode(string $zip_code): void
    {
        $this->zip_code = $zip_code;
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
