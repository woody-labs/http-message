<?php

namespace Woody\Http\Message;

use GuzzleHttp\Cookie\SetCookie;
use Psr\Http\Message\ResponseInterface;
use Swoole\Http\Response as SwooleResponse;

/**
 * Class Response
 *
 * @package Woody\Http\Message
 */
class Response extends \GuzzleHttp\Psr7\Response
{

    /**
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param \Swoole\Http\Response $swooleResponse
     */
    public static function send(ResponseInterface $response, SwooleResponse $swooleResponse): void
    {
        $swooleResponse->status($response->getStatusCode());
        $cookies = $response->getHeader('set-cookie');
        $headers = $response->withoutHeader('set-cookie')->getHeaders();

        foreach ($headers as $name => $values) {
            foreach ($values as $value) {
                $swooleResponse->header($name, $value);
            }
        }

        foreach ($cookies as $value) {
            $cookie = SetCookie::fromString($value);
            $swooleResponse->rawcookie(
                $cookie->getName(),
                $cookie->getValue(),
                $cookie->getExpires(),
                $cookie->getPath(),
                $cookie->getDomain(),
                $cookie->getSecure(),
                $cookie->getHttpOnly()
            );
        }

        // Rewind cursor.
        $response->getBody()->rewind();

        $swooleResponse->end($response->getBody()->getContents());
    }
}
