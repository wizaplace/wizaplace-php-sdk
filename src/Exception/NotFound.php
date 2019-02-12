<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\SDK\Exception;

use Throwable;

/**
 * Class NotFound
 * @package Wizaplace\SDK\Exception
 */
class NotFound extends \Exception
{
    /**
     * @internal
     *
     * @param string         $message
     * @param Throwable|null $previous
     */
    public function __construct($message = "", Throwable $previous = null)
    {
        parent::__construct($message, 404, $previous);
    }
}
