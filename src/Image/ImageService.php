<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */

namespace Wizaplace\Image;

use Wizaplace\AbstractService;

class ImageService extends AbstractService
{

    public function getImageLink(int $imageId, int $width = null, int $height = null): string
    {
        $query = http_build_query(array_filter(['w' => $width, 'h' => $height]));

        return $this->baseUrl."/image/${imageId}?${query}";
    }
}
