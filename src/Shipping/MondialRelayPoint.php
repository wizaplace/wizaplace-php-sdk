<?php

/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Shipping;

use function theodorejb\polycast\to_int;

/**
 * Class MondialRelayPoint
 * @package Wizaplace\SDK\Shipping
 */
class MondialRelayPoint
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string[]
     */
    private $address;

    /**
     * @var string
     */
    private $zipCode;

    /**
     * @var string
     */
    private $city;

    /**
     * @var string
     */
    private $country;

    /**
     * @var string|null
     */
    private $location1; // phpcs:ignore

    /**
     * @var string|null
     */
    private $location2; // phpcs:ignore

    /**
     * @var string
     */
    private $latitude;

    /**
     * @var string
     */
    private $longitude;

    /**
     * @var string
     */
    private $activityType;

    /**
     * @var string
     */
    private $information;

    /**
     * @var MondialRelayOpening[]
     */
    private $openingHours;

    /**
     * @var string
     */
    private $availabilityInformation;

    /**
     * @var string
     */
    private $urlPicture;

    /**
     * @var string
     */
    private $urlMap;

    /**
     * @var int
     *
     * The distance in meters.
     */
    private $distance;

    /**
     * MondialRelayPoint constructor.
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->id = $data['id'];
        $this->address = $data['address'];
        $this->zipCode = $data['zipCode'];
        $this->city = $data['city'];
        $this->country = $data['country'];
        $this->location1 = $data['location1'];
        $this->location2 = $data['location2'];
        $this->latitude = $data['latitude'];
        $this->longitude = $data['longitude'];
        $this->activityType = $data['activityType'];
        $this->information = $data['information'];
        $this->openingHours = array_map(
            function ($elt) {
                return new MondialRelayOpening($elt);
            },
            $data['openingHours']
        );
        $this->availabilityInformation = $data['availabilityInformation'];
        $this->urlPicture = $data['urlPicture'];
        $this->urlMap = $data['urlMap'];
        $this->distance = to_int($data['distance']);
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return array
     */
    public function getAddress(): array
    {
        return $this->address;
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

    /**
     * @return string|null
     */
    public function getLocation1(): ?string
    {
        return $this->location1;
    }

    /**
     * @return string|null
     */
    public function getLocation2(): ?string
    {
        return $this->location2;
    }

    /**
     * @return string
     */
    public function getLatitude(): string
    {
        return $this->latitude;
    }

    /**
     * @return string
     */
    public function getLongitude(): string
    {
        return $this->longitude;
    }

    /**
     * @return string
     */
    public function getActivityType(): string
    {
        return $this->activityType;
    }

    /**
     * @return string|null
     */
    public function getInformation(): ?string
    {
        return $this->information;
    }

    /**
     * @return array
     */
    public function getOpeningHours(): array
    {
        return $this->openingHours;
    }

    /**
     * @return string|null
     */
    public function getAvailabilityInformation(): ?string
    {
        return $this->availabilityInformation;
    }

    /**
     * @return string
     */
    public function getUrlPicture(): string
    {
        return $this->urlPicture;
    }

    /**
     * @return string
     */
    public function getUrlMap(): string
    {
        return $this->urlMap;
    }

    /**
     * @return int
     */
    public function getDistance(): int
    {
        return $this->distance;
    }
}
