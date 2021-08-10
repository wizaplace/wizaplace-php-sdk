<?php

/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Tests;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Wizaplace\SDK\PaginationHttpHeaders;

final class PaginationHttpHeadersTest extends TestCase
{
    public function dataProvider(): array
    {
        return [
            'no header' => [null, null, null],
            'valid headers' => [10, 1, 19],
        ];
    }

    /** @dataProvider dataProvider */
    public function testPaginationHttpHeaders(
        $limit = null,
        $offset = null,
        $total = null
    ): void {
        $headers = [];

        if (isset($limit)) {
            $headers = [
                PaginationHttpHeaders::LIMIT => $limit,
                PaginationHttpHeaders::OFFSET => $offset,
                PaginationHttpHeaders::TOTAL => $total,
            ];
        }

        $mock = new MockHandler([
            new Response(200, $headers, '')
        ]);

        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $response = $client->request('GET', '/');

        $paginationHttpHeaders = new PaginationHttpHeaders($response);

        $this->assertEquals($limit, $paginationHttpHeaders->getLimit());
        $this->assertEquals($offset, $paginationHttpHeaders->getOffset());
        $this->assertEquals($total, $paginationHttpHeaders->getTotal());
        $this->assertEquals(
            [
                PaginationHttpHeaders::LIMIT => $limit,
                PaginationHttpHeaders::OFFSET => $offset,
                PaginationHttpHeaders::TOTAL => $total,
            ],
            $paginationHttpHeaders->toArray()
        );
    }
}
