<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types=1);

namespace Wizaplace\SDK\Vendor\Order;

use Wizaplace\SDK\User\UserTitle;

/**
 * Class OrderAddress
 * @package Wizaplace\SDK\Vendor\Order
 */
final class OrderAddress
{
    /** @var string */
    private $address;

    /** @var string */
    private $complementaryAddress;

    /** @var string */
    private $city;

    /** @var string */
    private $countryCode;

    /** @var string */
    private $firstName;

    /** @var string */
    private $lastName;

    /** @var string */
    private $phoneNumber;

    /** @var string */
    private $zipCode;

    /** @var string */
    private $company;

    /** @var string */
    private $title;

    /**
     * @internal
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->address = $data['address'];
        $this->complementaryAddress = $data['address_2'];
        $this->city = $data['city'];
        $this->countryCode = $data['country'];
        $this->firstName = $data['firstname'];
        $this->lastName = $data['lastname'];
        $this->phoneNumber = $data['phone'];
        $this->zipCode = $data['zipcode'];
        $this->company = $data['company'];
        $this->title = array_key_exists('title', $data) ? $data['title'] : UserTitle::MR();
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
    public function getComplementaryAddress(): string
    {
        return $this->complementaryAddress;
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
    public function getCountryCode(): string
    {
        return $this->countryCode;
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
    public function getPhoneNumber(): string
    {
        return $this->phoneNumber;
    }

    /**
     * @return string
     */
    public function getZipCode(): string
    {
        return $this->zipCode;
    }

    /** @return string */
    public function getCompany(): string
    {
        return $this->company;
    }


    /**
     * @return string
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @internal
     *
     * @param array $data
     *
     * @return OrderAddress
     */
    public static function extractShippingAddressData(array $data)
    {
        return self::extractData('s_', $data);
    }

    /**
     * @internal
     *
     * @param array $data
     *
     * @return OrderAddress
     */
    public static function extractBillingAddressData(array $data)
    {
        return self::extractData('b_', $data);
    }

    /**
     * @param string $prefix
     * @param array  $data
     *
     * @return OrderAddress
     */
    private static function extractData(string $prefix, array $data)
    {
        $newData = [];
        $prefixLength = strlen($prefix);
        foreach ($data as $key => $value) {
            if (strpos($key, $prefix) !== 0) {
                continue;
            }
            $key = substr($key, $prefixLength);
            $newData[$key] = $value;
        }

        return new self($newData);
    }
}
