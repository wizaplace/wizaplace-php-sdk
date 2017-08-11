<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\Catalog;

use Wizaplace\Image\Image;

class CompanyDetail
{
    /** @var integer */
    private $id;

    /** @var string */
    private $name;

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

    /**
     * @internal
     */
    public function __construct(array $data)
    {
        $this->id = (int) $data['id'];
        $this->name = (string) $data['name'];
        $this->description = (string) $data['description'];
        $this->address = (string) $data['address'];
        $this->phoneNumber = (string) $data['phoneNumber'];
        $this->professional = (bool) $data['professional'];
        $this->slug = (string) $data['slug'];
        $this->image = ($data['image'] !== null) ? new Image($data['image']) : null;
        if ($data['location'] !== null) {
            $this->location = new Location($data['location']['latitude'], $data['location']['longitude']);
        } else {
            $this->location = null;
        }
        $this->averageRating = $data['averageRating'];
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getAddress(): string
    {
        return $this->address;
    }

    public function getPhoneNumber(): string
    {
        return $this->phoneNumber;
    }

    public function isProfessional(): bool
    {
        return $this->professional;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getImage(): ?Image
    {
        return $this->image;
    }

    public function getLocation(): ?Location
    {
        return $this->location;
    }

    public function getAverageRating(): ?int
    {
        return $this->averageRating;
    }
}
