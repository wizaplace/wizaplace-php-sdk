<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */

namespace Wizaplace\Cms;

use Wizaplace\AbstractService;

class BannerService extends AbstractService
{
    public function getHomepageBanners(string $device = null)
    {

        $results = (null !== $device) ? $this->get('cms/banners?device='.$device) : $this->get('cms/banners');

        return $this->buildBannersArray($results);
    }

    public function getCategoriesBanners(int $categoryId, string $device = null)
    {
        $results = (null !== $device) ? $this->get('cms/banners/category/'.$categoryId.'?device='.$device) : $this->get('cms/banners/category/'.$categoryId);

        return $this->buildBannersArray($results);
    }

    private function buildBannersArray(array $results)
    {
        $banners = [];
        foreach ($results as $result) {
            $banner = new Banner($result['link'], $result['shouldOpenInNewWindow'], $result['image']['id']);
            $banners[] = $banner;
        }

        return $banners;
    }
}
