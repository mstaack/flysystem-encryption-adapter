<?php

namespace MStaack\Flysystem\Encryption;

use League\Flysystem\AdapterDecorator\DecoratorTrait;
use League\Flysystem\AdapterInterface;
use League\Flysystem\Config;

/**
 * Class EncryptionDecorator
 */
class EncryptionAdapterDecorator implements AdapterInterface
{
    use DecoratorTrait;

    /**
     * @var AdapterInterface
     */
    protected $adapter;

    /**
     * @var EncryptionInterface
     */
    private $encryption;

    /**
     * @param AdapterInterface $adapter
     * @param EncryptionInterface $encryption
     */
    public function __construct(AdapterInterface $adapter, EncryptionInterface $encryption)
    {
        $this->adapter = $adapter;

        $this->encryption = $encryption;
    }

    /**
     * @return AdapterInterface
     */
    protected function getDecoratedAdapter()
    {
        return $this->adapter;
    }

    /**
     * @param string $path
     * @param string $contents
     * @param Config $config
     * @return array|false
     */
    public function write($path, $contents, Config $config)
    {
        $contents = $this->encryption->encrypt($contents);

        return $this->getDecoratedAdapter()->write($path, $contents, $config);
    }

    /**
     * @param string $path
     * @param string $contents
     * @param Config $config
     * @return array|false
     */
    public function update($path, $contents, Config $config)
    {
        return $this->write($path, $contents, $config);
    }

    /**
     * @param string $path
     * @return array|false
     */
    public function read($path)
    {
        $response = $this->getDecoratedAdapter()->read($path);

        $response['contents'] = $this->encryption->decrypt($response['contents']);

        return $response;
    }

    public function writeStream($path, $resource, Config $config)
    {
        $this->encryption->appendEncryptStreamFilter($resource);

        return $this->getDecoratedAdapter()->writeStream($path, $resource, $config);
    }

    public function readStream($path)
    {
        $resource = $this->getDecoratedAdapter()->readStream($path)['stream'];

        $this->encryption->appendDecryptStreamFilter($resource);

        return ['type' => 'file', 'path' => $path, 'stream' => $resource];
    }
}