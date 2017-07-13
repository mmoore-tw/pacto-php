<?php

namespace Pact\Phpacto\Matcher;

use Pact\Phpacto\Diff\Diff;
use Pact\Phpacto\Diff\Mismatch;
use Pact\Phpacto\Diff\MismatchType;
use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\StreamInterface;

class BodyMatcher
{
    const LOCATION = 'Body';

    public function match(MessageInterface $expected, MessageInterface $actual)
    {
        $isJsonExpected = true;

        $contentType = $expected->getHeaderLine('Content-Type');
        if (strpos($contentType, 'application/json') === false && strpos($contentType, 'application/json') === false) {
            $isJsonExpected = false;
        }

        return $this->compareBodyResponse($expected->getBody(), $actual->getBody(), $isJsonExpected);
    }

    private function compareBodyResponse(StreamInterface $expected, StreamInterface $actual, $isJsonExpected = false)
    {
        $diff = new Diff();

        $expected->rewind();
        $actual->rewind();

        $expectedBody = $expected->getContents();
        $actualBody = $actual->getContents();

        if (($json = json_decode($expectedBody, true)) !== null) {
            $expectedBody = $json;
        } elseif ($isJsonExpected) {
            $diff->add(
                new Mismatch(
                    self::LOCATION,
                    MismatchType::JSON_CONTENT_EXPECTED
                )
            );

            return $diff;
        }

        if (($json = json_decode($actualBody, true)) !== null) {
            $actualBody = $json;
        } elseif ($isJsonExpected) {
            $diff->add(
                new Mismatch(
                    self::LOCATION,
                    MismatchType::JSON_CONTENT_EXPECTED
                )
            );

            return $diff;
        }

        if ($expectedBody) {
            $this->getDiffRecursively(self::LOCATION, $expectedBody, $actualBody, $diff);
        } else if ($actualBody) {
            $diff->add(
                new Mismatch(
                    self::LOCATION,
                    MismatchType::NIL_VS_NOT_NULL
                )
            );
        }

        return $diff;
    }

    private function getDiffRecursively($location, $expectedBody, $actualBody, Diff $diff)
    {
        if (is_array($expectedBody) && is_array($actualBody)) {
            foreach ($expectedBody as $key => $value) {
                if (is_string($key)) {
                    $currentLocation = $location.' => "'.$key.'"';
                } else {
                    $currentLocation = $location.' => '.$key;
                }

                if (key_exists($key, $actualBody)) {
                    if (is_array($value)) {
                        $this->getDiffRecursively($currentLocation, $value, $actualBody[$key], $diff);
                    } elseif ($actualBody[$key] !== $expectedBody[$key]) {
                        $diff->add(
                            new Mismatch(
                                $currentLocation,
                                MismatchType::UNEQUAL,
                                [$expectedBody[$key], $actualBody[$key]]
                            )
                        );
                    }
                } else {
                    $diff->add(
                        new Mismatch(
                            $currentLocation,
                            MismatchType::KEY_NOT_FOUND,
                            [$key]
                        )
                    );
                }
            }
        } elseif (gettype($expectedBody) !== gettype($actualBody)) {
            $diff->add(
                new Mismatch(
                    $location,
                    MismatchType::TYPE_MISMATCH,
                    [gettype($expectedBody), gettype($actualBody)]
                )
            );
        } elseif ($expectedBody !== $actualBody) {
            $diff->add(
                new Mismatch(
                    $location,
                    MismatchType::UNEQUAL,
                    [$expectedBody, $actualBody]
                )
            );
        }

        return $diff;
    }
}
