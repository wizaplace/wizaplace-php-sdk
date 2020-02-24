<?php

/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */

declare(strict_types=1);

namespace Wizaplace\SDK\User;

use Throwable;

/**
 * Class UserAlreadyExists
 * @package Wizaplace\SDK\User
 */
final class UserAlreadyExists extends \Exception
{
    /**
     * @internal
     *
     * @param Throwable|null $previous
     */
    public function __construct(Throwable $previous = null)
    {
        parent::__construct("User already exists", 409, $previous);
    }
}
