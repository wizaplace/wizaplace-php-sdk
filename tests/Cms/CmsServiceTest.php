<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\Tests\Cms;

use Wizaplace\Cms\CmsService;
use Wizaplace\Tests\ApiTestCase;

class CmsServiceTest extends ApiTestCase
{
    public function testGetAllMenus()
    {
        $cmsService = new CmsService($this->getGuzzleClient());
        $menus = $cmsService->getAllMenus();

        $this->assertNotEmpty($menus);

        $firstMenu = array_pop($menus);

        $this->assertNotEmpty($firstMenu->getItems());
    }
}
