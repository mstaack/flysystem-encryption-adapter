<?php

namespace MStaack\Flysystem\Encryption;

use ParagonIE\Halite\KeyFactory;
use ParagonIE\Halite\Symmetric\Crypto;
use ParagonIE\Halite\Symmetric\EncryptionKey;
use ParagonIE\HiddenString\HiddenString;
use function Clue\StreamFilter\append;

class Encryption implements EncryptionInterface
{
    /**
     * @var EncryptionKey
     */
    private $key;

    /**
     * Encryption constructor.
     * @param string $encryptionKey
     * @throws \ParagonIE\Halite\Alerts\InvalidKey
     * @throws \ParagonIE\Halite\Alerts\InvalidSalt
     * @throws \ParagonIE\Halite\Alerts\InvalidType
     */
    public function __construct(string $encryptionKey)
    {
        $this->key = KeyFactory::deriveEncryptionKey(
            new HiddenString($encryptionKey),
            random_bytes(16)
        );
    }

    /**
     * @param $contents
     * @return string
     */
    public function encrypt($contents)
    {
        $source = $this->createTemporaryStreamFromContents($contents);

        $this->appendEncryptStreamFilter($source);

        return stream_get_contents($source);
    }

    /**
     * @param $contents
     * @return string
     */
    public function decrypt($contents)
    {
        $source = $this->createTemporaryStreamFromContents($contents);

        $this->appendDecryptStreamFilter($source);

        return stream_get_contents($source);
    }

    /**
     * @param $resource
     * @return string
     */
    public function appendEncryptStreamFilter($resource)
    {
        append($resource, function ($chunk) {
            return Crypto::encrypt(new HiddenString($chunk), $this->key);
        });
    }

    /**
     * @param $resource
     * @return string
     */
    public function appendDecryptStreamFilter($resource)
    {
        append($resource, function ($chunk) {
            return Crypto::decrypt($chunk, $this->key);
        });
    }

    /**
     * @param $contents
     * @return bool|resource
     */
    private function createTemporaryStreamFromContents($contents)
    {
        $source = fopen('php://temp/maxmemory', 'wb+');
        fwrite($source, $contents);
        rewind($source);

        return $source;
    }
}