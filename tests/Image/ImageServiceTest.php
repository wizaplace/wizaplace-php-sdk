<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\SDK\Tests\Image;

use Wizaplace\SDK\Image\ImageService;
use Wizaplace\SDK\Tests\ApiTestCase;

final class ImageServiceTest extends ApiTestCase
{
    public function testGetImageLink()
    {
        $this->assertSame($this->getApiBaseUrl().'image/1', (string) $this->buildImageService()->getImageLink(1));
    }

    public function testGetImageLinkWithWidth()
    {
        $this->assertSame($this->getApiBaseUrl().'image/1?w=42', (string) $this->buildImageService()->getImageLink(1, 42));
    }

    public function testGetImageLinkWithWidthAndHeight()
    {
        $this->assertSame($this->getApiBaseUrl().'image/1?w=42&h=36', (string) $this->buildImageService()->getImageLink(1, 42, 36));
    }

    private function buildImageService(): ImageService
    {
        return new ImageService($this->buildApiClient());
    }
}
