<?php

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

    }

    public function Description($description){
        $this->description = $description;
        return $this;
    }

    public function ProviderState($state){
        $this->providerState = $$state;
        return $this;
    }

    public function Request(RequestInterface $request){
        return new Exception("Not Implemented Yet.");
    }


}
