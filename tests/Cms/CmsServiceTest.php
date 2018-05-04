<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\SDK\Tests\Cms;

use Wizaplace\SDK\Cms\CmsService;
use Wizaplace\SDK\Tests\ApiTestCase;

final class CmsServiceTest extends ApiTestCase
{
    public function testGetAllMenus()
    {
        $cmsService = $this->buildCmsService();
        $menus = $cmsService->getAllMenus();

        $this->assertNotEmpty($menus);
        $firstMenu = reset($menus);;

        $this->assertSame(1, $firstMenu->getId());
        $this->assertSame('Informations', $firstMenu->getName());

        $items = $firstMenu->getItems();
        $this->assertCount(3, $items);

        $firstItem = $items[0];
        $this->assertSame('Contact', $firstItem->getName());
        $this->assertSame(0, $firstItem->getPosition());
        $this->assertSame('https://wizaplace.loc/contact.html', $firstItem->getUrl()->__toString());
        $this->assertSame(1, count($firstItem->getChildren()));

        $firstChild = $firstItem->getChildren()[0];

        $this->assertSame('Qui sommes-nous ?', $firstChild->getName());
        $this->assertSame(0, $firstChild->getPosition());
        $this->assertSame('https://wizaplace.loc/about-us.html', $firstChild->getUrl()->__toString());
        $this->assertSame(0, count($firstChild->getChildren()));
    }

    public function testGetPage()
    {
        $cmsService = $this->buildCmsService();
        $page = $cmsService->getPage(9);

        $this->assertNotEmpty($page);

        $this->assertSame(9, $page->getId());
        $this->assertSame('Test Cms Page Slug', $page->getTitle());
        $this->assertSame('test-cms-page-slug', $page->getSlug());
        $this->assertSame('', $page->getContent());
        $this->assertSame('', $page->getMetaDescription());
        $this->assertSame('', $page->getMetaKeywords());
        $this->assertSame('', $page->getMetaTitle());
    }

    private function buildCmsService(): CmsService
    {
        return new CmsService($this->buildApiClient());
    }
}
