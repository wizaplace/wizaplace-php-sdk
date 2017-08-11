<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\Catalog;

final class Attribute
{
    /** @var int */
    private $id;
    /** @var string */
    private $name;
    /** @var AttributeType */
    private $type;
    /** @var int */
    private $position;
    /** @var null|int */
    private $parentId;

    /**
     * @internal
     */
    public function __construct(int $id, string $name, AttributeType $type, int $position, ?int $parentId)
    {
        $this->id = $id;
        $this->name = $name;
        $this->type = $type;
        $this->position = $position;
        $this->parentId = $parentId;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getType(): AttributeType
    {
        return $this->type;
    }

    public function getPosition()
    {
        return $this->position;
    }

    public function getParentId(): ?int
    {
        return $this->parentId;
    }
}
