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

    /** @var boolean */
    private $professional;

    /** @var string */
    private $slug;

    /** @var null|Image */
    private $image;

    /** @var null|Location */
    private $location;

    public function __construct($data)
    {
        $this->id = (int) $data['id'];
        $this->name = (string) $data['name'];
        $this->description = (string) $data['description'];
        $this->professional = (bool) $data['professional'];
        $this->slug = (string) $data['slug'];
        $this->image = ($data['image'] !== null) ? new Image($data['image']) : null;
        if ($data['location'] !== null) {
            $this->location = new Location($data['location']['latitude'], $data['location']['longitude']);
        } else {
            $this->location = null;
        }
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
}
