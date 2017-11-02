<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types=1);

namespace Wizaplace\SDK\Catalog;

use Wizaplace\SDK\Exception\SomeParametersAreInvalid;

class DeclinationId
{
    /** @var string */
    private $declinationId;

    public function __construct(string $declinationId)
    {
        if ($declinationId === '') {
            throw new SomeParametersAreInvalid('Missing declination Id');
        }
        $this->declinationId = $declinationId;
    }

    public function __toString(): string
    {
        return $this->declinationId;
    }

    public function equals(DeclinationId $declinationId): bool
    {
        return $this->declinationId === $declinationId->declinationId;
    }
}
