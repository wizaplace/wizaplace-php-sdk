<?php

/**
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Catalog;

use Wizaplace\SDK\Image\Image;

use function theodorejb\polycast\to_int;
use function theodorejb\polycast\to_string;

/**
 * Class CompanyListItem
 * @package Wizaplace\SDK\Catalog
 */
final class CompanyListItem
{
    /** @var integer */
    private $id;

    /** @var string */
    private $name;

    /** @var null|string */
    private $corporateName;

    /** @var string */
    private $description;

    /** @var string */
    private $address;

    /** @var string */
    private $phoneNumber;

    /** @var boolean */
    private $professional;

    /** @var string */
    private $slug;

    /** @var null|Image */
    private $image;

    /** @var null|Location */
    private $location;

    /** @var null|integer */
    private $averageRating;

    /** @var CompanyAddress */
    private $fullAddress;

    /** @var array */
    private $extra = [];

    /**
     * @internal
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->id = to_int($data['id']);
        $this->name = to_string($data['name']);
        $this->corporateName = (\array_key_exists('corporateName', $data) === true) ? $data['corporateName'] : null;
        $this->description = to_string($data['description']);
        $this->address = to_string($data['address']);
        $this->phoneNumber = to_string($data['phoneNumber']);
        $this->professional = (bool) $data['professional'];
        $this->slug = to_string($data['slug']);
        $this->image = ($data['image'] !== null) ? new Image($data['image']) : null;
        if ($data['location'] !== null) {
            $this->location = new Location($data['location']['latitude'], $data['location']['longitude']);
        } else {
            $this->location = null;
        }
        $this->averageRating = $data['averageRating'];
        $this->fullAddress = new CompanyAddress($data['fullAddress']);

        $this->extra = $data['extra'] ?? [];
    }

    /**
     * @return CompanyAddress
     */
    public function getFullAddress(): CompanyAddress
    {
        return $this->fullAddress;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /** @return null|string */
    public function getCorporateName(): ?string
    {
        return $this->corporateName;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @deprecated use self::getFullAddress instead
     */
    public function getAddress(): string
    {
        return $this->address;
    }

    /**
     * @return string
     */
    public function getPhoneNumber(): string
    {
        return $this->phoneNumber;
    }

    /**
     * @return bool
     */
    public function isProfessional(): bool
    {
        return $this->professional;
    }

    /**
     * @return string
     */
    public function getSlug(): string
    {
        return $this->slug;
    }

    /**
     * @return Image|null
     */
    public function getImage(): ?Image
    {
        return $this->image;
    }

    /**
     * @return Location|null
     */
    public function getLocation(): ?Location
    {
        return $this->location;
    }

    /**
     * @return int|null
     */
    public function getAverageRating(): ?int
    {
        return $this->averageRating;
    }

    /**
     * @return array
     */
    public function getExtra(): array
    {
        return $this->extra;
    }
}
