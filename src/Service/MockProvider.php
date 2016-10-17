<?php

namespace Pact\Phpacto\Service;

use Slim\Slim;

/**
 * Class MockProvider.
 */
class MockProvider extends Slim
{
    public function __construct(array $userSettings = [])
    {
        parent::__construct($userSettings);
    }

    public function invoke()
    {
        $this->middleware[0]->call();
        $this->response()->finalize();

        return $this->response();
    }
}
