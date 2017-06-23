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
use Wizaplace\ServicesFactory;

abstract class ApiTestCase extends TestCase
{
    /**
     * @var array[]
     */
    protected static $historyContainer = [];

    private $requestIndex = 0;

    public static function getApiBaseUrl(): string
    {
        return 'http://wizaplace.loc/api/v1/';
    }

    public function buildServicesFactory(): ServicesFactory
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

        $servicesFactory = ServicesFactory::fromGuzzleClient(new Client([
            'handler' => $handlerStack,
            'base_uri' => self::getApiBaseUrl(),
        ]));

        return $servicesFactory;
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->requestIndex = 0;
        VCR::turnOn();
        $cassette = (new \ReflectionClass($this))->getShortName().DIRECTORY_SEPARATOR.$this->getName().'.yml';
        VCR::insertCassette($cassette);
    }

    protected function tearDown(): void
    {
        VCR::turnOff();
        self::$historyContainer = [];
        parent::tearDown();
    }
}
