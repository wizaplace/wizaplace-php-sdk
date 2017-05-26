<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\Seo;

/**
 * SlugTarget indicates which object is referenced by a slug.
 */
class SlugTarget
{
    /**
     * @var SlugTargetType
     */
    private $objectType;

    /**
     * @var string
     */
    private $objectId;

    public function __construct(SlugTargetType $objectType, string $objectId)
    {
        $this->objectId = $objectId;

        $this->objectType = $objectType;
    }

    public function getObjectType(): SlugTargetType
    {
        return $this->objectType;
    }

    public function getObjectId(): string
    {
        return $this->objectId;
    }
}
