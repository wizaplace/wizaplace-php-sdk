<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\SDK\Image;

use GuzzleHttp\Psr7\Uri;
use Psr\Http\Message\UriInterface;
use Wizaplace\SDK\AbstractService;
use function theodorejb\polycast\to_string;

final class ImageService extends AbstractService
{
    /**
     * Return the public URL of an image.
     *
     * The URL returned can be used to display the image, for example by using an
     * <img src="..."> tag in HTML code.
     *
     * @param int $imageId
     * @param int|null $width You can optionally constraint the max width of the image.
     * @param int|null $height You can optionally constraint the max height of the image.
     *
     * @return UriInterface Image URL
     */
    public function getImageLink(int $imageId, int $width = null, int $height = null): UriInterface
    {
        $query = http_build_query(array_filter(['w' => $width, 'h' => $height]));

        $apiBaseUrl = rtrim(to_string($this->client->getBaseUri()), '/');

        return new Uri("{$apiBaseUrl}/image/${imageId}?${query}");
    }
}
