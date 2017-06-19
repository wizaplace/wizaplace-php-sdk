<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\Cms;

class Banner
{
    /**
     * @var string
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

    public function __construct(
        string $link,
        bool $shouldOpenInNewWindow,
        int $imageId
    ) {
        $this->link = $link;
        $this->shouldOpenInNewWindow = $shouldOpenInNewWindow;
        $this->imageId = $imageId;
    }

    public function getLink(): string
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
}
