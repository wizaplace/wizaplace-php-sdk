<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\Catalog;

class Company
{
    /** @var integer */
    private $id;

    /** @var string */
    private $name;

    /** @var null|string */
    private $description;

    /** @var null|boolean */
    private $professional;

    /** @var null|string */
    private $slug;

    /** @var null|integer */
    private $imageId;

    /** @var null|float */
    private $latitude;

    /** @var null|float */
    private $longitude;

    public function __construct($data)
    {
        $this->id = $data['id'];
        $this->name = $data['name'];
        $this->description = $data['description'];
        $this->professional = $data['professional'];
        $this->slug = $data['slug'];
        $this->imageId = $data['imageId'];
        $this->latitude = $data['latitude'];
        $this->longitude = $data['longitude'];
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function isProfessional(): ?bool
    {
        return $this->professional;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function getImageId(): ?int
    {
        return $this->imageId;
    }

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }
}
