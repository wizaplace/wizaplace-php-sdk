<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\Catalog;

use Wizaplace\Image\Image;

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

    /**
     * @internal
     */
    public function __construct(int $id, string $name, string $slug, ?Image $image)
    {
        $this->id = $id;
        $this->name = $name;
        $this->slug = $slug;
        $this->image = $image;
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
