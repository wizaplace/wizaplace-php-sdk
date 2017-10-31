<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\SDK\Catalog;

use GuzzleHttp\Psr7\Uri;
use Psr\Http\Message\UriInterface;

final class ProductVideo
{
    /** @var UriInterface */
    private $thumbnailUrl;

    /** @var UriInterface */
    private $videoUrl;

    /**
     * @internal
     */
    public function __construct(array $data)
    {
        $this->thumbnailUrl = new Uri($data['thumbnailUrl']);
        $this->videoUrl = new Uri($data['videoUrl']);
    }

    public function getThumbnailUrl(): UriInterface
    {
        return $this->thumbnailUrl;
    }

    public function getVideoUrl(): UriInterface
    {
        return $this->videoUrl;
    }
}
