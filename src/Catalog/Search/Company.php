<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\SDK\Catalog\Search;

use Wizaplace\SDK\Image\Image;

final class Company
{
    /** @var int */
    private $id;

    /** @var string */
    private $name;

    /** @var string */
    private $slug;

    /** @var null|Image */
    private $image;

    /** @var bool */
    private $professional;

    /** @var float|null */
    private $averageRating;

    /**
     * @internal
     */
    public function __construct(array $data)
    {
        $this->id = $data['id'];
        $this->name = $data['name'];
        $this->slug = $data['slug'];
        $this->image = isset($data['image']) ? new Image($data['image']) : null;
        $this->professional = $data['isProfessional'];
        $this->averageRating = $data['averageRating'] ?? null;
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

    public function isProfessional(): bool
    {
        return $this->professional;
    }

    public function getAverageRating(): ?float
    {
        return $this->averageRating;
    }
}
