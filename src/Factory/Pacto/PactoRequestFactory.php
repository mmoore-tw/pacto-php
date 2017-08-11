<?php

namespace Pact\Phpacto\Factory\Pacto;

use Pact\Phpacto\Factory\PactoRequestFactoryInterface;
use Zend\Diactoros\Request;
use Zend\Diactoros\Stream;

class PactoRequestFactory implements PactoRequestFactoryInterface
{
    public function from($requestArray)
    {
        $body = isset($requestArray['body']) ? $requestArray['body'] : '';

        $uriString = $requestArray['path'];
        if (isset($requestArray['query'])) {
            $uriString .= '?' . $requestArray['query'];
        }

        $bodyStream = new Stream('php://memory', 'w');
        $bodyStream->write(json_encode($body));

        // build the PSR-7 request 
	$request = new Request(
            $uriString,
            $requestArray['method'],
            $bodyStream,
            isset($requestArray['headers']) ? $requestArray['headers'] : []
        );

        return $request;
    }
}
