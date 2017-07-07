<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\Tests;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use VCR\VCR;
use Wizaplace\ApiClient;

abstract class ApiTestCase extends TestCase
{
    /**
     * @var array[]
     */
    protected static $historyContainer = [];

    private $requestIndex = 0;

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
        $handlerStack->push(function (callable $handler) use (&$i) {
            return function (RequestInterface $request, array $options) use ($handler, &$i) {
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

    protected function setUp(): void
    {
        parent::setUp();

        $this->requestIndex = 0;
        $this->cassetteName = (new \ReflectionClass($this))->getShortName().DIRECTORY_SEPARATOR.$this->getName().'.yml';
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
                unlink(__DIR__.'/fixtures/VCR/'.$this->cassetteName);
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
        $this->cassetteName = null;
        self::$historyContainer = [];
        parent::tearDown();
    }
}
