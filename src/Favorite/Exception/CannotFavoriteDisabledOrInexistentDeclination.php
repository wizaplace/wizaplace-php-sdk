<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */

namespace Wizaplace\SDK\Favorite\Exception;

use Wizaplace\SDK\Catalog\DeclinationId;

/**
 * Class CannotFavoriteDisabledOrInexistentDeclination
 * @package Wizaplace\SDK\Favorite\Exception
 */
final class CannotFavoriteDisabledOrInexistentDeclination extends \Exception
{
    /**
     *
     */
    public const HTTP_ERROR_CODE = 400;

    /**
     * @internal
     *
     * @param DeclinationId   $declinationId
     * @param \Throwable|null $previous
     */
    public function __construct(DeclinationId $declinationId, \Throwable $previous = null)
    {
        parent::__construct(
            "Declination #{$declinationId} cannot be added to favorite as it is disabled or does not exist",
            self::HTTP_ERROR_CODE,
            $previous
        );
    }
}
