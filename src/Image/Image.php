<?php

/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Image;

/**
 * Class Image
 * @package Wizaplace\SDK\Image
 */
final class Image
{
    /** @var int */
    private $id;

    /** @var string */
    private $alt;

    /**
     * @internal
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->id = $data['id'];
        $this->alt = $data['alt'] ?? null;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /** @return string|null */
    public function getAlt(): ?string
    {
        return $this->alt;
    }
}
