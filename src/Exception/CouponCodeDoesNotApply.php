<?php

/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Exception;

/**
 * Class CouponCodeDoesNotApply
 * @package Wizaplace\SDK\Exception
 */
final class CouponCodeDoesNotApply extends NotFound implements DomainError // not really a NotFound, but we have to keep it for backward-compatibility
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

        parent::__construct($message, $previous);
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
        return ErrorCode::COUPON_CODE_DOES_NOT_APPLY();
    }
}
