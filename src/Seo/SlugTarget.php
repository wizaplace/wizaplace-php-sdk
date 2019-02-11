<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\SDK\Seo;

/**
 * Class SlugTarget
 * @package Wizaplace\SDK\Seo
 *
 * SlugTarget indicates which object is referenced by a slug.
 */
final class SlugTarget
{
    /**
     * @var SlugTargetType
     */
    private $objectType;

    /**
     * @var string
     */
    private $objectId;

    /**
     * @internal
     *
     * @param SlugTargetType $objectType
     * @param string         $objectId
     */
    public function __construct(SlugTargetType $objectType, string $objectId)
    {
        $this->objectId = $objectId;

        $this->objectType = $objectType;
    }

    /**
     * @return SlugTargetType
     */
    public function getObjectType(): SlugTargetType
    {
        return $this->objectType;
    }

    /**
     * @return string
     */
    public function getObjectId(): string
    {
        return $this->objectId;
    }
}
