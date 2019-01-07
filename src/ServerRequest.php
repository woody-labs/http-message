<?php

namespace Woody\Http\Message;

use GuzzleHttp\Psr7\Uri;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;

/**
 * Class RequestServer
 *
 * @package Woody\Http\Message
 */
class ServerRequest
{

    /**
     * @param \Swoole\Http\Request $swooleRequest
     *
     * @return \Psr\Http\Message\ServerRequestInterface
     */
    public static function createFromSwoole(\Swoole\Http\Request $swooleRequest): ServerRequestInterface
    {
        if (!defined('DOCUMENT_ROOT')) {
            define('DOCUMENT_ROOT', __DIR__);
        }
        if (!defined('SCRIPT_FILENAME')) {
            define('SCRIPT_FILENAME', __FILE__);
        }

        $protocolVersion = str_replace('HTTP/', '', $swooleRequest->server['server_protocol']) ?: '1.1';
        $uri = static::getUriFromRequest($swooleRequest);

        $request = new \GuzzleHttp\Psr7\ServerRequest(
            $swooleRequest->server['request_method'],
            $uri,
            $swooleRequest->header,
            $swooleRequest->rawcontent(),
            $protocolVersion,
            $swooleRequest->server
        );

        $get = $swooleRequest->get ?? [];
        $post = $swooleRequest->post ?? [];
        $cookie = $swooleRequest->cookie ?? [];
        $files = $swooleRequest->files ?? [];
        $server = static::extractServerAttributes($swooleRequest, $uri);

        $request = $request
            ->withCookieParams($cookie)
            ->withQueryParams($get)
            ->withParsedBody($post)
            ->withUploadedFiles(\GuzzleHttp\Psr7\ServerRequest::normalizeFiles($files));

        // For global compatibility.
        static::fillGlobals($get, $post, $cookie, $files, $server);

        return $request;
    }

    /**
     * @param \Swoole\Http\Request $swoole
     *
     * @return array
     */
    private static function extractServerAttributes(\Swoole\Http\Request $swoole, UriInterface $uri): array
    {
        $server = array_change_key_case($swoole->server, CASE_UPPER);
        $server += [
            'DOCUMENT_ROOT' => DOCUMENT_ROOT,
            'PATH' => getenv('PATH'),
            'PHP_SELF' => '/'.basename(SCRIPT_FILENAME),
            'QUERY_STRING' => $swoole->server['query_string'] ?? '',
            'REQUEST_SCHEME' => 'http', // @todo switch on https
            'SCRIPT_FILENAME' => SCRIPT_FILENAME,
            'SERVER_NAME' => $uri->getHost(),
        ];

        foreach ($swoole->header as $name => $value) {
            $server['HTTP_'.strtoupper(str_replace('-', '_', $name))] = $value;
        }

        return $server;
    }

    /**
     * @param \Swoole\Http\Request $swoole
     *
     * @return \GuzzleHttp\Psr7\Uri
     */
    private static function getUriFromRequest(\Swoole\Http\Request $swoole): Uri
    {
        $url = sprintf(
            'http://%s%s%s',
            $swoole->header['host'],
            $swoole->server['request_uri'],
            !empty($swoole->server['query_string']) ? '?'.$swoole->server['query_string'] : ''
        );

        return new Uri($url);
    }

    /**
     * @param array $get
     * @param array $post
     * @param array $cookie
     * @param array $files
     * @param array $server
     */
    private static function fillGlobals(array $get, array $post, array $cookie, array $files, array $server): void
    {
        $_GET = $_POST = $_REQUEST = $_COOKIE = $_FILES = [];

        foreach ($get as $key => $value) {
            $_GET[$key] = $value;
            $_REQUEST[$key] = $value;
        }

        foreach ($post as $key => $value) {
            $_POST[$key] = $value;
            $_REQUEST[$key] = $value;
        }

        foreach ($cookie as $key => $value) {
            $_COOKIE[$key] = $value;
        }

        foreach ($files as $key => $value) {
            $_FILES[$key] = $value;
        }

        foreach ($server as $key => $value) {
            $_SERVER[$key] = $value;
        }
    }
}
