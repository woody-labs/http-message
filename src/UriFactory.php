<?php

namespace Woody\Http\Message;

use Psr\Http\Message\UriInterface;
use Swoole\Http\Request;
use Zend\Diactoros\Uri;

/**
 * Class UriFactory
 *
 * @package Woody\Http\Message
 */
class UriFactory
{

    /**
     * @param \Swoole\Http\Request $swoole
     *
     * @return \Psr\Http\Message\UriInterface
     */
    public function create(Request $swoole): UriInterface
    {
        // @todo: support https scheme.
        $url = sprintf(
            'http://%s%s%s',
            $swoole->header['host'],
            $swoole->server['request_uri'],
            !empty($swoole->server['query_string']) ? '?'.$swoole->server['query_string'] : ''
        );

        return new Uri($url);
    }
}
