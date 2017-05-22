<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */

namespace Wizaplace\Basket;

use Wizaplace\Image\Image;

class Payment
{
    /** @var  int */
    private $id;
    /** @var  string */
    private $name;
    /** @var  string */
    private $description;
    /** @var  int */
    private $position;
    /** @var  Image */
    private $image;


    public function __construct(array $data)
    {
        $this->id = $data['id'];
        $this->name = $data['name'];
        $this->description = $data['description'];
        $this->position = $data['position'];
        $this->image = $data['image'];
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function getImage(): Image
    {
        return $this->image;
    }
}
