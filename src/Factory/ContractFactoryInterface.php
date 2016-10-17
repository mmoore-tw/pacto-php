<?php

namespace Pact\Phpacto\Factory;

use Pact\Phpacto\Pact\Pact;

interface ContractFactoryInterface
{
    /**
     * @param $jsonDescription
     *
     * @return Pact
     */
    public function from($jsonDescription);
}
