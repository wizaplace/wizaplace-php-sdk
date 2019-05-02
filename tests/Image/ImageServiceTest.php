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
            "http://wizaplace.loc/images/detailed/0/samsung-galaxy_dohh-2a.png",
            (string) $this->buildImageService()->getImageLink($this->id)
        );
    }

    public function testGetImageLinkWithWidth()
    {
        $this->assertSame(
            "http://wizaplace.loc/images/thumbnails/ea0/e1e/d50/ef3/ad3/beb/158/a79/f1b/569/5c/42/detailed/0/samsung-galaxy_dohh-2a.png",
            (string) $this->buildImageService()->getImageLink($this->id, 42)
        );
    }

    public function testGetImageLinkWithWidthAndHeight()
    {
        $this->assertSame(
            "http://wizaplace.loc/images/thumbnails/ea0/e1e/d50/ef3/ad3/beb/158/a79/f1b/569/5c/42/36/detailed/0/samsung-galaxy_dohh-2a.png",
            (string) $this->buildImageService()->getImageLink($this->id, 42, 36)
        );
    }

    private function buildImageService(): ImageService
    {
        return new ImageService($this->buildApiClient());
    }
}
