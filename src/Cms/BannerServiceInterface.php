<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types=1);

namespace Wizaplace\SDK\Cms;

interface BannerServiceInterface
{
    /**
     * Get the banners that should display on the home page.
     *
     * @param string|null $device If provided, will only fetch banners for the given device.
     *
     * @return Banner[]
     */
    public function getHomepageBanners(?string $device = null): array;

    /**
     * Get the banners that should display on the given category.
     *
     * @param string|null $device If provided, will only fetch banners for the given device.
     *
     * @return Banner[]
     */
    public function getCategoriesBanners(int $categoryId, ?string $device = null): array;
}
