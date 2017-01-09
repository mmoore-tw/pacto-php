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

        $bodyStream = new Stream('php://memory', 'w');
        $bodyStream->write(json_encode($body));

        $request = new Request(
            $requestArray['path'],
            $requestArray['method'],
            $bodyStream,
            isset($requestArray['headers']) ? $requestArray['headers'] : []
        );

        return $request;
    }
}
