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

    /** @var Image */
    private $image;

    /** @var Location */
    private $location;

    public function __construct($data)
    {
        //image & location can be null right now, setting those to default value if they are null
        $image = (isset($data['image'])) ? $data['image'] : [];
        $latitude = (isset($data['location']['latitude'])) ? $data['location']['latitude'] : 0;
        $longitude = (isset($data['location']['longitude'])) ? $data['location']['longitude'] : 0;

        $this->id = $data['id'];
        $this->name = $data['name'];
        $this->description = $data['description'];
        $this->professional = $data['professional'];
        $this->slug = $data['slug'];
        $this->image = new Image($image);
        $this->location = new Location($latitude, $longitude);
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

    public function getImage(): Image
    {
        return $this->image;
    }

    public function getLocation(): Location
    {
        return $this->location;
    }
}
