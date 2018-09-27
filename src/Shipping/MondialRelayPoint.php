<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\SDK\Shipping;

use function theodorejb\polycast\to_int;

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
        $this->openingHours = array_map(function ($elt) {
            return new MondialRelayOpening($elt);
        }, $data['openingHours']);
        $this->availabilityInformation = $data['availabilityInformation'];
        $this->urlPicture = $data['urlPicture'];
        $this->urlMap = $data['urlMap'];
        $this->distance = to_int($data['distance']);
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getAddress(): array
    {
        return $this->address;
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

    public function getLocation1(): ?string
    {
        return $this->location1;
    }

    public function getLocation2(): ?string
    {
        return $this->location2;
    }

    public function getLatitude(): string
    {
        return $this->latitude;
    }

    public function getLongitude(): string
    {
        return $this->longitude;
    }

    public function getActivityType(): string
    {
        return $this->activityType;
    }

    public function getInformation(): ?string
    {
        return $this->information;
    }

    public function getOpeningHours(): array
    {
        return $this->openingHours;
    }

    public function getAvailabilityInformation(): ?string
    {
        return $this->availabilityInformation;
    }

    public function getUrlPicture(): string
    {
        return $this->urlPicture;
    }

    public function getUrlMap(): string
    {
        return $this->urlMap;
    }

    public function getDistance(): int
    {
        return $this->distance;
    }
}
