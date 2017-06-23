<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\Exception;

use Throwable;
use Wizaplace\ApiClient;
use Wizaplace\User\UserService;

/**
 * @see UserService::authenticate()
 * @see ApiClient::setApiKey()
 */
class AuthenticationRequired extends \Exception
{
    public function __construct(Throwable $previous = null)
    {
        parent::__construct("This action requires you to be authenticated.", 401, $previous);
    }
}
