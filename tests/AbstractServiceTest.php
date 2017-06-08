<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\Tests;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Wizaplace\Exception\JsonDecodingError;

class AbstractServiceTest extends TestCase
{
    public function testInvalidJsonProcessing()
    {
        $mock = new MockHandler([
            new Response(200, [], '{"malformedJSON":[]]}'),
        ]);

        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);

        $this->expectException(JsonDecodingError::class);
        (new TestService($client))->getTest();
    }
}
