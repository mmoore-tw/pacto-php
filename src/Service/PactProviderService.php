<?php

namespace Pact\Phpacto\Service;

use Pact\Phpacto\Builder\PactBuilder;
use Pact\Phpacto\Builder\PactInteraction;

use Slim\App;
use Slim\Http\Request;
use Slim\Http\Body;
use Slim\Http\Response;
use Slim\Http\Environment;
use Slim\Http\Uri;
use Slim\Http\Headers;


define("PACT_SPEC_VERSION", "2.0.0");
define("PACTO_PHP_VERSION", "0.1.5");

/**
 * Mock Service that implements a local web server to which calls can be directed
 */
class PactProviderService
{

    private $contractFolder;
    private $uri;
    private $pactBuilder;
    private $interaction;


    public function __construct($contractFolder, $uri = "http://127.0.0.1:8880")
    {
        $this->contractFolder = $contractFolder;
        $this->uri = $uri;

        $this->pactBuilder = new PactBuilder();
        $this->pactBuilder->AddMetadata(
                array(
                        "pact-specification" => array("version" => PACT_SPEC_VERSION),
                        "pact-php" => array("version" => PACTO_PHP_VERSION)
                )
        );
    }

    public function ServiceConsumer($consumerName)
    {
        $this->pactBuilder->ServiceConsumer($consumerName);
        return $this;
    }

    public function HasPactWith($providerName)
    {
        $this->pactBuilder->HasPactWith($providerName);
        return $this;
    }

    public function Uri()
    {
        return $this->uri;
    }

    public function ContractFolder()
    {
        return $this->contractFolder;
    }

    public function Given($providerState)
    {
        if (is_null($this->interaction)) {
            $this->interaction = new PactInteraction();
        }

        $this->interaction->ProviderState($providerState);
        return $this;
    }

    public function With(array $request)
    {
        $this->interaction->SetRequest($request);
        return $this;
    }

    public function UponReceiving($description)
    {
        $this->interaction->Description($description);
        return $this;
    }

    public function WillRespond(array $response)
    {
        $this->interaction->SetResponse($response);
        $this->pactBuilder->AddInteraction($this->interaction);
    }

    public function Start()
    {
        // create the app with the appropriate route
        $app = new App();
        $int = $this->interaction;
        switch ($int->Method()) {
            case "get":
                $app->get(
                        $int->Path(),
                        function (Request $req, Response $res) use ($int) {

                            $res->withJson($int->Body(RESPONSE), $int->Status());

                            foreach ($int->Headers(RESPONSE) as $key => $value) {
                                $res->withHeader($key, $value);
                            }

                            return $res;
                        }
                );
                break;
            case "post":
                break;
            default:
        }

        $uri = Uri::createFromString(sprintf("%s%s", $this->uri, $this->interaction->Path()));

        // setup the request and make the call
        $env = Environment::mock(
                [
                        'REQUEST_URI' => (string)$uri,
                        'SERVER_NAME' => $uri->getHost(),
                        'SERVER_PORT' => $uri->getPort(),
                        'HTTP_HOST' => $uri->getHost(),
                        'REMOTE_ADDR' => $uri->getHost(),
                        'REQUEST_METHOD' => $this->interaction->Method(),
                        'HTTP_USER_AGENT' => sprintf('Pacto-Php %s', PACTO_PHP_VERSION)
                ]
        );


        $headers = new Headers();
        foreach ($this->interaction->Headers(REQUEST) as $key => $value) {
            $headers->add($key, $value);
        }

        $cookies = [];
        $serverParams = $env->all();


        $body = new Body(fopen('php://temp', 'r+'));
        if ($this->interaction->Method() != "get") {
            $body->write(json_encode($this->interaction->Body(REQUEST)));
        }

        $req = new Request($this->interaction->Method(), $uri, $headers, $cookies, $serverParams, $body);
        $res = new Response();

        // Invoke app
        $resOut = $app($req, $res);
        return $resOut;
    }

    public function Stop()
    {
        // reset the interaction
        unset($this->interaction);
    }

    public function WriteContract($filename = "consumer-provider.json")
    {
        $pact = $this->pactBuilder->Build();

        $filename = !is_null($this->pactBuilder) ? sprintf(
                "%s/%s-%s.json",
                $this->contractFolder,
                $this->pactBuilder->ConsumerName(),
                $this->pactBuilder->ProviderName()
        ) : $filename;

        if (!is_dir($this->contractFolder)) {
            mkdir($this->contractFolder, 0777, true);
        }

        file_put_contents($filename, $pact);
    }


}
