<?php

namespace Pact\Phpacto\Service;

use Pact\Phpacto\Builder\PactBuilder;
use Pact\Phpacto\Builder\PactInteraction;

use Slim\Slim;
use Slim\Http\Request;

//use Slim\Http\Body;
//use Slim\Http\Response;
use Slim\Environment;
//use Slim\Uri;
use Slim\Http\Headers;
use Pact\Phpacto\Service\App;


define("PACT_SPEC_VERSION", "2.0.0");
define("PACTO_PHP_VERSION", "0.1.4");

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


    /**
     * @return \Slim\Http\Response
     */
    public function Start()
    {
        // create the app with the appropriate route
        $app = new MockProvider();

        $int = $this->interaction;
        $app->map(
                $int->Path(),
                function () use ($int, $app) {
                    $app->response->headers->replace($int->Headers(RESPONSE));
                    echo json_encode($int->Body(RESPONSE));
                }

        )->via("GET", "POST", "PUT", "DELETE", "OPTIONS");

        Environment::mock(
                [
                        'PATH_INFO' => $this->interaction->Path(),
                        'HTTP_USER_AGENT' => sprintf('Pacto-Php %s', PACTO_PHP_VERSION),
                        'USER_AGENT' => sprintf('Pacto-Php %s', PACTO_PHP_VERSION)
                ]
        );

        $response = $app->invoke();
        return $response;
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
