<?php

namespace App\Exceptions;

class InvalidDateRangeException extends \InvalidArgumentException
{
  public function __construct(int $code = 0, ?Throwable $previous = null)
  {
      parent::__construct("From date is greater that until date ", $code, $previous);
  }
}