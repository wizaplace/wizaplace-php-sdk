<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types=1);

namespace Wizaplace\SDK\Seo;

class Metadata
{
    /** @var null|string */
    private $title;

    /** @var null|string */
    private $description;

    /** @var null|string */
    private $keywords;

    public function __construct(array $data = [])
    {
        $this->title = $data['title'] ?? null;
        $this->description = $data['description'] ?? null;
        $this->keywords = $data['keywords'] ?? null;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }


    public function setKeywords(?string $keywords): self
    {
        $this->keywords = $keywords;

        return $this;
    }

    public function getKeywords(): ?string
    {
        return $this->keywords;
    }
}
