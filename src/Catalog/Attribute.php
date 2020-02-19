<?php

/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Catalog;

/**
 * Class Attribute
 * @package Wizaplace\SDK\Catalog
 */
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
     *
     * @param int           $id
     * @param string        $name
     * @param AttributeType $type
     * @param int           $position
     * @param int|null      $parentId
     */
    public function __construct(int $id, string $name, AttributeType $type, int $position, ?int $parentId)
    {
        $this->id = $id;
        $this->name = $name;
        $this->type = $type;
        $this->position = $position;
        $this->parentId = $parentId;
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
     * @return AttributeType
     */
    public function getType(): AttributeType
    {
        return $this->type;
    }

    /**
     * @return int
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @return int|null
     */
    public function getParentId(): ?int
    {
        return $this->parentId;
    }
}
