<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\Tests\Cms;

use VCR\VCR;
use Wizaplace\Cms\CmsService;
use Wizaplace\Tests\ApiTest;

class CmsServiceTest extends ApiTest
{
    public function testGetAllMenus()
    {
        try {
            VCR::turnOn();
            VCR::insertCassette($this->getName());

            $cmsService = new CmsService($this->getGuzzleClient());
            $menus = $cmsService->getAllMenus();

            $this->assertNotEmpty($menus);

            $firstMenu = array_pop($menus);

            $this->assertNotEmpty($firstMenu->getItems());
        } finally {
            VCR::turnOff();
        }
    }
}
