<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */

namespace Wizaplace\Favorite\Exception;

final class CannotFavoriteDisabledOrInexistentDeclination extends \Exception
{
    public const HTTP_ERROR_CODE = 400;

    public function __construct(int $productId, \Throwable $previous = null)
    {
        parent::__construct(
            "Product #{$productId} cannot be added to favorite as it is disabled or does not exist",
            self::HTTP_ERROR_CODE,
            $previous
        );
    }
}
