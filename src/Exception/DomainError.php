<?php

/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Exception;

/**
 * Class DomainError
 * @package Wizaplace\SDK\Exception
 *
 * @internal
 */
interface DomainError
{
    /**
     * DomainError constructor.
     *
     * @param string          $message
     * @param array           $context
     * @param \Throwable|null $previous
     */
    public function __construct(string $message, array $context = [], ?\Throwable $previous = null);

    /**
     * @return ErrorCode
     */
    public static function getErrorCode(): ErrorCode;

    /**
     * @return array
     */
    public function getContext(): array;
}
