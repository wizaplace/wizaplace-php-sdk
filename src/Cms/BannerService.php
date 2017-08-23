<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\Cms;

use GuzzleHttp\Psr7\Uri;
use Wizaplace\AbstractService;

final class BannerService extends AbstractService
{
    /**
     * Get the banners that should display on the home page.
     *
     * @param string|null $device If provided, will only fetch banners for the given device.
     *
     * @return Banner[]
     */
    public function getHomepageBanners(?string $device = null): array
    {
        $results = (null !== $device) ? $this->client->get('cms/banners?device='.$device) : $this->client->get('cms/banners');

        return $this->buildBannersArray($results);
    }

    /**
     * Get the banners that should display on the given category.
     *
     * @param string|null $device If provided, will only fetch banners for the given device.
     *
     * @return Banner[]
     */
    public function getCategoriesBanners(int $categoryId, ?string $device = null): array
    {
        $results = (null !== $device) ? $this->client->get('cms/banners/category/'.$categoryId.'?device='.$device) : $this->client->get('cms/banners/category/'.$categoryId);

        return $this->buildBannersArray($results);
    }

    /**
     * @return Banner[]
     */
    private function buildBannersArray(array $results): array
    {
        $banners = [];
        foreach ($results as $result) {
            $banner = new Banner(new Uri((string) $result['link']), $result['shouldOpenInNewWindow'], $result['image']['id']);
            $banners[] = $banner;
        }

        return $banners;
    }
}
