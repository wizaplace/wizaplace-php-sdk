<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\Image;

final class Image
{
    /** @var int */
    private $id;

    /**
     * @internal
     */
    public function __construct(array $data)
    {
        $this->id = $data['id'];
    }

    public function getId(): int
    {
        return $this->id;
    }
}
