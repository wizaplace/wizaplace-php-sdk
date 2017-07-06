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
use PHPUnit\Runner\BaseTestRunner;
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

    private $currentCassettePath;

    private $cassetteIsNew = false;

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
        $this->currentCassettePath = (new \ReflectionClass($this))->getShortName().DIRECTORY_SEPARATOR.$this->getName().'.yml';
        $this->cassetteIsNew = !file_exists(VCR::configure()->getCassettePath().'/'.$this->currentCassettePath);
        VCR::turnOn();
        VCR::insertCassette($this->currentCassettePath);
    }

    protected function tearDown(): void
    {
        VCR::turnOff();
        if ($this->cassetteIsNew && ($this->getStatus() !== BaseTestRunner::STATUS_PASSED)) {
            // We don't want to register a new K7 for a failing test
            unlink(VCR::configure()->getCassettePath().'/'.$this->currentCassettePath);
        }
        $this->currentCassettePath = null;
        $this->cassetteIsNew = false;
        self::$historyContainer = [];
        parent::tearDown();
    }
}
