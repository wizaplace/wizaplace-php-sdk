<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\Cms;

use GuzzleHttp\Psr7\Uri;
use Wizaplace\AbstractService;

class CmsService extends AbstractService
{
    /**
     * @return Menu[]
     */
    public function getAllMenus(): array
    {
        $results = $this->get('cms/menus');

        return array_map([$this, 'convertNestedArraysToMenu'], $results);
    }

    private function convertNestedArraysToMenu(array $menuData): Menu
    {
        $items = array_map([$this, 'convertArrayToMenuItem'], $menuData['items']);

        return new Menu((int) $menuData['id'], (string) $menuData['name'], $items);
    }

    private function convertArrayToMenuItem(array $menuItem): MenuItem
    {
        return new MenuItem((string) $menuItem['name'], (int) $menuItem['position'], new Uri((string) $menuItem['url']));
    }
}
