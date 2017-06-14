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

    public function testGetPage()
    {
        $cmsService = new CmsService($this->getGuzzleClient());
        $page = $cmsService->getPage(4);

        $this->assertNotEmpty($page);

        $this->assertEquals(
            [
                'id' => 4,
                'name' => 'Contact',
                'content'   => '',
                'meta_keywords' => '',
                'meta_description'  =>  '',
                'seo_title' => '',
                'slug'  => 'contact',
            ],
            $page
        );
    }
}
