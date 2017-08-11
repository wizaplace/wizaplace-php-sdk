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
use Wizaplace\ApiClient;
use Wizaplace\Exception\JsonDecodingError;

final class AbstractServiceTest extends TestCase
{
    public function testInvalidJsonProcessing()
    {
        // Create a Guzzle client which will send back just one mocked response with malformed JSON.
        $mock = new MockHandler([
            new Response(200, [], '{"malformedJSON":[]]}'),
        ]);
        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);

        // We want a proper exception, not a fatal error nor a silent fail.
        $this->expectException(JsonDecodingError::class);
        (new TestService(new ApiClient($client)))->getTest();
    }
}
