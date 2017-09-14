<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\SDK\Catalog;

final class ProductVideo
{
    /** @var string */
    private $thumbnailUrl;

    /** @var string */
    private $videoUrl;

    /**
     * @internal
     */
    public function __construct(array $data)
    {
        $this->thumbnailUrl = $data['thumbnailUrl'];
        $this->videoUrl = $data['videoUrl'];
    }

    public function getThumbnailUrl(): string
    {
        return $this->thumbnailUrl;
    }

    public function getVideoUrl(): string
    {
        return $this->videoUrl;
    }
}
