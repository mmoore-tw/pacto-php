<?php

namespace Pact\Phpacto\Factory\Pacto;

use Pact\Phpacto\Factory\ContractFactoryInterface;
use Pact\Phpacto\Factory\PactoRequestFactoryInterface;
use Pact\Phpacto\Factory\PactoResponseFactoryInterface;
use Pact\Phpacto\Pact\Pact;
use Pact\Phpacto\Pact\PactList;

class PactListFactory implements ContractFactoryInterface
{
    private $pactoRequestFactory;
    private $pactoResponseFactory;

    public function __construct(
        PactoRequestFactoryInterface $pactoRequestFactory,
        PactoResponseFactoryInterface $pactoResponseFactory
    ) {
        $this->pactoRequestFactory = $pactoRequestFactory;
        $this->pactoResponseFactory = $pactoResponseFactory;
    }

    public function from($jsonDescription)
    {
        $jsonDescription = json_decode($jsonDescription, true);
        $provider = $jsonDescription['provider']['name'];
        $consumer = $jsonDescription['consumer']['name'];

        $pactList = new PactList($provider, $consumer);

        foreach ($jsonDescription['interactions'] as $interaction) {
            $pact = new Pact(
                $this->pactoRequestFactory->from($interaction['request']),
                $this->pactoResponseFactory->from($interaction['response']),
                $interaction['description'],
                $interaction['providerState']
            );

            $pactList->add($pact);
        }

        return $pactList;
    }

    public static function getPactoListFactory()
    {
        return new self(
            new PactoRequestFactory(),
            new PactoResponseFactory()
        );
    }
}
