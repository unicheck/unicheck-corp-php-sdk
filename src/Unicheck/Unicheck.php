<?php namespace Unicheck;


use Unicheck\Check\CheckParam;
use Unicheck\Exception\ApiException;
use Unicheck\Client\CallbackTrait;
use Unicheck\Client\Client as IClient;
use Unicheck\Exception\UnexpectedResponseException;

/**
 * Class Unicheck
 * @package Unicheck
 */
class Unicheck implements IClient
{
	use CallbackTrait;

	const LANG_EN = 'en_EN';
	const LANG_UA = 'uk_UA';
	const LANG_ES = 'es_ES';
	const LANG_BE = 'nl_BE';
	const LANG_TR = 'tr_TR';

	/**
	 * @var Client
	 */
	protected $client;

	/**
	 * Unicheck constructor.
	 *
	 * @param string $key Valid API key 16-32 chars
	 * @param string $secret Valid API secret 32-64 chars
	 */
	public function __construct($key, $secret)
	{
		$this->client = new Client($key, $secret);
	}

	/**
	 * @return Client
	 */
	public function getClient()
	{
		return $this->client;
	}


	/**
	 * Executes Request.
	 * Alias for ->getClient()->execute($request)
	 *
	 * @param Request $request
	 *
	 * @return Response
	 * @throws Exception\ApiException
	 * @throws Exception\RequestException
	 * @throws Exception\ResponseException
	 */
	public function execute(Request $request)
	{
		return $this->getClient()->execute($request);
	}


	/**
	 * @param PayloadFile $file
	 * @param string $format
	 * @param string $name Optional (default: random generated)
	 * @param int $directoryId Optional (default: 0)
	 *
	 * @return array
	 * @throws UnexpectedResponseException
	 */
	public function fileUpload(PayloadFile $file, $format, $name = null, $directoryId = null)
	{
		$params =
			[
				'file' => $file,
				'format' => $format
			];

		if($name)
		{
			$params['name'] = $name;
		}

		if($directoryId)
		{
			$params['directory_id'] = $directoryId;
		}

		$req = new Request(Request::METHOD_POST, 'file/upload', $params);

		return $this->execute($req)->getExpectedDataProperty('file');
	}


	/**
	 * @param int $fileId
	 *
	 * @return int
	 * @throws UnexpectedResponseException
	 */
	public function fileDelete($fileId)
	{
		$req = new Request(Request::METHOD_POST, 'file/delete', [
			'id' => $fileId
		]);
		$resp = $this->execute($req);
		$fileData = $resp->getExpectedDataProperty('file');
		if(false === isset($fileData['id']))
		{
			throw new UnexpectedResponseException("File delete response do not contain file ID. " . $resp, null, $resp);
		}

		return $fileData['id'];
	}


	/**
	 * @param int $fileId
	 *
	 * @return array File info
	 *
	 * @throws UnexpectedResponseException
	 */
	public function fileGet($fileId)
	{
		$req = new Request(Request::METHOD_GET, 'file/get', [
			'id' => $fileId
		]);

		return $this->execute($req)->getExpectedDataProperty('file');
	}


	/**
	 * @param CheckParam $checkParam
	 *
	 * @return array Check info
	 *
	 * @throws UnexpectedResponseException
	 */
	public function checkCreate(CheckParam $checkParam)
	{
		$req = new Request(Request::METHOD_POST, 'check/create', $checkParam->mergeParams());

		return $this->execute($req)->getExpectedDataProperty('check');
	}


	/**
	 * @param int $checkId
	 *
	 * @return array Check info
	 *
	 * @throws UnexpectedResponseException
	 */
	public function checkGetInfo($checkId)
	{
		$req = new Request(Request::METHOD_GET, 'check/get', [
			'id' => $checkId
		]);

		return $this->execute($req)->getExpectedDataProperty('check');
	}


	/**
	 * @param int $checkId
	 *
	 * @return int ID of deleted check
	 *
	 * @throws UnexpectedResponseException
	 */
	public function checkDelete($checkId)
	{
		$req = new Request(Request::METHOD_POST, 'check/delete', [
			'id' => $checkId
		]);
		$resp = $this->execute($req);
		$checkData = $resp->getExpectedDataProperty('check');
		if(false === isset($checkData['id']))
		{
			throw new UnexpectedResponseException(
				"Check delete response do not contain check ID. " . $resp,
				null,
				$resp
			);
		}

		return $checkData['id'];
	}


