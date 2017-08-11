<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\Authentication;

final class BadCredentials extends \Exception
{
    public function __construct(\Throwable $e = null)
    {
        parent::__construct("Bad Credentials", 401, $e);
    }
}
