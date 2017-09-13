<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\SDK\Authentication;

use Throwable;
use Wizaplace\SDK\ApiClient;

/**
 * @see ApiClient::authenticate()
 */
final class AuthenticationRequired extends \Exception
{
    /**
     * @internal
     */
    public function __construct(Throwable $previous = null)
    {
        parent::__construct("This action requires you to be authenticated.", 401, $previous);
    }
}
