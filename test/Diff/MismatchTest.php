<?php

namespace Pact\Phpacto\Diff;

class MismatchTest extends \PHPUnit_Framework_TestCase
{
    public function testItPrintsMismatch()
    {
        $mismatch = new Mismatch('location', MismatchType::UNEQUAL, ['a', 2]);
        $this->assertEquals('Unequal expected "a" received 2', $mismatch->getMessage());
    }
}
