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

abstract class ApiTest extends TestCase
{
    use Psr7AssertsTrait;
    /**
     * @var SchemaManager
     */
    private static $schemaManager;

    /**
     * @var array[]
     */
    private static $historyContainer = [];

    public static function getApiBaseUrl(): string
    {
        return 'http://wizaplace.loc/api/v1/';
    }

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        VCR::configure()->setCassettePath(__DIR__.'/fixtures/VCR/');
        VCR::configure()->setMode(VCR::MODE_ONCE);
        VCR::configure()->enableLibraryHooks(['stream_wrapper', 'curl'])
            ->addRequestMatcher('headers_custom_matcher', function (\VCR\Request $first, \VCR\Request $second) {
                $headersBags = [$first->getHeaders(), $second->getHeaders()];

                foreach ($headersBags as &$headers) {
                    // Remove flaky headers that we don't care about
                    unset($headers['User-Agent']);
                }

                return $headersBags[0] == $headersBags[1];
            })
            ->enableRequestMatchers(array('method', 'url', 'query_string', 'body', 'post_fields', 'headers_custom_matcher'));

        VCR::turnOn();
        VCR::insertCassette('Swagger-schema');
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

    protected function tearDown(): void
    {
        foreach (self::$historyContainer as $transaction) {
            $this->assertResponseAndRequestMatch($transaction['response'], $transaction['request'], self::$schemaManager);
        }
        self::$historyContainer = [];
        parent::tearDown();
    }
}
