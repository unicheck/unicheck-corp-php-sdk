<?php namespace Unicheck;


use GuzzleHttp\HandlerStack;
use GuzzleHttp\Subscriber\Oauth\Oauth1;
use Psr\Http\Message\ResponseInterface;
use Unicheck\Exception\ApiException;
use Unicheck\Exception\RequestException;
use Unicheck\Exception\ResponseException;

/**
 * Class Client
 * @package Unicheck
 */
class Client
{

	protected static $keyRegex = '/^[A-z0-9]{16,32}$/';
	protected static $secretRegex = '/^[A-z0-9]{32,64}$/';

	protected $key;
	protected $secret;

	protected $apiHost = 'https://corpapi.unicheck.com';
	protected $apiRootPath = '/api/v2/';

	/**
	 * @var \GuzzleHttp\Client
	 */
	protected $client;


	/**
	 * Client constructor.
	 *
	 * @param string $key
	 * @param string $secret
	 * @param array  $options
	 */
	public function __construct($key, $secret, array $options = [])
	{
		if (!preg_match(static::$keyRegex, $key))
		{
			throw new \InvalidArgumentException("Invalid key $key");
		}

		if (!preg_match(static::$secretRegex, $secret))
		{
			throw new \InvalidArgumentException("Invalid secret $secret");
		}

		$this->key = $key;
		$this->secret = $secret;

		if (isset($options['host']))
		{
			$this->apiHost = $options['host'];
		}

		$this->createGuzzleClient();
	}

	/**
	 * Method createGuzzleClient description.
	 */
	protected function createGuzzleClient()
	{
		$stack = HandlerStack::create();

		$middleware = new Oauth1([
			'consumer_key'    => $this->key,
			'consumer_secret' => $this->secret,
			'token_secret'    => '',
			'token'           => '',
		]);

		$stack->push($middleware);

		$this->client = new \GuzzleHttp\Client([
			'base_uri' => $this->getBaseUrl(),
			'handler'  => $stack,
			'auth'     => 'oauth'
		]);
	}

	/**
	 * @return string
	 */
	public function getBaseUrl()
	{
		return $this->apiHost . $this->apiRootPath;
	}

	/**
	 * Method execute description.
	 *
	 * @param Request $request
	 *
	 * @return Response
	 * @throws ApiException
	 * @throws RequestException
	 * @throws ResponseException
	 */
	public function execute(Request $request)
	{
		try
		{
			$guzzle_response = $this->client->send($request->makeGuzzleRequest());
		}
		catch (\GuzzleHttp\Exception\RequestException $ex)
		{
			if (!$ex->hasResponse())
			{
				throw new RequestException($ex->getMessage(), $ex->getCode(), $ex, $request);
			}

			try
			{
				$response = new Response($ex->getResponse());
			}
			catch (\Exception $ex2)
			{
				if ($ex instanceof \InvalidArgumentException)
				{
					$code = ResponseException::CODE_INVALID_CONTENT_TYPE;
				}
				else
				{
					$code = ResponseException::CODE_RESPONSE_PARSE_FAIL;
				}
				throw new ResponseException("Failed to obtain error response. Resp: " . $this->_dumpResponse($ex->getResponse()), $code, $ex2, $request, null);
			}

			throw new ApiException($request, $response, $ex);
		}
		catch (\Exception $ex)
		{
			throw new RequestException($ex->getMessage(), $ex->getCode(), $ex, $request);
		}


		try
		{
			$response = new Response($guzzle_response);
		}
		catch (\Exception $ex)
		{
			if ($ex instanceof \InvalidArgumentException)
			{
				$code = ResponseException::CODE_INVALID_CONTENT_TYPE;
			}
			else
			{
				$code = ResponseException::CODE_RESPONSE_PARSE_FAIL;
			}
			throw new ResponseException("Response parse failed. Resp: " . $this->_dumpResponse($guzzle_response), $code, $ex, $request, null);
		}

		if (!$response->isSuccess())
		{
			throw new ApiException($request, $response);
		}

		return $response;
	}


	/**
	 * Method _dumpResponse description.
	 *
	 * @param ResponseInterface $resp
	 *
	 * @return string
	 */
	protected function _dumpResponse(ResponseInterface $resp)
	{
		$pattern = "Status: %d Body: %s";

		return sprintf($pattern, $resp->getStatusCode(), $resp->getBody()->getContents());
	}

	/**
	 * Method getKey description.
	 *
	 * @return mixed
	 */
	public function getKey()
	{
		return $this->key;
	}
}