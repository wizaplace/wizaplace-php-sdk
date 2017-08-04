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
        $cmsService = $this->buildCmsService();
        $menus = $cmsService->getAllMenus();

        $this->assertNotEmpty($menus);

        $firstMenu = array_pop($menus);

        $this->assertEquals(5, $firstMenu->getId());
        $this->assertEquals('Espace pro', $firstMenu->getName());

        $items = $firstMenu->getItems();
        $this->assertCount(3, $items);

        $firstItem = $items[0];
        $this->assertEquals('Annoncer sur la plateforme', $firstItem->getName());
        $this->assertEquals(0, $firstItem->getPosition());
        $this->assertEquals('https://wizaplace.loc/annoncer-sur-la-plateforme.html', $firstItem->getUrl()->__toString());
    }

    public function testGetPage()
    {
        $cmsService = $this->buildCmsService();
        $page = $cmsService->getPage(31);

        $this->assertNotEmpty($page);

        $this->assertEquals(31, $page->getId());
        $this->assertEquals('Test Cms Page Slug', $page->getTitle());
        $this->assertEquals('test-cms-page-slug', $page->getSlug());
    }

    private function buildCmsService(): CmsService
    {
        return new CmsService($this->buildApiClient());
    }
}
