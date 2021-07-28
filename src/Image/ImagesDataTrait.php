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
    public function getImagesWithAltText(array $data): array
    {
        if (\array_key_exists('images', $data) === true) {
            return array_map(
                static function (array $image): Image {
                    return new Image($image);
                },
                $data['images']
            );
        }

        return [];
    }
}
