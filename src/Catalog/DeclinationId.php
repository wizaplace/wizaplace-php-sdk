<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types=1);

namespace Wizaplace\SDK\Catalog;

/**
 * Class DeclinationId
 * @package Wizaplace\SDK\Catalog
 */
class DeclinationId implements \JsonSerializable
{
    /** @var string */
    private $declinationId;

    /**
     * @param string $declinationId
     */
    public function __construct(string $declinationId)
    {
        if ($declinationId === '') {
            throw new \InvalidArgumentException('Missing declination Id', 400);
        }
        $this->declinationId = $declinationId;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->declinationId;
    }

    /**
     * @param self $declinationId
     *
     * @return bool
     */
    public function equals(self $declinationId): bool
    {
        return $this->declinationId === $declinationId->declinationId;
    }

    /**
     * @inheritdoc
     */
    public function jsonSerialize(): string
    {
        return $this->__toString();
    }
}
