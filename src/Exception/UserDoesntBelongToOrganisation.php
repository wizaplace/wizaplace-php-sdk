<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Exception;


final class UserDoesntBelongToOrganisation extends \Exception implements DomainError
{
    /**
     * @var array
     */
    private $context;

    /**
     * UserDoesntBelongToOrganisation constructor.
     * @param string $message
     * @param array $context
     * @param null|\Throwable $previous
     * @internal
     */
    public function __construct(string $message, array $context = [], ?\Throwable $previous = null)
    {
        $this->context = $context;

        parent::__construct($message, $previous);
    }

    final public function getContext(): array
    {
        return $this->context;
    }

    public static function getErrorCode(): ErrorCode
    {
        return ErrorCode::USER_DOESNT_BELONG_TO_ORGANISATION();
    }
}
