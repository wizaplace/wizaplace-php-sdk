<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types=1);

namespace Wizaplace\SDK\Tests\Image;

use PHPUnit\Framework\TestCase;
use Wizaplace\SDK\Exception\NotFound;
use Wizaplace\SDK\Image\Image;

final class ImageTest extends TestCase
{
    public function testImageMethods(): void
    {
        $data = [
            'id' => 1,
            'urls' => [
                'original' => '//wathev.er/path/to/an/image.png',
                'large' => '//wathev.er/path/to/an/image.png',
                'medium' => '//wathev.er/path/to/an/image.png',
                'small' => '//wathev.er/path/to/an/image.png',
            ],
        ];
        $image = new Image($data);

        $this->assertEquals($data['id'], $image->getId());
        $this->assertEquals($data['urls'], $image->getUrls());
        $this->assertEquals($data['urls']['original'], $image->getUrl('original'));

        $this->expectException(NotFound::class);
        $image->getUrl('unknow');
    }
}
