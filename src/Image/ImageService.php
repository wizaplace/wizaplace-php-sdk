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

/**
 * Class ImageService
 * @package Wizaplace\SDK\Image
 */
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
        static $cache = [];

        $uid = "id$imageId.w$width.h$height";

        if (!isset($cache[$uid])) {
            $query = http_build_query(
                array_filter([
                    'w' => $width,
                    'h' => $height,
                ])
            );

            $cache[$uid] = $this->client->get(
                "image/$imageId.json".($query ? "?$query" :  '')
            )['url'];
        }

        return new Uri($cache[$uid]);
    }
}
