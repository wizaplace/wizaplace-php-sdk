<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types=1);

namespace Wizaplace\SDK\Catalog;

use Psr\Http\Message\UriInterface;

/**
 * Class ProductAttachment
 * @package Wizaplace\SDK\Catalog
 */
final class ProductAttachment
{
    /** @var string */
    private $id;

    /** @var string */
    private $label;

    /** @var UriInterface */
    private $url;

    /**
     * @internal
     *
     * @param array        $data
     * @param UriInterface $apiBaseUrl
     */
    public function __construct(array $data, UriInterface $apiBaseUrl)
    {
        $this->id = $data['id'];
        $this->label = $data['label'];
        $this->url = $apiBaseUrl->withPath($apiBaseUrl->getPath()."catalog/products/attachments/{$this->id}");
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * @return UriInterface
     */
    public function getUrl(): UriInterface
    {
        return $this->url;
    }
}
