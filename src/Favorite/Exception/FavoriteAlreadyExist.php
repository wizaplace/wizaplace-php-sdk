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
    public function __construct(int $productId, \Throwable $previous = null)
    {
        parent::__construct("Product #{$productId} is already a favorite", self::HTTP_ERROR_CODE, $previous);
    }
}
