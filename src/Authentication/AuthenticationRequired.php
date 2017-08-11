<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\Authentication;

use Throwable;
use Wizaplace\ApiClient;

/**
 * @see ApiClient::authenticate()
 */
final class AuthenticationRequired extends \Exception
{
    public function __construct(Throwable $previous = null)
    {
        parent::__construct("This action requires you to be authenticated.", 401, $previous);
    }
}
