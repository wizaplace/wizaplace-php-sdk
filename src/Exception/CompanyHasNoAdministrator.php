<?php

/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Exception;

/**
 * Class CompanyHasNoAdministrator
 * @package Wizaplace\SDK\Exception
 */
final class CompanyHasNoAdministrator extends \Exception implements DomainError
{
    /**
     * @var array
     */
    private $context;

    /**
     * @internal
     *
     * @param string          $message
     * @param array           $context
     * @param \Throwable|null $previous
     */
    public function __construct(string $message, array $context = [], ?\Throwable $previous = null)
    {
        $this->context = $context;

        parent::__construct($message, static::getErrorCode()->getValue(), $previous);
    }

    /**
     * @return array
     */
    final public function getContext(): array
    {
        return $this->context;
    }

    /**
     * @return ErrorCode
     */
    public static function getErrorCode(): ErrorCode
    {
        return ErrorCode::COMPANY_HAS_NO_ADMINISTRATOR();
    }
}
