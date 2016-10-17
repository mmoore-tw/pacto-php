<?php

namespace Pact\Phpacto\Builder;

/**
 * Class PactBuilder.
 */
class PactBuilder implements PactBuilderInterface
{
    private $provider = [];
    private $consumer = [];
    private $interactions = [];
    private $metadata = [];

    /**
     * Get the provider name.
     *
     * @return mixed
     */
    public function ProviderName()
    {
        return $this->provider['name'];
    }

    /**
     * Get the consumer name.
     *
     * @return mixed
     */
    public function ConsumerName()
    {
        return $this->consumer['name'];
    }

    /**
     * Set the consumer name.
     *
     * @param string $consumerName name of entity making calls to provider
     *
     * @return PactBuilder
     */
    public function ServiceConsumer($consumerName)
    {
        if (!is_string($consumerName) || $consumerName == '') {
            throw new \InvalidArgumentException(sprintf(
                'Invalid value/type for consumer name. Cannot be %s',
                gettype($consumerName)
            ));
        }

        $this->consumer['name'] = $consumerName;

        return $this;
    }

    /**
     * Set the provider name.
     *
     * @param string $providerName
     *
     * @return PactBuilder
     */
    public function HasPactWith($providerName)
    {
        if (!is_string($providerName) || $providerName == '') {
            throw new \InvalidArgumentException(
                    sprintf('Invalid value/type for consumer name. Cannot be %s', gettype($providerName))
            );
        }

        $this->provider['name'] = $providerName;

        return $this;
    }

    /**
     * Add Metadata to the contract.
     *
     * @param $newMetadata
     *
     * @return $this
     */
    public function AddMetadata($newMetadata)
    {
        if (!isset($this->metadata)) {
            $this->metadata = $newMetadata;
        } else {
            $this->metadata += $newMetadata;
        }

        return $this;
    }

    /**
     * @param string $key target key to removed from dictionary
     *
     * @return PactBuilder
     */
    public function RemoveMetadata($key)
    {
        if (isset($this->metadata[$key])) {
            unset($this->metadata[$key]);
        } else {
            throw new \InvalidArgumentException('Key not found in metadata section');
        }

        return $this;
    }

    /**
     * Get Metadata for contract.
     *
     * @return mixed
     */
    public function Metadata()
    {
        return $this->metadata;
    }

    /**
     *  Construct the contract.
     *
     * @return string JSON string of the contract
     */
    public function Build()
    {
        return json_encode(get_object_vars($this), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }

    /**
     * @param PactInteraction $newInteraction
     *
     * @return PactBuilder
     */
    public function AddInteraction(PactInteraction $newInteraction)
    {
        if (!isset($this->interactions)) {
            $this->interactions = [$newInteraction->ToArray()];
        } else {
            array_push($this->interactions, $newInteraction->ToArray());
        }

        return $this;
    }
}
