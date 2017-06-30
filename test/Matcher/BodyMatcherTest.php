<?php

namespace Pact\Phpacto\Matcher;

use Zend\Diactoros\Request;
use Zend\Diactoros\Stream;

class BodyMatcherTest extends \PHPUnit_Framework_TestCase
{
    /** @var  BodyMatcher */
    private $bodyMatcher;

    public function setUp()
    {
        $this->bodyMatcher = new BodyMatcher();
    }

    public function testItShouldReturnsNoDiffIfBodiesAreEqualsStrings()
    {
        $stream = new Stream('php://memory', 'w');
        $stream->write('String');

        $expected = (new Request())
            ->withBody($stream);

        $streamActual = new Stream('php://memory', 'w');
        $streamActual->write('String');

        $actual = (new Request())
            ->withBody($streamActual);

        $diff = $this->bodyMatcher->match($expected, $actual);
        $this->assertCount(0, $diff->getMismatches());
    }

    public function testItShouldReturnsDiffIfBodiesAreDifferentStrings()
    {
        $stream = new Stream('php://memory', 'w');
        $stream->write(json_encode('String'));

        $expected = (new Request())
            ->withBody($stream);

        $streamActual = new Stream('php://memory', 'w');
        $streamActual->write(json_encode('Another string'));

        $actual = (new Request())
            ->withBody($streamActual);

        $diff = $this->bodyMatcher->match($expected, $actual);
        $this->assertCount(1, $diff->getMismatches());

        $this->assertEquals('Body', $diff->getMismatches()[0]->getLocation());
    }

    public function testCompareStringWithArray()
    {
        $stream = new Stream('php://memory', 'w');
        $stream->write(json_encode('String'));

        $expected = (new Request())
            ->withBody($stream);

        $streamActual = new Stream('php://memory', 'w');
        $streamActual->write(json_encode(['A' => 1]));

        $actual = (new Request())
            ->withBody($streamActual);

        $diff = $this->bodyMatcher->match($expected, $actual);
        $this->assertCount(1, $diff->getMismatches());

        $this->assertEquals('Body', $diff->getMismatches()[0]->getLocation());
    }

    public function testCompareArrayWithString()
    {
        $stream = new Stream('php://memory', 'w');
        $stream->write(json_encode(['A' => 1]));

        $expected = (new Request())
            ->withBody($stream);

        $streamActual = new Stream('php://memory', 'w');
        $streamActual->write(json_encode('String'));

        $actual = (new Request())
            ->withBody($streamActual);

        $diff = $this->bodyMatcher->match($expected, $actual);
        $this->assertCount(1, $diff->getMismatches());

        $this->assertEquals('Body', $diff->getMismatches()[0]->getLocation());
    }

    public function testItShouldReturnsNoDiffIfBodiesAreEqualArrays()
    {
        $stream = new Stream('php://memory', 'w');
        $stream->write(json_encode(['A' => 1]));

        $expected = (new Request())
            ->withBody($stream);

        $streamActual = new Stream('php://memory', 'w');
        $streamActual->write(json_encode(['A' => 1]));

        $actual = (new Request())
            ->withBody($streamActual);

        $diff = $this->bodyMatcher->match($expected, $actual);
        $this->assertCount(0, $diff->getMismatches());
    }

    public function testItShouldReturnsDiffIfBodiesAreDifferentArrays()
    {
        $stream = new Stream('php://memory', 'w');
        $stream->write(json_encode(['A' => 1, 'B' => '2']));

        $expected = (new Request())
            ->withBody($stream);

        $streamActual = new Stream('php://memory', 'w');
        $streamActual->write(json_encode(['A' => 1]));

        $actual = (new Request())
            ->withBody($streamActual);

        $diff = $this->bodyMatcher->match($expected, $actual);
        $this->assertCount(1, $diff->getMismatches());

        $this->assertEquals('Body => "B"', $diff->getMismatches()[0]->getLocation());
    }

    public function testWithCollectionOfObjectsInBody()
    {
        $stream = new Stream('php://memory', 'w');
        $stream->write(json_encode([['A' => 1]]));

        $expected = (new Request())
            ->withBody($stream);

        $streamActual = new Stream('php://memory', 'w');
        $streamActual->write(json_encode([[]]));

        $actual = (new Request())
            ->withBody($streamActual);

        $diff = $this->bodyMatcher->match($expected, $actual);
        $this->assertCount(1, $diff->getMismatches());

        $this->assertEquals('Body => 0 => "A"', $diff->getMismatches()[0]->getLocation());
    }

    public function testWithNestedObjects()
    {
        $stream = new Stream('php://memory', 'w');
        $stream->write(json_encode(['level1' => ['level2' => ['A']]]));

        $expected = (new Request())
            ->withBody($stream);

        $streamActual = new Stream('php://memory', 'w');
        $streamActual->write(json_encode(['level1' => ['level2' => ['B']]]));

        $actual = (new Request())
            ->withBody($streamActual);

        $diff = $this->bodyMatcher->match($expected, $actual);
        $this->assertCount(1, $diff->getMismatches());

        $this->assertEquals('Body => "level1" => "level2" => 0', $diff->getMismatches()[0]->getLocation());
    }

    public function testItShouldReturnNoDiffIfExpectedAndActualEmpty()
    {
        $stream = new Stream('php://memory', 'w');
        $stream->write('{}');

        $expected = (new Request())
            ->withBody($stream);

        $streamActual = new Stream('php://memory', 'w');
        $streamActual->write('');

        $actual = (new Request())
            ->withBody($streamActual);

        $diff = $this->bodyMatcher->match($expected, $actual);
        $this->assertCount(0, $diff->getMismatches());
    }

    public function testItReturnDiffWhenExpectedBodyShouldBeValidJson()
    {
        $stream = new Stream('php://memory', 'w');
        $stream->write('Not a json');

        $expected = (new Request())
            ->withHeader('Content-Type', 'application/json')
            ->withBody($stream);

        $streamActual = new Stream('php://memory', 'w');
        $streamActual->write('[]');

        $actual = (new Request())
            ->withBody($streamActual);

        $diff = $this->bodyMatcher->match($expected, $actual);
        $this->assertCount(1, $diff->getMismatches());
    }

    public function testItReturnDiffWhenActualBodyShouldBeValidJson()
    {
        $stream = new Stream('php://memory', 'w');
        $stream->write(json_encode('JSON'));

        $expected = (new Request())
            ->withHeader('Content-Type', 'application/json')
            ->withBody($stream);

        $streamActual = new Stream('php://memory', 'w');
        $streamActual->write('Not a json');

        $actual = (new Request())
            ->withBody($streamActual);

        $diff = $this->bodyMatcher->match($expected, $actual);
        $this->assertCount(1, $diff->getMismatches());
    }
}
