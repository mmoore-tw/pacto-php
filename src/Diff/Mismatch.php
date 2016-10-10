<?php

namespace Pact\Phpacto\Diff;

class Mismatch
{
    private $type;
    private $location;
    private $message;

    public function __construct($location, $type, array $args = [])
    {
        $this->type = $type;
        $this->location = $location;

        foreach ($args as &$arg) {
            if (is_string($arg)) {
                $arg = '"'.$arg.'"';
            }
        }

        $this->message = vsprintf($type, $args);
    }

    public function getType()
    {
        return $this->type;
    }

    public function getLocation()
    {
        return $this->location;
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function __toString()
    {
        return $this->getMessage();
    }
}
