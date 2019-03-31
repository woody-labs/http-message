<?php

namespace Woody\Http\Message\Tests;

use PHPUnit\Framework\TestCase;
use Woody\Http\Message\UploadedFileFactory;

/**
 * Class UploadedFileFactoryTest
 *
 * @package Woody\Http\Message\Tests
 */
class UploadedFileFactoryTest extends TestCase
{

    public function testFile()
    {
        $files = [];
        $files[] = [
            'tmp_name' => '/tmp/trtere65',
            'size' => 42,
            'error' => 0,
            'name' => 'file.txt',
            'type' => 'text/plain',
        ];

        $request = $this->createMock(\Swoole\Http\Request::class);
        $request->files = $files;

        $uploadedFileFactory = new UploadedFileFactory();
        $uploadedFile = $uploadedFileFactory->create($files[0]);

        $this->assertEquals(42, $uploadedFile->getSize());
        $this->assertEquals('file.txt', $uploadedFile->getClientFilename());
        $this->assertEquals('text/plain', $uploadedFile->getClientMediaType());
        $this->assertEquals(0, $uploadedFile->getError());
    }
}
