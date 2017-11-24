<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\SDK\Cms;

use GuzzleHttp\Psr7\Uri;
use Wizaplace\SDK\AbstractService;
use function theodorejb\polycast\to_int;
use function theodorejb\polycast\to_string;

final class CmsService extends AbstractService
{
    /**
     * @return Menu[]
     */
    public function getAllMenus(): array
    {
        $results = $this->client->get('cms/menus');

        return array_map([$this, 'convertNestedArraysToMenu'], $results);
    }

    public function getPage(int $pageId) : Page
    {
        $data = $this->client->get('cms/page/'.$pageId);

        $page = new Page(
            $data['id'],
            $data['title'],
            $data['content'],
            $data['metaTitle'],
            $data['metaDescription'],
            $data['metaKeywords'],
            $data['slug']
        );

        return $page;
    }

    private function convertNestedArraysToMenu(array $menuData): Menu
    {
        $items = array_map([$this, 'convertArrayToMenuItem'], $menuData['items']);

        return new Menu(to_int($menuData['id']), to_string($menuData['name']), $items);
    }

    private function convertArrayToMenuItem(array $menuItem): MenuItem
    {
        return new MenuItem(to_string($menuItem['name']), to_int($menuItem['position']), new Uri(to_string($menuItem['url'])));
    }
}
