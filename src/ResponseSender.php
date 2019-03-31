<?php

namespace Woody\Http\Message;

use Psr\Http\Message\ResponseInterface;
use Swoole\Http\Response as SwooleResponse;
use Woody\Http\Message\CookieFactory;

/**
 * Class Response
 *
 * @package Woody\Http\Message
 */
class ResponseSender
{

    /**
     * @var \Woody\Http\Message\CookieFactory
     */
    protected $cookieFactory;

    /**
     * ResponseSender constructor.
     *
     * @param \Woody\Http\Message\CookieFactory|null $cookieFactory
     */
    public function __construct(CookieFactory $cookieFactory = null)
    {
        $this->cookieFactory = $cookieFactory ?? new CookieFactory();
    }

    /**
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param \Swoole\Http\Response $swooleResponse
     *
     * @throws \Exception
     */
    public function send(ResponseInterface $response, SwooleResponse $swooleResponse): void
    {
        // Propagate status code.
        $swooleResponse->status($response->getStatusCode());

        // Extract set-cookie headers.
        $cookies = $response->getHeader('set-cookie');

        // Remove set-cookie from headers.
        $headers = $response->withoutHeader('set-cookie')->getHeaders();

        // Propagate headers.
        foreach ($headers as $name => $values) {
            foreach ($values as $value) {
                $swooleResponse->header($name, $value);
            }
        }

        // Propagate cookies.
        foreach ($cookies as $header) {
            // Parse cookie headers.
            $cookie = $this->cookieFactory->create($header);

            foreach ($cookie->getValues() as $name => $value) {
                $swooleResponse->cookie(
                    $name,
                    $value,
                    $cookie->getExpires() ?? null,
                    $cookie->getPath() ?? null,
                    $cookie->getDomain() ?? null,
                    $cookie->getSecure() ?? null,
                    $cookie->getHttpOnly() ?? null
                );
            }
        }

        // Rewind cursor.
        $response->getBody()->rewind();

        // Send content.
        $swooleResponse->end($response->getBody()->getContents());
    }
}
