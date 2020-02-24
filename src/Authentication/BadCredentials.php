<?php

/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Authentication;

/**
 * Class BadCredentials
 * @package Wizaplace\SDK\Authentication
 */
final class BadCredentials extends \Exception
{
    /**
     * @internal
     *
     * @param \Throwable|null $e
     */
    public function __construct(\Throwable $e = null)
    {
        parent::__construct("Bad Credentials", 401, $e);
    }
}
