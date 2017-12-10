<?php

namespace GinoPane\PHPolyglot\Exception;

use Throwable;
use GinoPane\PHPolyglot\API\Response\ApiResponseInterface;

/**
 * Class BadResponseClassException
 */
class BadResponseClassException extends \Exception
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        $message = "Class $message must implement " . ApiResponseInterface::class;

        parent::__construct($message, $code, $previous);
    }
}
