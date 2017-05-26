<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\SEO;

class SlugTarget
{
    /**
     * @var SlugTargetType
     */
    private $objectType;

    /**
     * @var int
     */
    private $objectId;

    public function __construct(SlugTargetType $objectType, int $objectId)
    {
        if ($objectId < 1) {
            throw new \InvalidArgumentException("object ID has to be strictly positive, got $objectId");
        }
        $this->objectId = $objectId;

        $this->objectType = $objectType;
    }

    public function getObjectType(): SlugTargetType
    {
        return $this->objectType;
    }

    public function getObjectId(): int
    {
        return $this->objectId;
    }
}
