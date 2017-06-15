<?php namespace Unicheck\Exception;


use Unicheck\Response;

/**
 * Class UnexpectedResponseException
 * @package Unicheck\Exception
 */
class UnexpectedResponseException extends \Exception implements UnicheckException
{

	const CODE_UNEXPECTED_RESPONSE = 601;
	protected $response;

	/**
	 * UnexpectedResponseException constructor.
	 *
	 * @param string $message
	 * @param \Exception|null $previous
	 * @param Response $response
	 */
	public function __construct($message, \Exception $previous = null, Response $response)
	{
		parent::__construct($message, static::CODE_UNEXPECTED_RESPONSE, $previous);
		$this->response = $response;
	}

	/**
	 * Method getResponse description.
	 *
	 * @return Response
	 */
	public function getResponse()
	{
		return $this->response;
	}


}