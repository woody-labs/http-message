<?php

namespace Woody\Http\Message;

use Psr\Http\Message\ResponseInterface;
use Swoole\Http\Response as SwooleResponse;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

/**
 * Class Response
 *
 * @package Woody\Http\Message
 */
class Response
{

    /**
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param \Swoole\Http\Response $swooleResponse
     */
    public static function send(ResponseInterface $response, SwooleResponse $swooleResponse): void
    {
        $swooleResponse->status($response->getStatusCode());
        $headers = $response->getHeaders();

        foreach ($headers as $name => $values) {
            foreach ($values as $value) {
                $swooleResponse->header($name, $value);
            }
        }

        // Rewind cursor.
        $response->getBody()->rewind();

        $swooleResponse->end($response->getBody()->getContents());
    }
}
