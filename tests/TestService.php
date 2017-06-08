<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\Tests;

use Wizaplace\AbstractService;

class TestService extends AbstractService
{
    public function getTest()
    {
        return $this->get('/test');
    }
}
