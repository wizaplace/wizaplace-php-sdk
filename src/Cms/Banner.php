<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\SDK\Cms;

use Psr\Http\Message\UriInterface;

/**
 * Class Banner
 * @package Wizaplace\SDK\Cms
 */
final class Banner
{
    /**
     * @var UriInterface
     */
    private $link;

    /**
     * @var bool
     */
    private $shouldOpenInNewWindow;

    /**
     * @var int
     */
    private $imageId;

    /**
     * @var string
     */
    private $name;

    /**
     * @var null|string
     */
    private $alt;

    /**
     * @internal
     *
     * @param UriInterface $link
     * @param bool         $shouldOpenInNewWindow
     * @param int          $imageId
     * @param string       $name
     * @param null|string  $alt
     */
    public function __construct(
        UriInterface $link,
        bool $shouldOpenInNewWindow,
        int $imageId,
        string $name,
        ?string $alt
    ) {
        $this->link = $link;
        $this->shouldOpenInNewWindow = $shouldOpenInNewWindow;
        $this->imageId = $imageId;
        $this->name = $name;
        $this->alt = $alt;
    }

    /**
     * @return UriInterface
     */
    public function getLink(): UriInterface
    {
        return $this->link;
    }

    /**
     * @return bool
     */
    public function getShouldOpenInNewWindow(): bool
    {
        return $this->shouldOpenInNewWindow;
    }

    /**
     * @return int
     */
    public function getImageId(): int
    {
        return $this->imageId;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return null|string
     */
    public function getAlt(): ?string
    {
        return $this->alt;
    }
}
