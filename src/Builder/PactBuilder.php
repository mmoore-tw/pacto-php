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
        return $this->provider["name"];
    }


    public function ConsumerName()
    {
        return $this->consumer["name"];
    }


    public function ServiceConsumer($consumerName)
    {
        if (!is_string($consumerName) || $consumerName == "") {
            throw new \InvalidArgumentException(
                    sprintf("Invalid value/type for consumer name. Cannot be %s", gettype($consumerName))
            );
        }

        $this->consumer['name'] = $consumerName;
        return $this;
    }


    public function HasPactWith($providerName)
    {
        if (!is_string($providerName) || $providerName == "") {
            throw new \InvalidArgumentException(
                    sprintf("Invalid value/type for consumer name. Cannot be %s", gettype($providerName))
            );
        }

        $this->provider['name'] = $providerName;
        return $this;
    }


    public function AddMetadata($newMetadata)
    {
        // first value entered
        if (!isset($this->metadata)) {
            $this->metadata = $newMetadata;
        } else {
            $this->metadata += $newMetadata;
        }

        return $this;
    }

    public function RemoveMetadata($key)
    {
        if (isset($this->metadata[$key])) {
            unset($this->metadata[$key]);
        } else {
            throw new \InvalidArgumentException("Key not found in metadata section");
        }

        return $this;
    }

    public function Metadata()
    {
        return $this->metadata;
    }

    public function Build($fileName)
    {
        $pactFile = json_encode(get_object_vars($this), JSON_PRETTY_PRINT);
        return $pactFile;
    }

}
