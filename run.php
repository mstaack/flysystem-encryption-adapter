<?php

require_once 'vendor/autoload.php';

use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use MStaack\Flysystem\Encryption\Encryption;
use MStaack\Flysystem\Encryption\EncryptionAdapterDecorator;


$adapter = new Local(__DIR__);
$encryption = new Encryption('de25c3425f346fmokey');

$adapterDecorator = new EncryptionAdapterDecorator($adapter, $encryption);

$filesystem = new Filesystem($adapterDecorator);

$filesystem->putStream('demo', bin2hex(openssl_random_pseudo_bytes(2048)));
