<?php

/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Catalog;

use Wizaplace\SDK\Image\Image;

/**
 * Class CompanySummary
 * @package Wizaplace\SDK\Catalog
 */
final class CompanySummary
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
     *
     * @param array $data
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
     * @return bool
     */
    public function isProfessional(): bool
    {
        return $this->professional;
    }

    /**
     * @return float|null
     */
    public function getAverageRating(): ?float
    {
        return $this->averageRating;
    }
}
