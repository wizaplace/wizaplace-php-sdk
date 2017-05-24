<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\CMS;

class SlugTarget
{
    /**
     * @var ObjectType
     */
    private $objectType;

    /**
     * @var int
     */
    private $objectId;

    public function __construct(ObjectType $objectType, int $objectId)
    {
        if ($objectType != ObjectType::REDIRECT() && $objectId < 1) {
            throw new \InvalidArgumentException("object ID has to be strictly positive, got $objectId");
        }
        $this->objectId = $objectId;

        $this->objectType = $objectType;
    }

    public function getObjectType(): ObjectType
    {
        return $this->objectType;
    }

    public function getObjectId(): int
    {
        return $this->objectId;
    }
}
