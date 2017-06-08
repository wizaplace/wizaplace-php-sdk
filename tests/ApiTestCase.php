<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\Tests;

use FR3D\SwaggerAssertions\PhpUnit\Psr7AssertsTrait;
use FR3D\SwaggerAssertions\SchemaManager;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Yaml\Yaml;
use VCR\VCR;

abstract class ApiTestCase extends TestCase
{
    use Psr7AssertsTrait;
    /**
     * @var SchemaManager
     */
    private static $schemaManager;

    /**
     * @var array[]
     */
    protected static $historyContainer = [];

    public static function getApiBaseUrl(): string
    {
        return 'http://wizaplace.loc/api/v1/';
    }

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        VCR::turnOn();
        VCR::insertCassette('Swagger-schema.yml');
        $schemaStr = file_get_contents(self::getApiBaseUrl().'doc/schema.yml');
        VCR::turnOff();
        $schemaStdObject = Yaml::parse($schemaStr, Yaml::PARSE_OBJECT | Yaml::PARSE_OBJECT_FOR_MAP);
        self::$schemaManager = new SchemaManager($schemaStdObject);
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
        try {
            foreach (self::$historyContainer as $transaction) {
                $this->assertResponseAndRequestMatch($transaction['response'], $transaction['request'], self::$schemaManager);
            }
        } finally {
            self::$historyContainer = [];
        }
        parent::tearDown();
    }
}
