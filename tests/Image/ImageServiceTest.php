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
    private $id = 12;

    public function testGetImageLink()
    {
        $this->assertSame(
            "http://wizaplace.loc/images/detailed/0/samsung-galaxy_by7e-qk.png",
            (string) $this->buildImageService()->getImageLink($this->id)
        );
    }

    public function testGetImageLinkWithWidth()
    {
        $this->assertSame(
            "http://wizaplace.loc/images/thumbnails/b1c/970/456/233/c0d/c66/67d/407/63e/747/d1/42/detailed/0/samsung-galaxy_by7e-qk.png",
            (string) $this->buildImageService()->getImageLink($this->id, 42)
        );
    }

    public function testGetImageLinkWithWidthAndHeight()
    {
        $this->assertSame(
            "http://wizaplace.loc/images/thumbnails/b1c/970/456/233/c0d/c66/67d/407/63e/747/d1/42/36/detailed/0/samsung-galaxy_by7e-qk.png",
            (string) $this->buildImageService()->getImageLink($this->id, 42, 36)
        );
    }

    private function buildImageService(): ImageService
    {
        return new ImageService($this->buildApiClient());
    }
}
