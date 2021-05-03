<?php

/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Catalog;

use Wizaplace\SDK\Image\Image;

class DeclinationImages
{
    /** @var string */
    private $declinationId;

    /** @var Image[] */
    private $images;

    public function __construct(array $data)
    {
        $this->declinationId = $data['declinationId'];
        $this->images = $data['images'];
    }

    public function getDeclinationId(): string
    {
        return $this->declinationId;
    }

    /**
     * @return Image[]
     */
    public function getImages(): array
    {
        return $this->images;
    }
}
