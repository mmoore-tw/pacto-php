<?php

namespace Pact\Phpacto\Test;

use Pact\Phpacto\Service\MockProvider;
use Pact\Phpacto\Service\PactProviderService;


define("DEFAULT_CONSUMER_CONTRACTS", __DIR__ . "/consumer-contracts");

/**
 * Class PactoPactProviderServiceTest
 */
class PactoPactProviderServiceTest extends \PHPUnit_Framework_TestCase
{

    private static $providerService;
    private $svc;

    /**
     * @beforeClass
     */
    public static function setUpTestFixture()
    {
        if (is_null(self::$providerService)) {
            self::$providerService = new PactProviderService(DEFAULT_CONSUMER_CONTRACTS);
            self::$providerService->ServiceConsumer("monolith")->HasPactWith("Street-Fighter");
        }
    }

    /**
     * @afterClass
     */
    public static function tearDownTestFixture()
    {
        self::$providerService->WriteContract();
    }

    public function setUp()
    {
        if (!is_null(self::$providerService)) {
            $this->svc = self::$providerService;
        }
    }

    public function tearDown()
    {
        $this->svc->Stop();
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
        $this->assertEquals($expectedResponse['status'], $actualResponse->getStatus());
        $this->assertEquals($expectedResponse['headers'], $actualResponse->headers()->all());
    }

}
