<?php
declare(strict_types = 1);

namespace Wizaplace\Favorite\Declination;

use Wizaplace\Image\Image;

final class DeclinationCompany
{
    /** @var int */
    private $id;

    /** @var string */
    private $name;

    /** @var string */
    private $slug;

    /** @var Image|null  */
    private $image;

    public function __construct(array $data) {
        $this->id = $data['id'];
        $this->name = $data['name'];
        $this->slug = $data['slug'];
        $this->image = isset($data['image']) ? new Image($data['image']) : null;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getImage(): ?Image
    {
        return $this->image;
    }
}
