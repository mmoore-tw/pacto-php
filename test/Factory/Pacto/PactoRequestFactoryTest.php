<?php

namespace Pact\Phpacto\Factory\Pacto;

use Pact\Phpacto\Fixture;

class PactoRequestFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testGetShouldReturnAPsr7Request()
    {
        $factoryRequest = new PactoRequestFactory();

        $request = $factoryRequest->from($this->getGetRequestArray());

        $this->assertEquals('get', $request->getMethod());
        $this->assertEquals('/alligators/Mary', $request->getUri());
    }

    public function testPostShouldReturnAPsr7Request()
    {
        $factoryRequest = new PactoRequestFactory();

        $request = $factoryRequest->from($this->getPostRequestArray());

        $this->assertEquals('post', $request->getMethod());
        $this->assertEquals('/alligators', $request->getUri());
        $this->assertEquals('{"name":"ally","color":"green"}', $request->getBody()->__toString());
    }

    private function getGetRequestArray()
    {
        $content = json_decode(Fixture::load('hello_world.json'), true);
        return $content['interactions'][0]['request'];
    }

    private function getPostRequestArray()
    {
        $content = json_decode(Fixture::load('hello_world.json'), true);
        return $content['interactions'][3]['request'];
    }
}
