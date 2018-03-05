<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */

namespace Wizaplace\SDK\Favorite\Exception;

use Wizaplace\SDK\Exception\DomainError;
use Wizaplace\SDK\Exception\ErrorCode;

final class FavoriteAlreadyExist extends \Exception implements DomainError
{
    /**
     * @deprecated use self::getErrorCode instead
     */
    public const HTTP_ERROR_CODE = 409;

    /**
     * @var array
     */
    private $context;

    /**
     * @internal
     */
    public function __construct(string $message, array $context = [], ?\Throwable $previous = null)
    {
        $this->context = $context;

        parent::__construct($message, static::getErrorCode()->getValue(), $previous);
    }


    final public function getContext(): array
    {
        return $this->context;
    }

    public static function getErrorCode(): ErrorCode
    {
        return ErrorCode::FAVORITE_ALREADY_EXISTS();
    }
}
