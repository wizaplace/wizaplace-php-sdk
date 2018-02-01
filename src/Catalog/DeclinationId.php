<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types=1);

namespace Wizaplace\SDK\Catalog;

class DeclinationId implements \JsonSerializable
{
    /** @var string */
    private $declinationId;

    /**
     * @throws \InvalidArgumentException
     */
    public function __construct(string $declinationId)
    {
        if ($declinationId === '') {
            throw new \InvalidArgumentException('Missing declination Id', 400);
        }
        $this->declinationId = $declinationId;
    }

    public function __toString(): string
    {
        return $this->declinationId;
    }

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
