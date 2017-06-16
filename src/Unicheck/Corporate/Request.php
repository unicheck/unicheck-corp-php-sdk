<?php namespace Unicheck\Corporate;

use GuzzleHttp\Psr7\Uri;
use MessagePack\Packer;

/**
 * Class Request
 * @package Unicheck\Corporate
 */
class Request
{

	const CONTENT_MIME = 'application/x-msgpack';
	const METHOD_POST = 'POST';
	const METHOD_GET = 'GET';

	protected $method;
	protected $uri;
	protected $payload;

	/**
	 * Request constructor.
	 *
	 * @param $method
	 * @param $uri
	 * @param array $payload
	 */
	public function __construct($method, $uri, $payload = [])
	{
		if(!in_array($method, [static::METHOD_GET, static::METHOD_POST]))
		{
			throw new \InvalidArgumentException("Invalid method $method");
		}
		$this->method = $method;
		$this->uri = $uri;
		$this->payload = $payload;
	}

	/**
	 * Method payloadSet description.
	 *
	 * @param $key
	 * @param $value
	 *
	 * @return $this
	 */
	public function payloadSet($key, $value)
	{
		$this->payload[$key] = $value;

		return $this;
	}

	/**
	 * Method payloadGet description.
	 *
	 * @param $key
	 *
	 * @return mixed|null
	 */
	public function payloadGet($key)
	{
		return isset($this->payload[$key]) ? $this->payload[$key] : null;
	}

	/**
	 * Method payloadUnset description.
	 *
	 * @param $key
	 *
	 * @return $this
	 */
	public function payloadUnset($key)
	{
		unset($this->payload[$key]);

		return $this;
	}


	/**
	 * Method packPayload description.
	 *
	 * @return string
	 * @throws Exception\PayloadException
	 */
	protected function packPayload()
	{
		$packer = new Packer;

		$data = [];
		foreach($this->payload as $key => &$value)
		{
			if($value instanceof PayloadFile)
			{
				$data[$key] = &$value->getBinaryData();
			}
			else
			{
				$data[$key] = &$value;
			}
		}


		return $packer->packMap($data);
	}


	/**
	 * Method makeGuzzleRequest description.
	 *
	 * @return \GuzzleHttp\Psr7\Request
	 */
	public function makeGuzzleRequest()
	{
		$headers = [
			'Accept' => Response::ACCEPT_MIME,
			'Content-Type' => static::CONTENT_MIME
		];

		$uri = new Uri($this->uri);
		if($this->method == self::METHOD_GET)
		{
			$body = null;
			$new_uri = $uri->withQuery(http_build_query($this->payload));
		}
		else
		{
			$body = $this->packPayload();
			$new_uri = $uri;
		}

		return new \GuzzleHttp\Psr7\Request($this->method, $new_uri, $headers, $body);
	}


	/**
	 * Method __debugInfo description.
	 *
	 * @return array
	 */
	public function __debugInfo()
	{
		return [
			'method' => $this->method,
			'uri' => $this->uri,
			'payload' => $this->payload
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

		return 'Unicheck\Request: ' . ob_get_clean();
	}
}
