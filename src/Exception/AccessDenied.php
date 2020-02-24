<?php

/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Exception;

use Throwable;

/**
 * Class AccessDenied
 * @package Wizaplace\SDK\Exception
 */
final class AccessDenied extends \Exception
{
    /**
     * AccessDenied constructor.
     *
     * @param string         $message
     * @param int            $code
     * @param Throwable|NULL $previous
     */
    public function __construct($message = "Forbidden", $code = 403, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
