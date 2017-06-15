<?php namespace Unicheck\Client;

use Unicheck\Client;
use Unicheck\Request;
use Unicheck\Response;
use Unicheck\Exception\CallbackException;

use \MessagePack\Unpacker;

/**
 * Class CallbackTrait
 * @package Unicheck\Client
 *
 * @method Response execute(Request $request)
 * @method Client getClient()
 */
trait CallbackTrait
{

    /**
     * Method resolveCallback description.
     *
     * @return mixed
     * @throws CallbackException
     */
    public function resolveCallback()
    {
        $contentType = $_SERVER['CONTENT_TYPE'];
        if( empty($contentType) || strpos(Response::ACCEPT_MIME, $contentType) === false )
        {
            throw new CallbackException('Invalid MIME type');
        }

        $content = file_get_contents("php://input");
        if( empty($content) )
        {
            throw new CallbackException('Callback content body is empty');
        }

        $unpacker = new Unpacker();
        $params = $unpacker->unpack($content);

        return $params;
    }

}