<?php

namespace Woody\Http\Message\Tests;

use PHPUnit\Framework\TestCase;
use Woody\Http\Message\StreamFactory;

/**
 * Class StreamFactoryTest
 *
 * @package Woody\Http\Message\Tests
 */
class StreamFactoryTest extends TestCase
{

    public function testHtml()
    {
        $content = '<html><body>Hello World</body></html>';
        $request = $this->createMock(\Swoole\Http\Request::class);
        $request->method('rawContent')->willReturn($content);

        $streamFactory = new StreamFactory();
        $stream = $streamFactory->create($request);

        $this->assertEquals($content, $stream->getContents());
    }

    public function testEmpty()
    {
        $request = $this->createMock(\Swoole\Http\Request::class);
        $request->method('rawContent')->willReturn(false);

        $streamFactory = new StreamFactory();
        $stream = $streamFactory->create($request);

        // False is converted into empty string.
        $this->assertEmpty($stream->getContents());
    }
}
