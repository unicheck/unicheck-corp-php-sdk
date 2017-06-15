<?php namespace Unicheck\Client;

use Unicheck\Request;
use Unicheck\Response;

/**
 * Interface Client
 * @package Unicheck\Client
 */
interface Client
{
    /**
     * Method getClient description.
     *
     * @return Client
     */
    public function getClient();

    /**
     * Method execute description.
     * @param Request $request
     *
     * @return Response
     */
    public function execute(Request $request);
}