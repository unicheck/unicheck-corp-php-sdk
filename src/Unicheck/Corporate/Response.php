<?php namespace Unicheck\Corporate;


use MessagePack\Unpacker;
use Psr\Http\Message\ResponseInterface;
use Unicheck\Corporate\Exception\UnexpectedResponseException;


/**
 * Class Response
 * @package Unicheck\Corporate
 */
class Response
{
	const ACCEPT_MIME = 'application/x-msgpack';

	protected $_guzzle_resp;
	protected $_data;


	/**
	 * Response constructor.
	 *
	 * @param ResponseInterface $guzzle_response
	 */
	public function __construct(ResponseInterface $guzzle_response)
	{
		if($guzzle_response->getHeaderLine('Content-Type') !== static::ACCEPT_MIME)
		{
			throw new \InvalidArgumentException("Invalid content type received from Unicheck API");
		}

		$unpacker = new Unpacker();
		$this->_data = $unpacker->unpack($guzzle_response->getBody()->getContents());
		$this->_guzzle_resp = $guzzle_response;
	}

	/**
	 * Method getData description.
	 *
	 * @return mixed
	 */
	public function getData()
	{
		return $this->_data;
	}

	/**
	 * Method getGuzzleResponse description.
	 *
	 * @return ResponseInterface
	 */
	public function getGuzzleResponse()
	{
		return $this->_guzzle_resp;
	}

	/**
	 * Method getStatusCode description.
	 *
	 * @return int
	 */
	public function getStatusCode()
	{
		return $this->getGuzzleResponse()->getStatusCode();
	}

	/**
	 * Method getDataProperty description.
	 *
	 * @param $key
	 *
	 * @return null
	 */
	public function getDataProperty($key)
	{
		return isset($this->getData()[$key]) ? $this->getData()[$key] : null;
	}

	/**
	 * Method isSuccess description.
	 *
	 * @return bool
	 */
	public function isSuccess()
	{
		return $this->getStatusCode() === 200 && $this->getDataProperty('result') === true;
	}


	/**
	 * Method __debugInfo description.
	 *
	 * @return array
	 */
	public function __debugInfo()
	{
		return [
			'code' => $this->getStatusCode(),
			'data' => $this->getData()
		];
	}

	/**
	 * Method __toString description.
	 *
	 * @return string
	 */
	public function __toString()
	{
		ob_start();
		var_dump($this);

		return 'Unicheck\Response: ' . ob_get_clean();
	}


	/**
	 * Method getExpectedDataProperty description.
	 *
	 * @param $key
	 *
	 * @return null
	 * @throws UnexpectedResponseException
	 */
	public function getExpectedDataProperty($key)
	{
		$propData = $this->getDataProperty($key);
		if(!$propData)
		{
			throw new UnexpectedResponseException("Response $key property not found. Resp: " . $this, null, $this);
		}

		return $propData;
	}
}