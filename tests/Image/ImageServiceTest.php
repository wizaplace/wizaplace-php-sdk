<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\Tests\Image;

use Wizaplace\Image\ImageService;
use Wizaplace\Tests\ApiTestCase;

class ImageServiceTest extends ApiTestCase
{
    public function testGetImageLink()
    {
        $this->assertSame('http://wizaplace.loc/api/v1/image/1?', $this->buildImageService()->getImageLink(1));
    }

    public function testGetImageLinkWithWidth()
    {
        $this->assertSame('http://wizaplace.loc/api/v1/image/1?w=42', $this->buildImageService()->getImageLink(1, 42));
    }

    public function testGetImageLinkWithWidthAndHeight()
    {
        $this->assertSame('http://wizaplace.loc/api/v1/image/1?w=42&h=36', $this->buildImageService()->getImageLink(1, 42, 36));
    }

    private function buildImageService(): ImageService
    {
        return new ImageService($this->buildApiClient());
    }
}
