<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\SDK\Exception;

use Throwable;

final class SomeParametersAreInvalid extends \Exception
{
    public function __construct($message = "Some parameters are invalid", $code = 400, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
