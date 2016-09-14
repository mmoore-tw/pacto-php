<?php

namespace Pact\Phpacto\Builder;

/**
 * Interface PactBuilderInterface
 * @package Pact\Phpacto\Builder
 */
interface PactBuilderInterface {
    
/**
     * Get the provider name
     * @return mixed
     */
    public function ProviderName();

    /**
     * Get the consumer name
     * @return mixed
     */
    public function ConsumerName();

    /**
     * Set the consumer name
     * @param string $consumerName name of entity making calls to provider
     * @return PactBuilder
     */
    public function ServiceConsumer($consumerName);

    /**
     * Set the provider name
     * @param string $providerName
     * @return PactBuilder
     */
    public function HasPactWith($providerName);

    /**
     * Add Metadata to the contract
     * @param $newMetadata
     * @return $this
     */
    public function AddMetadata($newMetadata);

    /**
     * @param string $key target key to removed from dictionary
     * @return PactBuilder
     */
    public function RemoveMetadata($key);

    /**
     * Get Metadata for contract
     * @return mixed
     */
    public function Metadata();

    /**
     *  Construct the contract
     * @return string JSON string of the contract
     */
    public function Build();

    /**
     * @param PactInteraction $newInteraction
     * @return PactBuilder
     */
    public function AddInteraction(PactInteraction $newInteraction);


}

