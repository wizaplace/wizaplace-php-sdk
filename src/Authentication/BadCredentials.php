<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\Authentication;

class BadCredentials extends \Exception
{
    public function __construct(\Throwable $e = null)
    {
        parent::__construct("", 401, $e);
    }
}
