<?php

namespace Pact\Phpacto\Factory;

use Psr\Http\Message\ResponseInterface;

interface PactoResponseFactoryInterface
{
    /**
     * @param $responseArray
     * @return ResponseInterface
     */
    public function from($responseArray);
}
