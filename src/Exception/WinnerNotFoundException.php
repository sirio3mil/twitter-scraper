<?php

namespace App\Exception;

use Exception;

class WinnerNotFoundException extends Exception
{
    /**
     * WinnerNotFoundException constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }
}
