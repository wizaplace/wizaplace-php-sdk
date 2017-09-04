<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */

namespace Wizaplace\Favorite\Exception;

final class FavoriteAlreadyExist extends \Exception
{
    public const HTTP_ERROR_CODE = 409;

    /**
     * @internal
     */
    public function __construct(string $declinationId, \Throwable $previous = null)
    {
        parent::__construct("Declination #{$declinationId} is already a favorite", self::HTTP_ERROR_CODE, $previous);
    }
}
