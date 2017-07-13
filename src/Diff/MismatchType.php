<?php

namespace Pact\Phpacto\Diff;

final class MismatchType
{
    const JSON_CONTENT_EXPECTED = 'Should be a valid Json type';
    const TYPE_MISMATCH = 'Type mismatch expected %s received %s';
    const LENGTH_MISMATCH = 'Length mismatch, expected %d received %d';
    const UNEQUAL = 'Unequal expected %s received %s';
    const VALIDITY = 'Validity mismatch';
    const FIELD_UNEXPECTED = 'Unexpected field %s';
    const FIELD_NOT_FOUND = 'Field %s not found';
    const KEY_NOT_FOUND = 'Key %s not found';
    const NIL_VS_NOT_NULL = 'nil vs non-nil mismatch';
    const NON_NIL_FUNCTIONS = 'non-nil functions';
}
