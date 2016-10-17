<?php

namespace Pact\Phpacto\Test;

use Pact\Phpacto\Diff\Diff;
use Pact\Phpacto\Matcher\BodyMatcher;
use Pact\Phpacto\Matcher\HeadersMatcher;
use Pact\Phpacto\Matcher\StatusCodeMatcher;
use Pact\Phpacto\Pact\Pact;
use Pact\Phpacto\Test\Output\MismatchDiffOutput;
use Psr\Http\Message\ResponseInterface;

class PactoInstanceTest extends \PHPUnit_Framework_TestCase
{
    private $onSetup;
    private $onTearDown;
    private $makeRequest;
    private $pact;
    private $strict;

    public function __construct($name, \Closure $onTearDown, \Closure $onSetUp, \Closure $makeRequest, Pact $p, $strict = false)
    {
        parent::__construct($name);

        $this->onTearDown = $onTearDown;
        $this->onSetup = $onSetUp;
        $this->makeRequest = $makeRequest;
        $this->pact = $p;
        $this->strict = $strict;
    }

    public function setUp()
    {
        parent::setUp();
        call_user_func($this->onSetup, $this->pact->getProviderState());
    }

    public function testItHonorContract()
    {
        $response = call_user_func($this->makeRequest, $this->pact->getRequest());
        $this->assertResponse($this->pact, $response);
    }

    public function tearDown()
    {
        parent::tearDown();
        call_user_func($this->onTearDown, $this->pact->getProviderState());
    }

    public function assertResponse(Pact $p, ResponseInterface $r)
    {
        $statusCodeMatcher = new StatusCodeMatcher();
        $statusCodeDiff = $statusCodeMatcher->match($p->getResponse(), $r);

        $headersMatcher = new HeadersMatcher();
        $headersDiff = $headersMatcher->match($p->getResponse(), $r);

        $bodyMatcher = new BodyMatcher();
        $bodyDiff = $bodyMatcher->match($p->getResponse(), $r);

        $diffs = Diff::merge($statusCodeDiff, $headersDiff, $bodyDiff);

        if ($diffs->hasMismatches()) {
            $output = new MismatchDiffOutput(true);

            $this->fail(
                $output->getOutputFor($diffs, $p)
            );
        }
    }
}
