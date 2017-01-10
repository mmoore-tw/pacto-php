<?php

namespace Pact\Phpacto;

class Fixture
{
    public static function load($fixtureName)
    {
        $content = file_get_contents(sprintf(__DIR__.'/fixtures/%s', $fixtureName));

        return $content;
    }
}
