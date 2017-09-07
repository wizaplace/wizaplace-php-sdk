<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\SDK\User;

use Throwable;

final class UserAlreadyExists extends \Exception
{
    /**
     * @internal
     */
    public function __construct(Throwable $previous = null)
    {
        parent::__construct("User already exists", 409, $previous);
    }
}
