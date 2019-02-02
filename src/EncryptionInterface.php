<?php

namespace MStaack\Flysystem\Encryption;

interface EncryptionInterface
{
    /**
     * @param $contents
     * @return string
     */
    public function encrypt($contents);

    /**
     * @param $resource
     * @return string
     */
    public function appendEncryptStreamFilter($resource);

    /**
     * @param $resource
     * @return string
     */
    public function appendDecryptStreamFilter($resource);

    /**
     * @param $contents
     * @return string
     */
    public function decrypt($contents);
}