<?php

/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Pim\Product;

/**
 * Class ProductAttachment
 * @package Wizaplace\SDK\Pim\Product
 */
class ProductAttachment
{
    /** @var string */
    private $id;

    /** @var string */
    private $label;

    /** @var null|string */
    private $originalUrl;

    /** @var null|string */
    private $publicUrl;

    /**
     * @internal
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->id = $data['id'];
        $this->label = $data['label'];
        $this->originalUrl = \array_key_exists('originalUrl', $data) === true ? $data['originalUrl'] : null;
        $this->publicUrl = \array_key_exists('publicUrl', $data) === true ? $data['publicUrl'] : null;
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
     * @return string|null
     */
    public function getPublicUrl(): ?string
    {
        return $this->publicUrl;
    }

    /**
     * @retrun string|null
     */
    public function getOriginalUrl(): ?string
    {
        return $this->originalUrl;
    }
}
