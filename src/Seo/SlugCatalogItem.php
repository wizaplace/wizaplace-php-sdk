<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types=1);

namespace Wizaplace\SDK\Seo;

class SlugCatalogItem
{
    /** @var string */
    private $slug;
    /** @var SlugTarget */
    private $target;
    /** @var self[] */
    private $categoryPath;

    /**
     * @internal
     */
    public function __construct(array $data)
    {
        $this->slug = $data['slug'];
        $this->target = new SlugTarget(new SlugTargetType($data['target']['type']), $data['target']['id']);
        $this->categoryPath = array_map(function (array $categoryData): self {
            return new self($categoryData);
        }, $data['categoryPath'] ?? []);
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getTarget(): SlugTarget
    {
        return $this->target;
    }

    /**
     * @return self[]
     */
    public function getCategoryPath(): array
    {
        return $this->categoryPath;
    }
}
