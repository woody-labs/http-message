<?php

namespace Woody\Http\Message;

use Psr\Http\Message\UploadedFileInterface;
use Zend\Diactoros\UploadedFile;

/**
 * Class UploadedFileFactory
 *
 * @package Woody\Http\Message
 */
class UploadedFileFactory
{

    /**
     * @param array $swoole
     *
     * @return \Psr\Http\Message\UploadedFileInterface
     */
    public function create(array $swoole):UploadedFileInterface
    {
        return new UploadedFile(
            $swoole['tmp_name'],
            $swoole['size'],
            $swoole['error'],
            $swoole['name'],
            $swoole['type']
        );
    }
}
