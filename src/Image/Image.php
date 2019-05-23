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
    private $urls;

    /**
     * @internal
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->id = $data['id'];

        foreach ($data['urls'] as $key => $url) {
            $this->urls[$key] = new Uri($url);
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
        return $this->urls;
    }

    public function getUrl(string $size = 'original'): UriInterface
    {
        if (false === \array_key_exists($size, $this->urls)) {
            throw new NotFound(
                "Invalid size '$size'. Available sizes : ".implode(', ', array_keys($this->urls))
            );
        }

        return $this->urls[$size];
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'urls' => array_map(
                function ($uri) {
                    return (string) $uri;
                },
                $this->urls
            ),
        ];
    }
}
