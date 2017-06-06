<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\Image;

use Wizaplace\AbstractService;

class ImageService extends AbstractService
{

    public function getImageLink(int $imageId, int $width = null, int $height = null): string
    {
        $query = http_build_query(array_filter(['w' => $width, 'h' => $height]));

        $apiBaseUrl = rtrim((string) $this->client->getConfig('base_uri'), '/');

        return "{$apiBaseUrl}/image/${imageId}?${query}";
    }
}
