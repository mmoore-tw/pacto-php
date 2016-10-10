<?php

namespace Pact\Phpacto\Diff;

class Diff
{
    /**
     * @var Mismatch[]
     */
    private $mismatches;

    public function __construct($mismatches = [])
    {
        $this->mismatches = $mismatches;
    }

    public function add(Mismatch $mismatch)
    {
        $this->mismatches[] = $mismatch;
    }

    public function hasMismatches()
    {
        return count($this->mismatches) > 0;
    }

    /**
     * @return Mismatch[]
     */
    public function getMismatches()
    {
        return $this->mismatches;
    }

    /**
     * @return Diff
     */
    public static function merge()
    {
        $mismatches = [];

        /** @var Diff $diff */
        foreach (func_get_args() as $diff) {
            $mismatches[] = $diff->getMismatches();
        }

        return new self(call_user_func_array('array_merge', $mismatches));
    }
}
