<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types=1);

namespace Wizaplace\SDK\Cms;

abstract class AbstractBannerServiceDecorator implements BannerServiceInterface
{
    /** @var BannerServiceInterface */
    private $decorated;

    public function __construct(BannerServiceInterface $decorated)
    {
        $this->decorated = $decorated;
    }

    /**
     * @inheritdoc
     */
    public function getHomepageBanners(?string $device = null): array
    {
        return $this->decorated->getHomepageBanners($device);
    }

    /**
     * @inheritdoc
     */
    public function getCategoriesBanners(int $categoryId, ?string $device = null): array
    {
        return $this->decorated->getCategoriesBanners($categoryId, $device);
    }
}
