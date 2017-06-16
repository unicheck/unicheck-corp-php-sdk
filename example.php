<?php

require_once 'vendor/autoload.php';

use Unicheck\Corporate\Unicheck;
use Unicheck\Corporate\PayloadFile;
use Unicheck\Corporate\Exception\UnicheckException;
use Unicheck\Corporate\Check\CheckParam;

//create Unicheck client
$unicheck = new Unicheck('YOUR-API-KEY', 'YOUR-API-SECRET');


//upload file
$testText = <<<EOF
PHP code may be embedded into HTML code, or it can be used in combination with various web template systems, web content management systems and web frameworks.
PHP code is usually processed by a PHP interpreter implemented as a module in the web server or as a Common Gateway Interface (CGI) executable.
The web server combines the results of the interpreted and executed PHP code, which may be any type of data, including images, with the generated web page.
PHP code may also be executed with a command-line interface (CLI) and can be used to implement standalone graphical applications.
EOF;

try
{
	$file = $unicheck->fileUpload(PayloadFile::bin($testText), 'txt');
}
catch (UnicheckException $e)
{
	echo $e;
	die('File upload error: ' . $e->getMessage() . PHP_EOL);
}

echo 'File uploaded!' . PHP_EOL;
var_dump($file);

//start check
$checkParam = new CheckParam($file['id']);
$checkParam->setType(CheckParam::TYPE_WEB);

try
{
	$check = $unicheck->checkCreate($checkParam);
}
catch (UnicheckException $e)
{
	die('Check start fail: ' . $e->getMessage() . PHP_EOL);
}

echo 'Check started!' . PHP_EOL;
var_dump($check);

