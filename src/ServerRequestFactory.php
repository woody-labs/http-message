<?php

namespace Woody\Http\Message;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;
use Swoole\Http\Request;
use Zend\Diactoros\ServerRequest;

/**
 * Class ServerRequestFactory
 *
 * @package Woody\Http\Message
 */
class ServerRequestFactory
{

    /**
     * @var \Woody\Http\Message\UriFactory
     */
    protected $uriFactory;

    /**
     * @var \Woody\Http\Message\StreamFactory
     */
    protected $streamFactory;

    /**
     * @var \Woody\Http\Message\UploadedFileFactory
     */
    protected $uploadedFileFactory;

    /**
     * ServerRequestFactory constructor.
     *
     * @param \Woody\Http\Message\UriFactory|null $uriFactory
     * @param \Woody\Http\Message\StreamFactory|null $streamFactory
     */
    public function __construct(UriFactory $uriFactory = null, StreamFactory $streamFactory = null, UploadedFileFactory $uploadedFileFactory = null)
    {
        $this->uriFactory = $uriFactory ?? new UriFactory();
        $this->streamFactory = $streamFactory ?? new StreamFactory();
        $this->uploadedFileFactory = $uploadedFileFactory ?? new UploadedFileFactory();
    }

    /**
     * @param \Swoole\Http\Request $swooleRequest
     *
     * @return \Psr\Http\Message\ServerRequestInterface
     */
    public function create(Request $swooleRequest): ServerRequestInterface
    {
        $uri = $this->uriFactory->create($swooleRequest);
        $server = $this->extractServerAttributes($swooleRequest, $uri);
        $headers = $swooleRequest->header ?? [];
        $get = $swooleRequest->get ?? [];
        $post = $swooleRequest->post ?? [];
        $cookies = $swooleRequest->cookie ?? [];
        $files = $this->prepareUploadedFiles($swooleRequest->files ?? []);

        $method = $swooleRequest->server['request_method'];
        $body = $this->streamFactory->create($swooleRequest);
        $protocol = str_replace('HTTP/', '', $swooleRequest->server['server_protocol']) ?: '1.1';

        $request = new ServerRequest(
            $server,
            $files,
            $uri,
            $method,
            $body,
            $headers,
            $cookies,
            $get,
            $post,
            $protocol
        );

        return $request;
    }

    /**
     * @param \Swoole\Http\Request $swoole
     * @param \Psr\Http\Message\UriInterface $uri
     *
     * @return array
     */
    private function extractServerAttributes(Request $swoole, UriInterface $uri): array
    {
        $server = array_change_key_case($swoole->server, CASE_UPPER);
        $server += [
            'DOCUMENT_ROOT' => __DIR__,
            'PATH' => getenv('PATH'),
            'PHP_SELF' => '/'.basename(__DIR__),
            'QUERY_STRING' => $swoole->server['query_string'] ?? '',
            'REQUEST_SCHEME' => 'http', // @todo switch on https
            'SCRIPT_FILENAME' => __FILE__,
            'SERVER_NAME' => $uri->getHost(),
        ];

        foreach ($swoole->header as $name => $value) {
            $server['HTTP_'.strtoupper(str_replace('-', '_', $name))] = $value;
        }

        return $server;
    }

    /**
     * @param array $files
     *
     * @return \Psr\Http\Message\UploadedFileInterface[]
     */
    private function prepareUploadedFiles(array $files): array
    {
        $uploadedFiles = [];

        foreach ($files as $file) {
            $uploadedFiles[] = $this->uploadedFileFactory->create($file);
        }

        return $uploadedFiles;
    }
}
