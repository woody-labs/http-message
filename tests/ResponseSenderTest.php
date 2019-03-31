<?php

namespace Woody\Http\Message\Tests;

use PHPUnit\Framework\TestCase;
use Woody\Http\Message\ResponseSender;
use Zend\Diactoros\Response\HtmlResponse;

/**
 * Class ResponseSenderTest
 *
 * @package Woody\Http\Message\Tests
 */
class ResponseSenderTest extends TestCase
{

    public function testSend()
    {
        $response = new HtmlResponse(
            'Hello World',
            200,
            [
                'Content-Type' => 'text/plain',
                'Set-Cookie' => 'name=value; Expires=Wed, 21 Oct 2025 07:28:00 GMT; path=/; domain=localhost; HttpOnly; SameSite=Lax',
            ]
        );

        $headers = [
            ['Content-Type', 'text/plain', null],
        ];
        $cookies = [
            ['name', 'value', 1761118080, '/', 'localhost', null, true],
        ];

        $swooleResponse = $this->createMock(\Swoole\Http\Response::class);
        $swooleResponse->method('status')->willReturn($this->returnArgument(0))->with(200);
        $swooleResponse->method('header')->willReturn($this->returnArgument(0))->withConsecutive(...$headers);
        $swooleResponse->method('cookie')->willReturn($this->returnArgument(0))->withConsecutive(...$cookies);
        $swooleResponse->method('end')->willReturn($this->returnArgument(0))->with('Hello World');

        $responseSender = new ResponseSender();
        $responseSender->send($response, $swooleResponse);

        $this->assertNotEmpty($swooleResponse);
    }
}
