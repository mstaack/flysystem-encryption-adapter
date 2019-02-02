# Encrypted Flysystem Adapter

[![Build Status](https://img.shields.io/travis/mstaack/flysystem-encryption-adapter.svg?style=flat-square)](https://travis-ci.org/mstaack/flysystem-encryption-adapter)
[![Total Downloads](https://img.shields.io/packagist/dt/mstaack/flysystem-encryption-adapter.svg?style=flat-square)](https://packagist.org/packages/mstaack/flysystem-encryption-adapter)



Uses `halite` as a default or implement `EncryptionInterface`. Also uses a stream filter when requested.

## Installation

```bash
composer require mstaack/flysystem-encryption-adapter
```

## Usage

```php
use League\Flysystem\Filesystem;
use League\Flysystem\Memory\MemoryAdapter;
use MStaack\Flysystem\Encryption\Encryption;
use MStaack\Flysystem\Encryption\EncryptionAdapterDecorator;

$adapter = new MemoryAdapter();
$encryption = new Encryption($encryptionKey='yournicekey');

$adapterDecorator = new EncryptionAdapterDecorator($adapter, $encryption);

$filesystem = new Filesystem($adapterDecorator)
```