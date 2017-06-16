<?php namespace Unicheck\Corporate\Client;

use Unicheck\Corporate\Request;
use Unicheck\Corporate\Response;

/**
 * Interface Client
 * @package Unicheck\Corporate\Client
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