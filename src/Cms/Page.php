<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */

namespace Wizaplace\Cms;

class Page
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $title;

    /**
     * @var string
     */
    private $content;

    /**
     * @var string
     */
    private $metaTitle;

    /**
     * @var string
     */
    private $metaDescription;

    /**
     * @var string
     */
    private $metaKeywords;

    /**
     * @var string
     */
    private $slug;

    public function __construct(
        int $id,
        string $title,
        string $content,
        string $metaTitle,
        string $metaDescription,
        string $metaKeywords,
        string $slug
    ) {
        $this->id = $id;
        $this->title = $title;
        $this->content = $content;
        $this->metaTitle = $metaTitle;
        $this->metaDescription = $metaDescription;
        $this->metaKeywords = $metaKeywords;
        $this->slug = $slug;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * Returns html content
     */
    public function getContent(): string
    {
        return $this->content;
    }

    public function getMetaTitle(): string
    {
        return $this->metaTitle;
    }

    public function getMetaDescription(): string
    {
        return $this->metaDescription;
    }

    public function getMetaKeywords(): string
    {
        return $this->metaKeywords;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }
}
