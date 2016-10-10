<?php

namespace Pact\Phpacto\Builder;

define('REQUEST', 'REQUEST');
define('RESPONSE', 'RESPONSE');

/**
 * Class PactInteraction.
 */
class PactInteraction
{
    private $description;
    private $providerState;
    private $request;
    private $response;

    public function __construct()
    {
        $this->description = '';
        $this->providerState = '';
        $this->request = ['method' => null, 'path' => null, 'headers' => null, 'query' => '', 'body' => null];
        $this->response = ['status' => 0, 'headers' => null, 'body' => null];
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

    public function Method()
    {
        return $this->request['method'];
    }

    public function RequestMethod($method)
    {
        // check type and if it is a valid HTTP verb
        if (!is_string($method)) {
            throw new \InvalidArgumentException('HTTP method must be string');
        }

        if (empty($method)) {
            throw new \InvalidArgumentException('method cannot be empty');
        }

        $this->request['method'] = strtoupper($method);

        return $this;
    }

    public function Path()
    {
        return $this->request['path'];
    }

    public function RequestPath($path)
    {
        if (!is_string($path)) {
            throw new \InvalidArgumentException('path must be string');
        }

        if ($path[0] != '/') {
            throw new \InvalidArgumentException('path must start with a forward slash');
        }

        $this->request['path'] = $path;

        return $this;
    }

    public function Query()
    {
        return $this->request['query'];
    }

    public function RequestQuery($query)
    {
        $this->request['query'] = $query;

        return $this;
    }

    public function Headers($headerType)
    {
        $header = '';
        switch ($headerType) {
            case REQUEST:
                $header = $this->request['headers'];
                break;
            case RESPONSE:
                $header = $this->response['headers'];
                break;
        }

        return $header;
    }

    public function Body($bodyType)
    {
        $targetBody = '';
        switch ($bodyType) {
            case REQUEST:
                $targetBody = $this->request['body'];
                break;
            case RESPONSE:
                $targetBody = $this->response['body'];
                break;
            default:
                $targetBody = null;
        }

        return $targetBody;
    }

    public function RequestHeaders($headers)
    {
        if (!is_array($headers)) {
            throw new \InvalidArgumentException('Headers must be an associative array.');
        }

        $this->request['headers'] = $headers;

        return $this;
    }

    public function RequestBody($body)
    {
        $this->request['body'] = $body;

        return $this;
    }

    public function Status()
    {
        return $this->response['status'];
    }

    public function ResponseStatus($code)
    {
        if (!is_int($code)) {
            throw new \InvalidArgumentException('Status code must be an integer');
        }
        $this->response['status'] = $code;

        return $this;
    }

    public function ResponseHeaders($headers)
    {
        if (!is_array($headers)) {
            throw new \InvalidArgumentException('Headers must be an associative array.');
        }

        $this->response['headers'] = $headers;

        return $this;
    }

    public function ResponseBody($body)
    {
        $this->response['body'] = $body;

        return $this;
    }

    public function ToArray()
    {
        $temp = get_object_vars($this);

        return $temp;
    }

    public function Request()
    {
        return $this->request;
    }

    public function Response()
    {
        return $this->response;
    }

    public function SetRequest($request)
    {
        $this->request = $request;
    }

    public function SetResponse($response)
    {
        $this->response = $response;
    }
}
