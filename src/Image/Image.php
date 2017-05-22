<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */

namespace Wizaplace\Image;

class Image
{
    /** @var int */
    private $id;

    public function __construct(array $data)
    {
        $this->id = $data['id'];
    }

    public function getId(): int
    {
        return $this->id;
    }
}
