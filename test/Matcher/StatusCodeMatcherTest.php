<?php

namespace Pact\Phpacto\Matcher;

use Zend\Diactoros\Response;

class StatusCodeMatcherTest extends \PHPUnit_Framework_TestCase
{
    /** @var  StatusCodeMatcher */
    private $statusCodeMatcher;

    public function setUp()
    {
        $this->statusCodeMatcher = new StatusCodeMatcher();
    }

    public function testItShouldReturnsNoDiffIfStatusCodesAreEquals()
    {
        $expected = (new Response())
            ->withStatus(200);

        $actual = (new Response())
            ->withStatus(200);

        $diff = $this->statusCodeMatcher->match($expected, $actual);
        $this->assertCount(0, $diff->getMismatches());
    }

    public function testItShouldReturnsDiffIfStatusCodesMismatches()
    {
        $expected = (new Response())
            ->withStatus(200);

        $actual = (new Response())
            ->withStatus(201);

        $diff = $this->statusCodeMatcher->match($expected, $actual);
        $this->assertCount(1, $diff->getMismatches());
    }
}
