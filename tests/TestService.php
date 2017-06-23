<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\Tests;

use Wizaplace\ApiClientInjection;

/**
 * Dummy service, here to test generic features not tied to any specific service
 */
class TestService
{
    use ApiClientInjection;

    public function getTest()
    {
        return $this->client->get('/test');
    }
}
