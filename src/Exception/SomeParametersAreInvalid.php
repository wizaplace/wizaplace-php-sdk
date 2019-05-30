<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\SDK\Exception;

use Throwable;

/**
 * Class SomeParametersAreInvalid
 * @package Wizaplace\SDK\Exception
 */
final class SomeParametersAreInvalid extends \Exception
{
    /**
     * SomeParametersAreInvalid constructor.
     *
     * @param string         $message
     * @param int            $code
     * @param Throwable|NULL $previous
     */
    public function __construct($message = "Some parameters are invalid", $code = 400, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
