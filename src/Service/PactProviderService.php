<?php

namespace Pact\Phpacto\Service;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

use Pact\Phpacto\Builder\PactBuilder;
use Pact\Phpacto\Builder\PactInteraction;
use Slim\App;


/**
 * Mock Service that implements a local web server to which calls can be directed
 */
class PactProviderService
{

    private $contractFolder;
    private $uri;
    private $pactBuilder;
    private $app;
    private $interaction;


    public function __construct($contractFolder, $uri = "http://127.0.0.1")
    {
        $this->contractFolder = $contractFolder;
        $this->uri = $uri;

        $this->pactBuilder = new PactBuilder();
        $this->pactBuilder->AddMetadata(
                array(
                        "pact-specification" => array("version" => "2.0.0"),
                        "pact-php" => array("version" => "0.1.2")
                )
        );

        $this->app = new App();
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
        unset($this->interaction);
        return $this;
    }

    public function Start()
    {
        $this->app->get(
                "/some/path",
                function () {
                    return $this->app->response->getBody()->write(json_encode("Hello World"));
                }
        );

        $this->app->run();

    }

    public function Stop()
    {
        // reset the applicaiton
        $this->app = null;
    }

    public function WriteContract($filename = "consumer-provider.json")
    {
        $filename = !is_null($this->pactBuilder) ? sprintf(
                "%s-%s.json",
                $this->pactBuilder->ConsumerName(),
                $this->pactBuilder->ProviderName()
        ) : $filename;
        $pact = $this->pactBuilder->Build(sprintf("%s/%s", $this->contractFolder, $filename));
        file_put_contents($filename, $pact);
    }


}
