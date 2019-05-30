<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\SDK\Basket;

use Wizaplace\SDK\Image\Image;
use function theodorejb\polycast\to_int;
use function theodorejb\polycast\to_string;

/**
 * Class Payment
 * @package Wizaplace\SDK\Basket
 */
final class Payment
{
    /** @var int */
    private $id;
    /** @var string */
    private $name;
    /** @var string */
    private $description;
    /** @var int */
    private $position;
    /** @var Image */
    private $image;
    /** @var PaymentType */
    private $type;

    /**
     * @internal
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->id = to_int($data['id']);
        $this->name = to_string($data['name']);
        $this->description = to_string($data['description']);
        $this->position = to_int($data['position']);
        if (isset($data['image'])) {
            $this->image = new Image($data['image']);
        }
        $this->type = new PaymentType($data['type']);
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @return int
     */
    public function getPosition(): int
    {
        return $this->position;
    }

    /**
     * @return Image|null
     */
    public function getImage(): ?Image
    {
        return $this->image;
    }

    /**
     * @return PaymentType
     */
    public function getType(): PaymentType
    {
        return $this->type;
    }
}
