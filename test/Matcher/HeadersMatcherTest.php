<?php

namespace Pact\Phpacto\Matcher;

use Pact\Phpacto\Diff\Mismatch;
use Pact\Phpacto\Diff\MismatchType;
use Zend\Diactoros\Request;

class HeadersMatcherTest extends \PHPUnit_Framework_TestCase
{
    /** @var  HeadersMatcher */
    private $headersMatcher;

    public function setUp()
    {
        $this->headersMatcher = new HeadersMatcher();
    }
    public function testItShouldReturnsNoDiffIfHeadersAreEquals()
    {
        $expected = (new Request())
            ->withHeader('key1', '124')
            ->withHeader('key2', ['ciao', 'ciao']);

        $actual = (new Request())
            ->withHeader('key1', '124')
            ->withHeader('key2', '124')
            ->withHeader('key2', ['ciao', 'ciao']);

        $diff = $this->headersMatcher->match($expected, $actual);
        $this->assertCount(0, $diff->getMismatches());
    }

    public function testItShouldReturnsHeaderKeysMismatches()
    {
        $expected = (new Request())
            ->withHeader('key1', '124')
            ->withHeader('key2', ['ciao', 'ciao']);

        $actual = (new Request())
            ->withHeader('key1', '124');

        $diff = $this->headersMatcher->match($expected, $actual);
        $this->assertCount(1, $diff->getMismatches());
        $this->assertEquals(
            $diff->getMismatches()[0],
            new Mismatch(HeadersMatcher::LOCATION, MismatchType::KEY_NOT_FOUND, ['key2'])
        );
    }

    /**
     * @group it
     */
    public function testItShouldReturnsHeaderValuesMismatches()
    {
        $expected = (new Request())
            ->withHeader('key1', 'pippo')
            ->withHeader('key2', ['ciao', 'ciao']);

        $actual = (new Request())
            ->withHeader('key1', '12s4')
            ->withHeader('key2', ['ciao kikko', 'ciao']);

        $diff = $this->headersMatcher->match($expected, $actual);
        $this->assertCount(2, $diff->getMismatches());

        $this->assertEquals(
            $diff->getMismatches()[0],
            new Mismatch(HeadersMatcher::LOCATION, MismatchType::UNEQUAL, ['pippo', '12s4'])
        );

        $this->assertEquals(
            $diff->getMismatches()[1],
            new Mismatch(HeadersMatcher::LOCATION, MismatchType::UNEQUAL, ['ciao,ciao', 'ciao kikko,ciao'])
        );
    }
}
