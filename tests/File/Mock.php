<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */

namespace Wizaplace\SDK\Tests\File;

use Psr\Http\Message\UploadedFileInterface;
use Wizaplace\SDK\Tests\ApiTestCase;
use function GuzzleHttp\Psr7\stream_for;

class Mock extends ApiTestCase
{
    public static function mockUploadedFile(string $filename): UploadedFileInterface
    {
        $path = __DIR__.'/../fixtures/files/'.$filename;

        /** @var UploadedFileInterface|\PHPUnit_Framework_MockObject_MockObject $file */
        $file = (new Mock())->createMock(UploadedFileInterface::class);
        $file->expects(parent::once())->method('getStream')->willReturn(stream_for(fopen($path, 'r')));
        $file->expects(parent::once())->method('getClientFilename')->willReturn($filename);

        return $file;
    }
}
