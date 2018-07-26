<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\SDK\Cms;

use Psr\Http\Message\UriInterface;

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
     * @internal
     */
    public function __construct(
        UriInterface $link,
        bool $shouldOpenInNewWindow,
        int $imageId,
        string $name
    ) {
        $this->link = $link;
        $this->shouldOpenInNewWindow = $shouldOpenInNewWindow;
        $this->imageId = $imageId;
        $this->name = $name;
    }

    public function getLink(): UriInterface
    {
        return $this->link;
    }

    public function getShouldOpenInNewWindow(): bool
    {
        return $this->shouldOpenInNewWindow;
    }

    public function getImageId(): int
    {
        return $this->imageId;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
