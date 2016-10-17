<?php

namespace Pact\Phpacto\Matcher;

use Pact\Phpacto\Diff\Diff;
use Pact\Phpacto\Diff\Mismatch;
use Pact\Phpacto\Diff\MismatchType;
use Psr\Http\Message\MessageInterface;

class HeadersMatcher
{
    const LOCATION = 'Header';

    public function match(MessageInterface $expected, MessageInterface $actual)
    {
        $expectedHeaders = $this->normalizeKeys($expected->getHeaders());
        $actualHeaders = $this->normalizeKeys($actual->getHeaders());

        return $this->compareHeaders($expectedHeaders, $actualHeaders);
    }

    private function normalizeKeys($headers)
    {
        return array_change_key_case($headers, CASE_LOWER);
    }

    private function compareHeaders($expectedHeaders, $actualHeaders)
    {
        $diff = new Diff();
        $this->diffInHeadersKeys($diff, $expectedHeaders, $actualHeaders);
        $this->diffInHeadersValues($diff, $expectedHeaders, $actualHeaders);

        return $diff;
    }

    private function diffInHeadersKeys(Diff $diff, $expectedHeaders, $actualHeaders)
    {
        $keys = array_diff(array_keys($expectedHeaders), array_keys($actualHeaders));

        foreach ($keys as $key) {
            $diff->add(
                new Mismatch(
                    self::LOCATION,
                    MismatchType::KEY_NOT_FOUND,
                    [$key]
                )
            );
        }
    }

    private function diffInHeadersValues(Diff $diff, $expectedHeaders, $actualHeaders)
    {
        foreach ($expectedHeaders as $key => $expectedValues) {
            if (key_exists($key, $actualHeaders)) {
                $actualValues = $actualHeaders[$key];
                $values = array_diff(array_values($expectedValues), array_values($actualValues));
                $reverseValues = array_diff(array_values($actualValues), array_values($expectedValues));

                if (count($values) > 0 || count($reverseValues) > 0) {
                    $diff->add(
                        new Mismatch(
                            self::LOCATION,
                            MismatchType::UNEQUAL,
                            [implode(',', $expectedValues), implode(',', $actualValues)]
                        )
                    );
                }
            }
        }
    }
}
