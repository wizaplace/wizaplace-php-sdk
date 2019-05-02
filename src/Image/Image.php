<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types=1);

namespace Wizaplace\SDK\Image;

use GuzzleHttp\Psr7\Uri;
use Psr\Http\Message\UriInterface;
use Wizaplace\SDK\Exception\NotFound;

/**
 * Class Image.
 */
final class Image
{
    /** @var int */
    private $id;

    /** @var UriInterface[] */
    private $url;

    /**
     * @internal
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->id = $data['id'];

        foreach ($data['url'] as $key => $val) {
            $this->url[$key] = new Uri($val);
        }
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return UriInterface[]
     */
    public function getUrls(): array
    {
        return $this->url;
    }

    public function getUrl(string $size = 'original'): UriInterface
    {
        if (false === \array_key_exists($size, $this->url)) {
            throw new NotFound(
                "Invalid size '$size'. Available sizes : ".implode(', ', array_keys($this->url))
            );
        }

        return $this->url[$size];
    }
}
