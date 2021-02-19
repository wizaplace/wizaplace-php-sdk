<?php

/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Image;

use GuzzleHttp\Psr7\Uri;
use Psr\Http\Message\UriInterface;
use Wizaplace\SDK\AbstractService;

use function theodorejb\polycast\to_string;

/**
 * Class ImageService
 * @package Wizaplace\SDK\Image
 */
final class ImageService extends AbstractService implements ImageServiceInterface
{
    /**
     * @inheritdoc
     */
    public function getImageLink(int $imageId, int $width = null, int $height = null): UriInterface
    {
        $query = http_build_query(array_filter(['w' => $width, 'h' => $height]));

        $apiBaseUrl = rtrim(to_string($this->client->getBaseUri()), '/');

        return new Uri("{$apiBaseUrl}/image/${imageId}?${query}");
    }
}
