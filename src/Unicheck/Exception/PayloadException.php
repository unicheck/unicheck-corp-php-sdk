<?php namespace Unicheck\Exception;

/**
 * Class PayloadException
 * @package Unicheck\Exception
 */
class PayloadException extends \Exception implements UnicheckException
{
    const CODE_INVALID_RESOURCE = 101;
    const CODE_PATH_NOT_READABLE = 102;
    const CODE_FAILED_TO_READ_FILE = 103;
    const CODE_FAILED_TO_READ_RESOURCE = 104;
}