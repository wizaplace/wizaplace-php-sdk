<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */

namespace Wizaplace\Favorite\Exception;

class FavoriteAlreadyExist extends \Exception
{
    public const HTTP_ERROR_CODE = 400;

    public function __construct(int $productId, \Throwable $previous = null)
    {
        parent::__construct("Product #{$productId} is already a favorite", self::HTTP_ERROR_CODE, $previous);
    }

}
