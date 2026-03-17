<?php
// app/Exceptions/DuplicateProgressException.php

namespace App\Exceptions;

use Exception;

class DuplicateProgressException extends Exception
{
    protected $message = 'Duplicate lesson progress detected.';

    public function render($request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => false,
                'message' => $this->getMessage(),
            ], 409);
        }

        return redirect()->back()
            ->with('error', $this->getMessage())
            ->withInput();
    }
}