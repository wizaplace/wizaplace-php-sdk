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

    public function getImage(): ?Image
    {
        return $this->image;
    }

    public function getType(): PaymentType
    {
        return $this->type;
    }
}
