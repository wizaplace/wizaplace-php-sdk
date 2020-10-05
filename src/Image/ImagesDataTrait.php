<?php

/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Image;

trait ImagesDataTrait
{
    /** @return Image[]|array */
    public function getImagesDataWithAltText(array $data): array
    {
        if (\array_key_exists('imagesData', $data) === true) {
            return array_map(
                static function (array $imageData): Image {
                    return new Image($imageData);
                },
                $data['imagesData']
            );
        }

        return [];
    }
}
