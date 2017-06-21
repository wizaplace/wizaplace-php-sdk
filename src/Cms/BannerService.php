<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\Cms;

use Wizaplace\AbstractService;

class BannerService extends AbstractService
{
    /**
     * @return Banner[]
     */
    public function getHomepageBanners(?string $device = null): array
    {

        $results = (null !== $device) ? $this->get('cms/banners?device='.$device) : $this->get('cms/banners');

        return $this->buildBannersArray($results);
    }

    /**
     * @return Banner[]
     */
    public function getCategoriesBanners(int $categoryId, ?string $device = null): array
    {
        $results = (null !== $device) ? $this->get('cms/banners/category/'.$categoryId.'?device='.$device) : $this->get('cms/banners/category/'.$categoryId);

        return $this->buildBannersArray($results);
    }

    /**
     * @return Banner[]
     */
    private function buildBannersArray(array $results): array
    {
        $banners = [];
        foreach ($results as $result) {
            $banner = new Banner($result['link'], $result['shouldOpenInNewWindow'], $result['image']['id']);
            $banners[] = $banner;
        }

        return $banners;
    }
}
