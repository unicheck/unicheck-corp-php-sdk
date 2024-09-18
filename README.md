# [DEPRECATED] This project has reached the end of its development

# unicheck-corp-php-sdk

[![Packagist](https://img.shields.io/packagist/v/unicheck/unicheck-corp-php-sdk.svg?style=flat-square)](https://packagist.org/packages/unicheck/unicheck-corp-php-sdk)

PHP SDK for Unicheck.com corporate API 2.0+.  
SDK implements API methods in PHP OOP way.

## Installation
#### Using composer
```bash
#Require Unicheck sdk
php composer.phar require unicheck/unicheck-corp-php-sdk
```

## Usage
```php
//create Unicheck client
$unicheck = new Unicheck('YOUR-API-KEY', 'YOUR-API-SECRET');

//upload file
$file = $unicheck->fileUpload(PayloadFile::bin($testText), 'txt');

//start check
$checkParam = new CheckParam($file['id']);
$checkParam->setType(CheckParam::TYPE_WEB);

$check = $unicheck->checkCreate($checkParam);

echo 'Check started!' . PHP_EOL;
var_dump($check);
```

## Help and docs

- [Unicheck API Documentation](https://corpapi.unicheck.com/api/doc)
