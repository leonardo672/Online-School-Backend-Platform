<?php
// app/Exceptions/DuplicateReviewException.php

namespace App\Exceptions;

use Exception;

class DuplicateReviewException extends Exception
{
    protected $message = 'Duplicate review detected.';
}