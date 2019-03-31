<?php

namespace Woody\Http\Message;

use Psr\Http\Message\StreamInterface;
use Swoole\Http\Request;
use Zend\Diactoros\Stream;

/**
 * Class StreamFactory
 *
 * @package Woody\Http\Message
 */
class StreamFactory
{

    /**
     * @param \Swoole\Http\Request $request
     *
     * @return \Psr\Http\Message\StreamInterface
     */
    public function create(Request $request): StreamInterface
    {
        $stream = new Stream('php://temp', 'wb+');

        if ($content = $request->rawcontent()) {
            $stream->write($content);
            $stream->rewind();
        }

        return $stream;
    }
}
