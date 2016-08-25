<?php

namespace Pact\Phpacto\Matcher;

use Pact\Phpacto\Diff\Diff;
use Pact\Phpacto\Diff\Mismatch;
use Pact\Phpacto\Diff\MismatchType;
use Psr\Http\Message\ResponseInterface;

class StatusCodeMatcher
{
    const LOCATION = 'Status Code';

    /**
     * @param ResponseInterface $expected
     * @param ResponseInterface $actual
     *
     * @return Diff
     */
    public function match(ResponseInterface $expected, ResponseInterface $actual)
    {
        $diff = new Diff();

        if ($expected->getStatusCode() !== $actual->getStatusCode()) {
            $diff->add(
                new Mismatch(
                    self::LOCATION,
                    MismatchType::UNEQUAL,
                    [$expected->getStatusCode(), $actual->getStatusCode()]
                )
            );
        }

        return $diff;
    }
}
