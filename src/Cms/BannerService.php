<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\Cms;

use GuzzleHttp\Psr7\Uri;
use Wizaplace\ApiClientInjection;

class BannerService
{
    use ApiClientInjection;

    /**
     * @return Banner[]
     */
    public function getHomepageBanners(?string $device = null): array
    {

        $results = (null !== $device) ? $this->client->get('cms/banners?device='.$device) : $this->client->get('cms/banners');

        return $this->buildBannersArray($results);
    }

    /**
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
