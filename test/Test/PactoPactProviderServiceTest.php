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

    public $svc;

    /**
     * @beforeClass
     */
    public static function setUpTestFixture()
    {
    }

    /**
     * @afterClass
     */
    public static function tearDownTestFixture()
    {

    }

    public function setUp()
    {
        if (is_null($this->svc)) {
            $this->svc = new PactProviderService(DEFAULT_CONSUMER_CONTRACTS);
            $this->svc->ServiceConsumer("monolith")->HasPactWith("Street-Fighter");
        }
    }

    public function tearDown()
    {
        $this->svc->Stop();
        $this->svc->WriteContract(); //TODO: Need to perform at end of all interactions, not every test
    }


    public function testPactoProviderService()
    {

        $expectedResponse = array(
                "status" => 200,
                "headers" => array("Content-Type" => "application/json;charset=utf-8"),
                "body" => array("name" => "Mary")
        );

        // Arrange
        $this->svc
                ->Given("some provider state")
                ->UponReceiving("some description of the interaction")
                ->With(
                        array(
                                "method" => "get",
                                "path" => "/some/path",
                                "headers" => array("Accept" => "application/json")
                        )
                )
                ->WillRespond($expectedResponse);

        // Act
        $actualResponse = $this->svc->Start();

        // Assert
        $actualResponseBody = json_decode((string)$actualResponse->getBody(), true);
        $this->assertEquals($expectedResponse['body'], $actualResponseBody);
    }

}
