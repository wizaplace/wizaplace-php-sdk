<?php

/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Exception;

use Throwable;

/**
 * Class UnauthorizedModerationAction
 * @package Wizaplace\SDK\Exception
 */
final class UnauthorizedModerationAction extends \Exception
{
    /**
     * @internal
     *
     * @param string          $message
     * @param \Throwable|null $previous
     */
    public function __construct(string $message = "", Throwable $previous = null)
    {
        parent::__construct($message, 401, $previous);
    }
}
