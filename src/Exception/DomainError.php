<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types=1);

namespace Wizaplace\SDK\Exception;

/**
 * @internal
 */
interface DomainError
{
    public function __construct(string $message, array $context = [], ?\Throwable $previous = null);

    public static function getErrorCode(): ErrorCode;

    public function getContext(): array;
}
