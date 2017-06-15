# unicheck-crop-php-sdk

[![Packagist](https://img.shields.io/packagist/v/unplag/unplag-php-sdk.svg?style=flat-square)](https://packagist.org/packages/unplag/unplag-php-sdk)

PHP SDK for Unicheck.com corporate API 2.0+.  
SDK implements API methods in PHP OOP way.

## Installation
#### Using composer
Package is not submitted to packagist.org, so you should add github repo to composer config
```bash
#Require Unicheck sdk
php composer.phar require unicheck/unicheck-corp-php-sdk
```

## Usage
```php
require_once 'vendor/autoload.php';


//create Unicheck client
$unicheck = new Unicheck\Unicheck('YOUR-API-KEY', 'YOUR-API-SECRET');

//upload file
$file = $unicheck->fileUpload(\Unicheck\PayloadFile::bin($testText), 'txt');

//start check
$checkParam = new \Unicheck\Check\CheckParam($file['id']);
$checkParam->setType(\Unicheck\Check\CheckParam::TYPE_WEB);
$check = $unicheck->checkCreate($checkParam);

var_dump($check);
```

## Help and docs

- [Unicheck API Documentation](https://corpapi.unicheck.com/api/doc)