<?php

use Pact\Phpacto\Builder\PactBuilder;

/**
 * Class PactoPactBuilderTest
 * @package Pact\Phpacto\Test
 */
class PactoPactBuilderTest extends \PHPUnit_Framework_TestCase
{

    public function setup()
    {
    }

    public function testCanSetProviderName()
    {
        $providerName = "Some name";
        $pb = new PactBuilder();
        $pb->HasPactWith($providerName);

        $this->assertEquals($pb->ProviderName(), $providerName, "The provider name was not set properly");
    }

    public function testCanSetConsumerName()
    {
        $consumerName = "Some name";
        $pb = new PactBuilder();
        $pb->ServiceConsumer($consumerName);

        $this->assertEquals($pb->ConsumerName(), $consumerName, "The provider name was not set properly");
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testConsumerNameCannotBeEmpty()
    {
        $consumerName = "";
        $pb = new PactBuilder();
        $pb->ServiceConsumer($consumerName);
    }


    /**
     * @expectedException InvalidArgumentException
     */
    public function testProviderNameCannotBeEmpty()
    {
        $providerName = "";
        $pb = new PactBuilder();
        $pb->HasPactWith($providerName);
    }
}
