<?php namespace Unicheck\Corporate\Exception;

use Unicheck\Corporate\Request;
use Unicheck\Corporate\Response;

/**
 * Class ResponseException
 * @package Unicheck\Corporate\Exception
 */
class ResponseException extends \Exception implements UnicheckException
{
    const CODE_RESPONSE_PARSE_FAIL = 203;
    const CODE_INVALID_CONTENT_TYPE = 204;

    protected $req;
    protected $resp = null;

    /**
     * ResponseException constructor.
     * @param string $message
     * @param int $code
     * @param \Exception|null $previous
     * @param Request $request
     * @param Response|null $response
     */
    public function __construct($message, $code, \Exception $previous = null, Request $request, Response $response = null)
    {
        $this->req = $request;
        $this->resp = $response;
        parent::__construct($message, $code, $previous);

    }

    /**
     * Method getRequest description.
     *
     * @return Request
     */
    public function getRequest()
    {
        return $this->req;
    }

    /**
     * Method getResponse description.
     *
     * @return null|Response
     */
    public function getResponse()
    {
        return $this->resp;
    }

    /**
     * Method hasResponse description.
     *
     * @return bool
     */
    public function hasResponse()
    {
        return (bool)$this->getResponse();
    }
}