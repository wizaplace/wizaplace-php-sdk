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

    /** @var string|null */
    private $altText;

    /**
     * @internal
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->id = $data['id'];
        $this->altText = \array_key_exists('altText', $data) === true
            && $data['altText'] !== null
            ? $data['altText'] : '';
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getAltText(): ?string
    {
        return $this->altText;
    }
}
