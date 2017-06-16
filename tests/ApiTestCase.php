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
use VCR\VCR;

abstract class ApiTestCase extends TestCase
{
    /**
     * @var array[]
     */
    protected static $historyContainer = [];

    public static function getApiBaseUrl(): string
    {
        return 'http://wizaplace.loc/api/v1/';
    }

    public function getGuzzleClient(): Client
    {
        $historyMiddleware = Middleware::history(self::$historyContainer);

        $handlerStack = HandlerStack::create();
        $handlerStack->push($historyMiddleware);

        return new Client([
            'handler' => $handlerStack,
            'base_uri' => self::getApiBaseUrl(),
        ]);
    }

    protected function setUp(): void
    {
        parent::setUp();

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
