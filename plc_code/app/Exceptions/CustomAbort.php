<?php

namespace App\Exceptions;

use Exception;

class CustomAbort extends Exception
{
    public int $status;
    public string $errorMessage;
    public bool $layout;

    public function __construct(int $status, string $errorMessage = "Error", bool $layout = false)
    {
        parent::__construct($errorMessage);
        $this->layout = $layout;
        $this->status = $status;
        $this->errorMessage = $errorMessage;
    }
}
