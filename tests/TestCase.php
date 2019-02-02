<?php

namespace MStaack\Flysystem\Encryption\Tests;

use League\Flysystem\AdapterInterface;
use League\Flysystem\Filesystem;
use League\Flysystem\Memory\MemoryAdapter;
use MStaack\Flysystem\Encryption\Encryption;
use MStaack\Flysystem\Encryption\EncryptionAdapterDecorator;

abstract class TestCase extends \PHPUnit\Framework\TestCase
{
    /**
     * @return Filesystem
     */
    protected function createTestFilesystem(): Filesystem
    {
        return new Filesystem(new MemoryAdapter());
    }

    /**
     * @param AdapterInterface|null $adapter
     * @param string $encryptionKey
     * @return Filesystem
     */
    protected function createEncryptedTestFilesystem(
        AdapterInterface $adapter = null,
        $encryptionKey = 'ieTh3iepa5yu4nei'
    ): Filesystem
    {
        $adapter = $adapter ?? new MemoryAdapter();
        $encryption = new Encryption($encryptionKey);

        $adapterDecorator = new EncryptionAdapterDecorator($adapter, $encryption);

        return new Filesystem($adapterDecorator);
    }
}