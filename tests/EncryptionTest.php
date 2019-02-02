<?php

namespace MStaack\Flysytem\Encryption\Tests;

use League\Flysystem\Filesystem;
use League\Flysystem\Memory\MemoryAdapter;
use League\Flysystem\Util;
use MStaack\Flysystem\Encryption\Encryption;
use MStaack\Flysystem\Encryption\EncryptionAdapterDecorator;
use MStaack\Flysystem\Encryption\Tests\TestCase;

class EncryptionTest extends TestCase
{
    public function test_stored_content_is_encrypted()
    {
        $filePath = '/demo.txt';
        $content = bin2hex(openssl_random_pseudo_bytes(20));

        $adapter = new MemoryAdapter();
        $encryption = new Encryption('ieTh3iepa5yu4nei');
        $filesystem = new Filesystem(new EncryptionAdapterDecorator($adapter, $encryption));

        $filesystem->put($filePath, $content);

        //api
        $this->assertSame($content, $filesystem->read($filePath));

        //raw data
        $this->assertNotSame($content, $adapter->read(Util::normalizePath($filePath)));
    }

    public function foo_test_encryption_and_decryption()
    {
        $filePath = '/demo.txt';
        $content = bin2hex(openssl_random_pseudo_bytes(20));

        $sourceFilesystem = $this->createTestFilesystem();

        $this->assertTrue($sourceFilesystem->put($filePath, $content));
        $this->assertTrue($sourceFilesystem->has($filePath));

        $targetFilesystem = $this->createEncryptedTestFilesystem();
        $targetFilesystem->put($filePath, $sourceFilesystem->read($filePath));

        $this->assertSame($content, $targetFilesystem->read($filePath));
    }

    public function test_streamed_text_file()
    {
        $string = 'I tried, honestly!';
        $source = fopen('data://text/plain,' . $string, 'rb');

        $targetFilesystem = $this->createEncryptedTestFilesystem();

        $filePath = '/demo-text-crypted.txt';

        if ($targetFilesystem->has($filePath)) {
            $targetFilesystem->delete($filePath);
        }

        $targetFilesystem->writeStream($filePath, $source);

        $this->assertSame($string, stream_get_contents($targetFilesystem->readStream($filePath)));
        $this->assertSame($string, $targetFilesystem->read($filePath));
    }

    public function test_streamed_binary_file()
    {
        $binaryBlob = openssl_random_pseudo_bytes(2000);

        $source = fopen('php://memory', 'wb+');
        fwrite($source, $binaryBlob);
        rewind($source);

        $targetFilesystem = $this->createEncryptedTestFilesystem();

        $filePath = '/demo-bin-crypted.bin';

        if ($targetFilesystem->has($filePath)) {
            $targetFilesystem->delete($filePath);
        }

        $targetFilesystem->writeStream($filePath, $source);

        $this->assertSame(
            sha1($binaryBlob),
            sha1(stream_get_contents($targetFilesystem->readStream($filePath)))
        );

        $this->assertSame(
            sha1($binaryBlob),
            sha1($targetFilesystem->read($filePath))
        );
    }
}