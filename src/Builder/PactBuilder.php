<?php

namespace Pact\Phpacto\Builder;

/**
 * Class PactBuilder
 * @package Pact\Phpacto\Builder
 */
class PactBuilder implements PactBuilderInterface
{

    private $provider;
    private $consumer;
    private $interactions;
    private $metadata;

    public function __construct()
    {
        $provider = array();
        $consumer = array();
        $interactions = array();
        $metadata = array();
    }


    public function ProviderName()
    {
        return $this->provider["provider"];
    }

    public function ConsumerName()
    {
        return $this->consumer["consumer"];
    }

    public function ServiceConsumer($consumerName)
    {
        if (!is_string($consumerName) || $consumerName == "") {
            throw new \InvalidArgumentException(
                    sprintf("Invalid value/type for consumer name. Cannot be %s", gettype($consumerName))
            );
        }

        $this->consumer['consumer'] = $consumerName;
        return $this;
    }


    public function HasPactWith($providerName)
    {
        if (!is_string($providerName) || $providerName == "") {
            throw new \InvalidArgumentException(
                    sprintf("Invalid value/type for consumer name. Cannot be %s", gettype($providerName))
            );
        }

        $this->provider['provider'] = $providerName;
        return $this;
    }


    public function Build($fileName)
    {
        $pactFile = json_encode($this);
        return $pactFile;
    }

}
