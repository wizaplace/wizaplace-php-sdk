<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types=1);

namespace Wizaplace\SDK\Vendor\Order;

final class OrderAddress
{
    /** @var string */
    private $address;

    /** @var string */
    private $address2;

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

    /**
     * @internal
     */
    public function __construct(array $data)
    {
        $this->address = $data['address'];
        $this->address2 = $data['address_2'];
        $this->city = $data['city'];
        $this->countryCode = $data['country'];
        $this->firstName = $data['firstname'];
        $this->lastName = $data['lastname'];
        $this->phoneNumber = $data['phone'];
        $this->zipCode = $data['zipcode'];
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

    public function getCountryCode(): string
    {
        return $this->countryCode;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function getPhoneNumber(): string
    {
        return $this->phoneNumber;
    }

    public function getZipCode(): string
    {
        return $this->zipCode;
    }

    /**
     * @internal
     */
    public static function extractShippingAddressData(array $data)
    {
        return self::extractData('s_', $data);
    }

    /**
     * @internal
     */
    public static function extractBillingAddressData(array $data)
    {
        return self::extractData('b_', $data);
    }

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