	/**
	 * @param int $checkId
	 * @param string $lang Optional (Default: LANG_EN)
	 *
	 * @return array PDF report info
	 */
	public function checkGeneratePdf($checkId, $lang = self::LANG_EN)
	{
		$req = new Request(Request::METHOD_POST, 'check/generate_pdf', [
			'id' => $checkId,
			'lang' => $lang
		]);

		return $this->execute($req)->getExpectedDataProperty('pdf_report');
	}


	/**
	 * @param int $checkId
	 * @param string $lang Optional (default: LANG_EN)
	 * @param bool $showLangPicker Optional (default: false)
	 *
	 * @return array ["lang" => lang, "view_url" => ..., "view_edit_url" => ...]
	 */
	public function checkGetReportLink($checkId, $lang = self::LANG_EN, $showLangPicker = false)
	{
		$req = new Request(Request::METHOD_GET, 'check/get_report_link', [
			'id' => $checkId,
			'lang' => $lang,
			'show_lang_picker' => $showLangPicker
		]);
		$res = $this->execute($req);

		return [
			'lang' => $res->getExpectedDataProperty('lang'),
			'view_url' => $res->getExpectedDataProperty('view_url'),
			'view_edit_url' => $res->getExpectedDataProperty('view_edit_url')
		];
	}

	/**
	 * @param int $checkId
	 * @param boolean $excludeCitations
	 * @param boolean $excludeReferences
	 *
	 * @return array Check info
	 */
	public function checkToggleCitations($checkId, $excludeCitations, $excludeReferences)
	{
		$req = new Request(Request::METHOD_POST, 'check/toggle', [
			'id' => $checkId,
			'exclude_citations' => $excludeCitations,
			'exclude_references' => $excludeReferences
		]);

		return $this->execute($req)->getExpectedDataProperty('check');
	}

	/**
	 * @param int[] $checkIds Array of checks to track progress
	 *
	 * @return array [check ID => progress]
	 */
	public function checkTrackProgress(array $checkIds)
	{
		$req = new Request(Request::METHOD_GET, 'check/progress', [
			'id' => implode(',', $checkIds)
		]);

		return $this->execute($req)->getExpectedDataProperty('progress');
	}

    /**
     * @param int $checkId
     *
     * @return null
     */
	public function checkGetSourcesList($checkId)
	{
		$req = new Request(Request::METHOD_GET, 'check/get_sources_list', [
			'id' => $checkId
		]);

		return $this->execute($req)->getExpectedDataProperty('sources');
	}

	/**
	 * @param int $directoryId (Default: 0 - root directory)
	 *
	 * @return array
	 */
	public function directoryGet($directoryId = 0)
	{
		$req = new Request(Request::METHOD_GET, 'directory/get', [
			'id' => $directoryId,
			'list' => 1
		]);

		return [
			'directory' => $this->execute($req)->getExpectedDataProperty('directory'),
			'list' => $this->execute($req)->getExpectedDataProperty('list')
		];
	}

	/**
	 * @param $name
	 * @param int $parentId (Default: 0 - root directory)
	 *
	 * @return array
	 */
	public function directoryCreate($name, $parentId = 0)
	{
		$req = new Request(Request::METHOD_POST, 'directory/create', [
			'name' => $name,
			'parent_id' => $parentId
		]);

		return $this->execute($req)->getExpectedDataProperty('directory');
	}

	/**
	 * @param $directoryId
	 *
	 * @return int
	 *
	 * @throws UnexpectedResponseException
	 * @throws \InvalidArgumentException
	 */
	public function directoryDelete($directoryId)
	{
		if($directoryId === 0)
		{
			throw new \InvalidArgumentException('Can not delete root (ID: 0) directory');
		}

		$req = new Request(Request::METHOD_POST, 'directory/delete', [
			'id' => $directoryId
		]);

		$resp = $this->execute($req);
		$directoryData = $resp->getExpectedDataProperty('id');
		if(false === isset($directoryData['id']))
		{
			throw new UnexpectedResponseException(
				"Directory delete response do not contain directory ID. " . $resp,
				null,
				$resp
			);
		}

		return $directoryData['id'];
	}
}
