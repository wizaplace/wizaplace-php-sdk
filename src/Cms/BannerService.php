<?php

/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Cms;

use GuzzleHttp\Psr7\Uri;
use Wizaplace\SDK\AbstractService;

use function theodorejb\polycast\to_string;

/**
 * Class BannerService
 * @package Wizaplace\SDK\Cms
 */
class BannerService extends AbstractService
{
    /**
     * Get the banners that should display on the home page.
     *
     * @param string|null $device If provided, will only fetch banners for the given device.
     *
     * @return Banner[]
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Wizaplace\SDK\Exception\JsonDecodingError
     */
    public function getHomepageBanners(?string $device = null): array
    {
        $results = (null !== $device) ? $this->client->get('cms/banners?device=' . $device) : $this->client->get('cms/banners');

        return $this->buildBannersArray($results);
    }

    /**
     * Get the banners that should display on the given category.
     *
     * @param int         $categoryId
     * @param string|null $device If provided, will only fetch banners for the given device.
     *
     * @return Banner[]
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Wizaplace\SDK\Exception\JsonDecodingError
     */
    public function getCategoriesBanners(int $categoryId, ?string $device = null): array
    {
        $results = (null !== $device) ? $this->client->get('cms/banners/category/' . $categoryId . '?device=' . $device) : $this->client->get('cms/banners/category/' . $categoryId);

        return $this->buildBannersArray($results);
    }

    /**
     * @return Banner[]
     */
    private function buildBannersArray(array $results): array
    {
        $banners = [];
        foreach ($results as $result) {
            $banner = new Banner(new Uri(to_string($result['link'])), $result['shouldOpenInNewWindow'], $result['image']['id'], $result['name'], $result['image']['alt']);
            $banners[] = $banner;
        }

        return $banners;
    }
}
