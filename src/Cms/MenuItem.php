<?php

/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Cms;

use Psr\Http\Message\UriInterface;

/**
 * Class MenuItem
 * @package Wizaplace\SDK\Cms
 */
final class MenuItem
{
    /** @var string */
    private $name;

    /** @var int */
    private $position;

    /** @var UriInterface */
    private $url;

    /** @var MenuItem[] */
    private $children;
    /**
     * @var bool
     */
    private $targetBlank;

    /**
     * @internal
     *
     * @param string       $name
     * @param int          $position
     * @param UriInterface $url
     * @param bool         $targetBlank
     * @param array        $children
     */
    public function __construct(string $name, int $position, UriInterface $url, bool $targetBlank, array $children)
    {
        $this->name = $name;
        $this->position = $position;
        $this->url = $url;
        $this->targetBlank = $targetBlank;
        $this->children = $children;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getPosition(): int
    {
        return $this->position;
    }

    /**
     * @return UriInterface
     */
    public function getUrl(): UriInterface
    {
        return $this->url;
    }

    /**
     * @return bool
     */
    public function isTargetBlank(): bool
    {
        return $this->targetBlank;
    }

    /**
     * @return MenuItem[]
     */
    public function getChildren(): array
    {
        return $this->children;
    }
}
