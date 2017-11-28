<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types=1);

namespace Wizaplace\SDK\Pim\Product;

use GuzzleHttp\Psr7\Uri;
use Psr\Http\Message\UriInterface;

final class ProductImage
{
    /** @var UriInterface */
    private $uri;

    /** @var string */
    private $base64;

    /** @var string */
    private $name;

    /** @var string */
    private $mimeType;

    /**
     * @internal
     */
    public function __construct(array $data)
    {
        $this->uri = new Uri($data['image_path']);
        $this->base64 = $data['image_data'];
        $this->name = $data['image_name'];
        $this->mimeType = $data['image_type'];
    }

    public function getUri(): UriInterface
    {
        return $this->uri;
    }

    public function getBase64(): string
    {
        return $this->base64;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getMimeType(): string
    {
        return $this->mimeType;
    }
}
