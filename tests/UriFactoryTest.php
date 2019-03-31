<?php

namespace Woody\Http\Message\Tests;

use PHPUnit\Framework\TestCase;
use Woody\Http\Message\UriFactory;

/**
 * Class UriFactoryTest
 *
 * @package Woody\Http\Message\Tests
 */
class UriFactoryTest extends TestCase
{

    public function testLocalhost()
    {
        $headers = [
            'host' => 'localhost:9501',
            'connection' => 'keep-alive',
            'cache-control' => 'max-age=0',
            'upgrade-insecure-requests' => '1',
            'user-agent' => 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/73.0.3683.86 Safari/537.36',
            'accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3',
            'accept-encoding' => 'gzip, deflate, br',
            'accept-language' => 'fr-FR,fr;q=0.9,en-US;q=0.8,en;q=0.7',
        ];
        $server = [
            'query_string' => 'foo=bar',
            'request_method' => 'GET',
            'request_uri' => '/path',
            'path_info' => '/path',
            'request_time' => 1554046325,
            'request_time_float' => 1554046326.805243,
            'server_port' => 9501,
            'remote_port' => 48082,
            'remote_addr' => '127.0.0.1',
            'master_time' => 1554046325,
            'server_protocol' => 'HTTP/1.1',
        ];

        $request = $this->createMock(\Swoole\Http\Request::class);
        $request->method('rawContent')->willReturn(false);
        $request->header = $headers;
        $request->server = $server;

        $uriFactory = new UriFactory();
        $uri = $uriFactory->create($request);

        $this->assertEquals('http://localhost:9501/path?foo=bar', (string) $uri);
    }
}