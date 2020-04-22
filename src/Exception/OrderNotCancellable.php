<?php

/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Exception;

final class OrderNotCancellable extends \Exception implements DomainError
{
    /** @var mixed[] */
    private $context;

    /** @param array $context */
    public function __construct(string $message, array $context = [], \Throwable $previous = null)
    {
        $this->context = $context;

        parent::__construct($message, static::getErrorCode()->getValue(), $previous);
    }

    /** @return mixed[] */
    final public function getContext(): array
    {
        return $this->context;
    }

    public static function getErrorCode(): ErrorCode
    {
        return ErrorCode::ORDER_NOT_CANCELLABLE();
    }
}
