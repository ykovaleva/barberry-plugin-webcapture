<?php

namespace Barberry\Plugin\Webcapture;

class PhantomJsException extends \Exception
{
    public function __construct($message)
    {
        parent::__construct('PhantomJs error: ' . $message, 500);
    }
}
