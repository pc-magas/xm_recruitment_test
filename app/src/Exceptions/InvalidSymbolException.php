<?php

namespace App\Exceptions;

class InvalidSymbolException extends \InvalidArgumentException
{
    public function __construct(int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct("Company Symbol is invalid", $code, $previous);
    }
}