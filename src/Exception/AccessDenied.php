<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\SDK\Exception;

use Throwable;

final class AccessDenied extends \Exception
{
    public function __construct($message = "Forbidden", $code = 403, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
