<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types=1);

namespace Wizaplace\SDK\Image;

use Wizaplace\SDK\Exception\NotFound;

/**
 * Class Image.
 */
final class Image
{
    /** @var int */
    private $id;

    /** @var array */
    private $url;

    /**
     * @internal
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->id = $data['id'];
        $this->url = $data['url'];
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    public function getUrls(): array
    {
        return $this->url;
    }

    public function getUrl(string $size = 'original'): string
    {
        $sizes = [
            'original',
            'large',
            'medium',
            'small',
        ];

        if (!in_array($size, $sizes)) {
            throw new NotFound("Invalid size ($size). Must be one of these".implode(', ', $sizes));
        }

        return $this->url[$size];
    }
}
