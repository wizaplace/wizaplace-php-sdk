<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\SDK\Tests;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\UploadedFileInterface;
use VCR\VCR;
use Wizaplace\SDK\ApiClient;
use function GuzzleHttp\Psr7\stream_for;

abstract class ApiTestCase extends TestCase
{
    /**
     * @var array[]
     */
    protected static $historyContainer = [];

    private $requestIndex = 0;

    private $cassettePath = null;
    private $cassetteName = null;

    public static function getApiBaseUrl(): string
    {
        return 'http://wizaplace.loc/api/v1/';
    }

    public function buildApiClient(): ApiClient
    {
        $historyMiddleware = Middleware::history(self::$historyContainer);

        $handlerStack = HandlerStack::create();
        $handlerStack->push($historyMiddleware);
        $i = &$this->requestIndex;
        $handlerStack->push(static function (callable $handler) use (&$i) {
            return static function (RequestInterface $request, array $options) use ($handler, &$i) {
                $request = $request->withHeader('VCR-index', $i);
                $i++;

                return $handler($request, $options);
            };
        });

        return new ApiClient(new Client([
            'handler' => $handlerStack,
            'base_uri' => self::getApiBaseUrl(),
        ]));
    }

    public function buildAdminApiClient(): ApiClient
    {
        $apiClient = $this->buildApiClient();
        $apiClient->authenticate('admin@wizaplace.com', 'password');

        return $apiClient;
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->requestIndex = 0;
        $reflectionClass = (new \ReflectionClass($this));

        $this->cassettePath = dirname($reflectionClass->getFileName());
        VCR::configure()->setCassettePath($this->cassettePath);

        $this->cassetteName = $reflectionClass->getShortName().DIRECTORY_SEPARATOR.$this->getName().'.yml';

        VCR::turnOn();
        VCR::insertCassette($this->cassetteName);
    }

    protected function runTest()
    {
        try {
            return parent::runTest();
        } catch (\LogicException $e) {
            if (strpos($e->getMessage(), 'request does not match a previously recorded request') !== false) {
                /* @see \VCR\Videorecorder::handleRequest */
                unlink($this->cassettePath.DIRECTORY_SEPARATOR.$this->cassetteName);
                throw new \Exception(
                    "VCR fixtures did not match the requests made during the tests.\nFixtures got deleted, re-run the test to re-populate them.",
                    $e->getCode(),
                    $e
                );
            }
            throw $e;
        }
    }

    protected function tearDown(): void
    {
        VCR::turnOff();
        $this->cassettePath = null;
        $this->cassetteName = null;
        self::$historyContainer = [];
        parent::tearDown();
    }

    protected function mockUploadedFile(string $filename): UploadedFileInterface
    {
        $path = __DIR__.'/fixtures/files/'.$filename;

        /** @var UploadedFileInterface|\PHPUnit_Framework_MockObject_MockObject $file */
        $file = $this->createMock(UploadedFileInterface::class);
        $file->expects($this->once())->method('getStream')->willReturn(stream_for(fopen($path, 'r')));
        $file->expects($this->once())->method('getClientFilename')->willReturn($filename);

        return $file;
    }
}
