<?php

namespace Pact\Phpacto\Test;

use Pact\Phpacto\Service\PactProviderService;
use Pact\Phpacto\Builder\PactInteraction;

define("DEFAULT_CONSUMER_CONTRACTS", __DIR__ . "/consumer-contracts");

/**
 * Class PactoPactProviderServiceTest
 */
class PactoPactProviderServiceTest extends \PHPUnit_Framework_TestCase
{

    private $svc;

    public function setUp()
    {
        // setup the server lazily
        // TODO: add to setUpBeforeClass instead
        if (is_null($this->svc)) {
            $this->svc = new PactProviderService(DEFAULT_CONSUMER_CONTRACTS);
            $this->svc->ServiceConsumer("monolith")->HasPactWith("Street-Fighter");
        }
    }


    public function testPactoProviderService()
    {
        // Arrange
        $this->svc->Given("some provider state")
                ->UponReceiving("some description of the interaction")
                ->With(
                        array(
                                "method" => "get",
                                "path" => "/some/path",
                                "headers" => array("Accept" => "application/json")
                        )
                )
                ->WillRespond(
                        array(
                                "status" => 200,
                                "headers" => array("Content-Type" => "application/json;charset=utf-8"),
                                "body" => array("name" => "Mary")
                        )
                );

        $this->svc->Start();

        // Act with client
        $ch = curl_init();
        $options = array(
                CURLOPT_URL => "http://127.0.0.1/some/path",
                CURLOPT_HEADER => array(
                    'Content-Type: application/json',
                    'Accept: application/json'
                )
        );
        curl_setopt_array($ch, $options);
        $result = curl_exec($ch);
        curl_close($ch);

        $temp = json_decode($result);

        $this->svc->Stop();

    }

}
