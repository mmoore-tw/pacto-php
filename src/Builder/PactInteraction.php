<?php

namespace Pact\Phpacto\Builder;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Class PactInteraction
 */
class PactInteraction
{

    private $description;
    private $providerState;
    private $request;
    private $response;
    private $matchingRules;

    public function __construct()
    {
        $this->description = "";
        $this->providerState = "";
        $this->request = array("method" => "", "path" => "", "headers" => array(), "query" => "");
        $this->response = array("status" => 0, "headers" => array(), "body" => array());
    }

    public function Description($description)
    {
        $this->description = $description;
        return $this;
    }

    public function ProviderState($state)
    {
        $this->providerState = $state;
        return $this;
    }

    public function Method(){
        return $this->request['method'];
    }

    public function RequestMethod($method)
    {
        // check type and if it is a valid HTTP verb
        if (!is_string($method)) {
            throw new \InvalidArgumentException("HTTP method must be string");
        }

        if (empty($method)) {
            throw new \InvalidArgumentException("method cannot be empty");
        }

        $this->request['method'] = $method;
        return $this;
    }

    public function Path(){
        return $this->request['path'];
    }

    public function RequestPath($path)
    {
        if (!is_string($path)) {
            throw new \InvalidArgumentException("path must be string");
        }

        if ($path[0] != "/") {
            throw new \InvalidArgumentException("path must start with a forward slash");
        }

        $this->request['path'] = $path;
        return $this;
    }

    public function Query(){
        return $this->request['query'];
    }

    public function RequestQuery($query)
    {
        $this->request['query'] = $query;
        return $this;
    }

    public function Headers(){
        return $this->request['headers'];
    }

    public function RequestHeaders($headers)
    {
        if(!is_array($headers)){
            throw new \InvalidArgumentException("Headers must be an associative array.");
        }

        $this->request['headers'] = $headers;
        return $this;
    }

}
