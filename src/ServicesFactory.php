<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace;

use GuzzleHttp\Client;
use Wizaplace\Basket\BasketService;
use Wizaplace\Catalog\CatalogService;
use Wizaplace\Cms\BannerService;
use Wizaplace\Cms\CmsService;
use Wizaplace\Image\ImageService;
use Wizaplace\Order\OrderService;
use Wizaplace\Seo\SeoService;
use Wizaplace\User\ApiKey;
use Wizaplace\User\UserService;

class ServicesFactory
{
    /** @var ApiClient */
    private $apiClient;

    public function __construct(ApiClient $apiClient)
    {
        $this->apiClient = $apiClient;
    }

    public static function fromGuzzleClient(Client $guzzleClient): self
    {
        return new self(new ApiClient($guzzleClient));
    }

    public static function fromApiBaseUrl(string $apiBaseUrl): self
    {
        $apiBaseUrl = rtrim($apiBaseUrl, '/').'/';
        $guzzleClient = new Client([
            'base_uri' => $apiBaseUrl,
        ]);

        return self::fromGuzzleClient($guzzleClient);
    }

    public function setApiKey(?ApiKey $apiKey = null): void
    {
        $this->apiClient->setApiKey($apiKey);
    }

    public function basketService(): BasketService
    {
        return new BasketService($this->apiClient);
    }

    public function catalogService(): CatalogService
    {
        return new CatalogService($this->apiClient);
    }

    public function bannerService(): BannerService
    {
        return new BannerService($this->apiClient);
    }

    public function cmsService(): CmsService
    {
        return new CmsService($this->apiClient);
    }

    public function imageService(): ImageService
    {
        return new ImageService($this->apiClient);
    }

    public function orderService(): OrderService
    {
        return new OrderService($this->apiClient);
    }

    public function seoService(): SeoService
    {
        return new SeoService($this->apiClient);
    }

    public function userService(): UserService
    {
        return new UserService($this->apiClient);
    }
}
