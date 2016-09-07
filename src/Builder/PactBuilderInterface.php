<?php

namespace Pact\Phpacto\Builder;

/**
 * Interface PactBuilderInterface
 * @package Pact\Phpacto\Builder
 */
interface PactBuilderInterface {
    
    public function ServiceConsumer($consumerName);

    public function HasPactWith($providerName);

    public function Build($fileName);
    
}

