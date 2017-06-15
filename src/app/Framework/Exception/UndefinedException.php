<?php

namespace Cocktales\Framework\Exception;

class UndefinedException extends \Exception
{
    public static function field(string $field): UndefinedException
    {
        return new UndefinedException("Field '$field' is undefined");
    }
}
