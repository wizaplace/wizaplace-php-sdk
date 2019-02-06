<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\SDK\Catalog;

use GuzzleHttp\Psr7\Uri;
use Psr\Http\Message\UriInterface;

/**
 * Class ProductVideo
 * @package Wizaplace\SDK\Catalog
 */
final class ProductVideo
{
    /** @var UriInterface */
    private $thumbnailUrl;

    /** @var UriInterface */
    private $videoUrl;

    /**
     * @internal
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->thumbnailUrl = new Uri($data['thumbnailUrl']);
        $this->videoUrl = new Uri($data['videoUrl']);
    }

    /**
     * @return UriInterface
     */
    public function getThumbnailUrl(): UriInterface
    {
        return $this->thumbnailUrl;
    }

    /**
     * @return UriInterface
     */
    public function getVideoUrl(): UriInterface
    {
        return $this->videoUrl;
    }
}
