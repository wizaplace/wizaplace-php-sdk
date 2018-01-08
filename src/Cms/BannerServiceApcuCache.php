<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types=1);

namespace Wizaplace\SDK\Cms;

final class BannerServiceApcuCache extends AbstractBannerServiceDecorator
{
    private const HOMEPAGE_BANNERS_CACHE_KEY_PREFIX = 'wizaplace_banners_homepage_';
    private const CATEGORIES_BANNERS_CACHE_KEY_PREFIX = 'wizaplace_banners_categories_';

    /** @var int */
    private $ttl;

    public function __construct(BannerServiceInterface $decorated, int $ttl = 120)
    {
        parent::__construct($decorated);
        $this->ttl = $ttl;

        if (!function_exists('apcu_cache_info')) {
            throw new \Exception('apcu is not installed');
        }
        if (apcu_cache_info(true) === false) {
            throw new \Exception('apcu is not enabled');
        }
    }


    public function getHomepageBanners(?string $device = null): array
    {
        $cacheKey = self::HOMEPAGE_BANNERS_CACHE_KEY_PREFIX.($device ?? '');

        return apcu_entry($cacheKey, function () use ($device): array {
            return parent::getHomepageBanners($device);
        }, $this->ttl);
    }

    public function getCategoriesBanners(int $categoryId, ?string $device = null): array
    {
        $cacheKey = self::CATEGORIES_BANNERS_CACHE_KEY_PREFIX.((string) $categoryId).'_'.($device ?? '');

        return apcu_entry($cacheKey, function () use ($categoryId, $device): array {
            return parent::getCategoriesBanners($categoryId, $device);
        }, $this->ttl);
    }
}
