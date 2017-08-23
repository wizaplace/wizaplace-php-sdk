<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\Authentication;

final class BadCredentials extends \Exception
{
    /**
     * @internal
     */
    public function __construct(\Throwable $e = null)
    {
        parent::__construct("Bad Credentials", 401, $e);
    }
}
