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

/**
 * Class CmsService
 * @package Wizaplace\SDK\Cms
 */
final class CmsService extends AbstractService
{
    /**
     * @return Menu[]
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Wizaplace\SDK\Exception\JsonDecodingError
     */
    public function getAllMenus(): array
    {
        $results = $this->client->get('cms/menus');

        return array_map([$this, 'convertNestedArraysToMenu'], $results);
    }

    /**
     * @param int $pageId
     *
     * @return Page
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Wizaplace\SDK\Exception\JsonDecodingError
     */
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

    /**
     * @param array $menuData
     *
     * @return Menu
     */
    private function convertNestedArraysToMenu(array $menuData): Menu
    {
        $items = array_map([$this, 'convertArrayToMenuItem'], $menuData['items']);

        return new Menu(to_int($menuData['id']), to_string($menuData['name']), $items);
    }

    /**
     * @param array $menuItem
     *
     * @return MenuItem
     */
    private function convertArrayToMenuItem(array $menuItem): MenuItem
    {
        return new MenuItem(
            to_string($menuItem['name']),
            to_int($menuItem['position']),
            new Uri(to_string($menuItem['url'])),
            (bool) $menuItem['targetBlank'],
            array_map([$this, 'convertArrayToMenuItem'], $menuItem['children'])
        );
    }
}
