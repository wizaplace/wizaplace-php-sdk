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
    public function getHomepageBanners(string $device = '')
    {
        $results = $this->get('cms/banners', [$device]);

        return $results;
    }

    public function getCategoriesBanners(int $categoryId, string $device)
    {
        $results = $this->get('cms/banners/category/'.$categoryId, [$device]);

        return $results;
    }
}
