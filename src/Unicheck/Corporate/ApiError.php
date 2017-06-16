<?php namespace Unicheck\Corporate;


/**
 * Class ApiError
 * @package Unicheck\Corporate
 */
class ApiError
{
	protected $code;
	protected $msg;
	protected $httpStatusCode;

	/**
	 * ApiError constructor.
	 *
	 * @param $code
	 * @param $msg
	 * @param $httpStatusCode
	 */
	public function __construct($code, $msg, $httpStatusCode)
	{
		$this->code = $code;
		$this->msg = $msg;
		$this->httpStatusCode = $httpStatusCode;
	}

	/**
	 * Method getCode description.
	 *
	 * @return mixed
	 */
	public function getCode()
	{
		return $this->code;
	}

	/**
	 * Method getMsg description.
	 *
	 * @return mixed
	 */
	public function getMsg()
	{
		return $this->msg;
	}

	/**
	 * Method getHttpStatusCode description.
	 *
	 * @return mixed
	 */
	public function getHttpStatusCode()
	{
		return $this->httpStatusCode;
	}


	/**
	 * Method __toString description.
	 *
	 * @return string
	 */
	public function __toString()
	{
		$pattern = "Unicheck API error. Code: %s; Msg: %s; HTTP status code: %d";

		return sprintf($pattern, $this->getCode(), $this->getMsg(), $this->getHttpStatusCode());
	}
}